<?php
/*=========================================================
    SIST - CANALIZACIÓN PSICOLÓGICA (flujo con avisos)

    Tablas: database/migraciones/003_canalizacion_psicologica.sql

      1. sist_psico_avisar()        el PROFESOR avisa al tutor             -> AVISO
      2. sist_psico_canalizar()     el TUTOR canaliza (o directo)          -> CANALIZADA
         (avisa a TODOS los psicólogos; ninguno está asignado todavía)
         sist_psico_descartar()     el TUTOR decide no canalizar           -> DESCARTADA
      3. sist_psico_tomar()         un PSICÓLOGO toma el caso (el primero) -> EN_ATENCION
      4. sist_psico_programar_sesion()  el psicólogo informa fecha, horario y lugar (avisa a alumno y tutor)
         sist_psico_cerrar_sesion() cómo fue la sesión (observación general + nota privada opcional)
      5. sist_psico_concluir()      el psicólogo concluye el proceso       -> CONCLUIDA

    CONFIDENCIALIDAD
      * Los AVISOS y CORREOS nunca llevan el motivo ni notas: solo quién, a quién y en qué paso.
      * sist_psico_listar() decide QUÉ COLUMNAS se leen según el perfil ('psicologo', 'tutor',
        'profesor', 'publico'). El perfil 'publico' (alumno, jefe de carrera, administrador) nunca
        selecciona motivos: no hay forma de que una pantalla los imprima por error.
      * La nota privada de cada sesión vive en otra tabla y solo se lee con
        sist_psico_notas_privadas(), que exige ser el psicólogo que la escribió.

    Cada función hace todo en una transacción, comprueba cada consulta (igual en PHP 8.0 y 8.1+)
    y devuelve ['ok' => bool, 'error' => string, ...datos]. Los correos se envían después del
    commit con sist_correos_procesar().
=========================================================*/

const SIST_PSICO_ABIERTOS = ['AVISO', 'CANALIZADA', 'EN_ATENCION'];

/** ¿Está aplicada la migración 003 (y la 002, que trae notificaciones)? */
function sist_psico_listo(mysqli $conn): bool
{
    static $listo = null;

    if ($listo === null) {
        $listo = false;
        try {
            foreach (['psico_canalizaciones', 'psico_sesiones', 'psico_notas_privadas', 'notificaciones', 'correos_pendientes'] as $t) {
                $q = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($t) . "'");
                if (!$q || $q->num_rows === 0) {
                    return $listo = false;
                }
            }
            $listo = true;
        } catch (Throwable $e) {
            $listo = false;
        }
    }

    return $listo;
}

function sist_psico_estado_texto(string $estado): string
{
    return [
        'AVISO'       => 'Aviso del profesor (el tutor debe revisar)',
        'CANALIZADA'  => 'Canalizada (esperando a que un psicólogo tome el caso)',
        'EN_ATENCION' => 'En atención',
        'CONCLUIDA'   => 'Concluida',
        'DESCARTADA'  => 'No se canalizó',
    ][$estado] ?? $estado;
}

/** Nombre del psicólogo por id_usuario */
function sist_nombre_psicologo(mysqli $conn, int $idUsuario): string
{
    $n = sist_db_valor(
        $conn,
        "SELECT " . sist_sql_nombre('ap') . " FROM administradores_psicologos ap WHERE ap.id_usuario = ?",
        'i',
        [$idUsuario]
    );

    return $n ? (string) $n : (string) (sist_db_valor($conn, "SELECT usuario FROM usuarios WHERE id_usuario = ?", 'i', [$idUsuario]) ?? '');
}

/** Psicólogos activos: [['id_usuario' => , 'nombre' => ], ...] */
function sist_psico_psicologos(mysqli $conn): array
{
    return sist_db_filas(
        $conn,
        "SELECT DISTINCT u.id_usuario, " . sist_sql_nombre('ap') . " AS nombre
         FROM usuarios u
         JOIN usuario_tipo ut ON ut.id_usuario = u.id_usuario AND ut.activo = 1
         JOIN tipos t         ON t.id_tipo = ut.id_tipo AND t.nombre = 'PSICOLOGO'
         JOIN administradores_psicologos ap ON ap.id_usuario = u.id_usuario AND ap.activo = 1
         WHERE u.activo = 1
         ORDER BY nombre"
    );
}

/** Alumnos activos de los grupos que imparte un profesor (docente_grupos) */
function sist_psico_alumnos_de_profesor(mysqli $conn, int $idProfesor): array
{
    return sist_db_filas(
        $conn,
        "SELECT DISTINCT t.id_tutorado, " . sist_sql_nombre('t') . " AS alumno, t.matricula, t.grupo, t.carrera, t.id_tutor
         FROM tutorados t
         JOIN docente_grupos dg       ON dg.grupo = t.grupo
         JOIN personal_academico pa   ON pa.id_personal = dg.id_personal AND pa.id_usuario = ?
         WHERE t.activo = 1
         ORDER BY t.grupo, alumno",
        'i',
        [$idProfesor]
    );
}

/** Tutorados activos de un tutor */
function sist_psico_alumnos_de_tutor(mysqli $conn, int $idTutor): array
{
    return sist_db_filas(
        $conn,
        "SELECT t.id_tutorado, " . sist_sql_nombre('t') . " AS alumno, t.matricula, t.grupo, t.carrera
         FROM tutorados t WHERE t.id_tutor = ? AND t.activo = 1 ORDER BY t.grupo, alumno",
        'i',
        [$idTutor]
    );
}

/** ¿Ya hay un proceso abierto para este alumno? Devuelve su folio o 0 */
function sist_psico_abierto_de(mysqli $conn, int $idTutorado): int
{
    return (int) (sist_db_valor(
        $conn,
        "SELECT id_psico FROM psico_canalizaciones
         WHERE id_tutorado = ? AND estado IN ('AVISO','CANALIZADA','EN_ATENCION') LIMIT 1",
        'i',
        [$idTutorado]
    ) ?? 0);
}

/* =========================================================
   1. EL PROFESOR AVISA AL TUTOR
========================================================= */
function sist_psico_avisar(mysqli $conn, int $idProfesor, int $idTutorado, string $motivo): array
{
    $motivo = trim($motivo);

    if ($idTutorado <= 0) {
        return ['ok' => false, 'error' => 'Selecciona al alumno.'];
    }
    if ($motivo === '' || mb_strlen($motivo) > 1500) {
        return ['ok' => false, 'error' => 'Cuéntale al tutor qué observaste (máx. 1500 caracteres).'];
    }

    try {
        $conn->begin_transaction();

        /* El alumno debe ser de un grupo que imparte este profesor */
        $al = null;
        foreach (sist_psico_alumnos_de_profesor($conn, $idProfesor) as $a) {
            if ((int) $a['id_tutorado'] === $idTutorado) {
                $al = $a;
                break;
            }
        }
        if (!$al) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese alumno no pertenece a ninguno de tus grupos.'];
        }

        $idTutor = (int) ($al['id_tutor'] ?? 0);
        if ($idTutor <= 0) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese alumno no tiene tutor asignado; avisa al administrador.'];
        }
        if ($idTutor === $idProfesor) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Tú eres el tutor de ese alumno: canalízalo directamente desde "Atención psicológica".'];
        }

        /* Bloquea la fila del alumno para que dos avisos a la vez no se dupliquen */
        sist_db_filas($conn, "SELECT id_tutorado FROM tutorados WHERE id_tutorado = ? FOR UPDATE", 'i', [$idTutorado]);
        $dup = sist_psico_abierto_de($conn, $idTutorado);
        if ($dup) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ya hay un proceso abierto para ese alumno (folio ' . $dup . '); el tutor ya fue avisado.'];
        }

        $ins = sist_db_preparar(
            $conn,
            "INSERT INTO psico_canalizaciones (id_tutorado, id_tutor, id_profesor_aviso, motivo_aviso, estado, fecha_aviso)
             VALUES (?, ?, ?, ?, 'AVISO', NOW())"
        );
        $ins->bind_param('iiis', $idTutorado, $idTutor, $idProfesor, $motivo);
        sist_db_ejecutar($ins, 'crear aviso');
        $id = (int) $conn->insert_id;
        $ins->close();

        $profesor = sist_nombre_personal($conn, $idProfesor);

        /* El aviso al tutor NO lleva el motivo: lo lee en el portal */
        sist_notificar(
            $conn, [$idTutor], 'PSICO_AVISO', 'Un profesor te avisa de un alumno',
            'Folio ' . $id . '. El profesor ' . $profesor . ' avisa que ' . $al['alumno'] . ' (' . $al['matricula']
                . ', grupo ' . $al['grupo'] . ') podría necesitar atención psicológica. Revisa el aviso y canaliza si procede.',
            'MOD_TUTOR/psicologia/index.php?id=' . $id
        );

        $conn->commit();

        return ['ok' => true, 'error' => '', 'id' => $id];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_psico_avisar: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo enviar el aviso. Intenta de nuevo.'];
    }
}

/* =========================================================
   2. EL TUTOR CANALIZA (desde un aviso, o directo a un tutorado suyo)
   $idPsico > 0  -> canaliza ese aviso;   si no, $idTutorado debe ser de este tutor
   No se elige psicólogo: se avisa a todos y el primero que toma el caso queda como responsable.
========================================================= */
function sist_psico_canalizar(
    mysqli $conn,
    int $idTutor,
    int $idPsico,
    int $idTutorado,
    string $motivo
): array {
    $motivo = trim($motivo);

    if ($motivo === '' || mb_strlen($motivo) > 1500) {
        return ['ok' => false, 'error' => 'Escribe el motivo para el psicólogo (máx. 1500 caracteres).'];
    }

    try {
        $conn->begin_transaction();

        /* Debe haber al menos un psicólogo activo a quien avisar */
        $psicologos = sist_usuarios_por_rol($conn, 'PSICOLOGO');
        if (empty($psicologos)) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'No hay psicólogos activos registrados; avisa al administrador.'];
        }

        $idProfesorAviso = 0;

        if ($idPsico > 0) {
            $f = sist_db_filas(
                $conn,
                "SELECT id_psico, id_tutorado, id_tutor, id_profesor_aviso, estado
                 FROM psico_canalizaciones WHERE id_psico = ? FOR UPDATE",
                'i',
                [$idPsico]
            );
            if (!$f || (int) $f[0]['id_tutor'] !== $idTutor) {
                $conn->rollback();
                return ['ok' => false, 'error' => 'Ese aviso no es de uno de tus tutorados.'];
            }
            if ($f[0]['estado'] !== 'AVISO') {
                $conn->rollback();
                return ['ok' => false, 'error' => 'Ese aviso ya fue atendido (estado: ' . $f[0]['estado'] . ').'];
            }
            $idTutorado      = (int) $f[0]['id_tutorado'];
            $idProfesorAviso = (int) ($f[0]['id_profesor_aviso'] ?? 0);
        }

        /* Datos del alumno; debe ser tutorado de este tutor y estar activo */
        $al = sist_db_filas(
            $conn,
            "SELECT t.id_tutorado, t.id_usuario, t.matricula, t.carrera, t.grupo, " . sist_sql_nombre('t') . " AS alumno
             FROM tutorados t WHERE t.id_tutorado = ? AND t.id_tutor = ? AND t.activo = 1 FOR UPDATE",
            'ii',
            [$idTutorado, $idTutor]
        );
        if (!$al) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese alumno no es tu tutorado o está inactivo.'];
        }
        $al = $al[0];

        if ($idPsico > 0) {
            $up = sist_db_preparar(
                $conn,
                "UPDATE psico_canalizaciones
                 SET estado = 'CANALIZADA', motivo = ?, fecha_canalizacion = NOW()
                 WHERE id_psico = ? AND estado = 'AVISO'"
            );
            $up->bind_param('si', $motivo, $idPsico);
            sist_db_ejecutar($up, 'canalizar aviso');
            if ($up->affected_rows !== 1) {
                $up->close();
                $conn->rollback();
                return ['ok' => false, 'error' => 'El aviso ya había sido atendido.'];
            }
            $up->close();
            $id = $idPsico;
        } else {
            $dup = sist_psico_abierto_de($conn, $idTutorado);
            if ($dup) {
                $conn->rollback();
                return ['ok' => false, 'error' => 'Ya hay un proceso abierto para ese alumno (folio ' . $dup . ').'];
            }
            $ins = sist_db_preparar(
                $conn,
                "INSERT INTO psico_canalizaciones (id_tutorado, id_tutor, motivo, estado, fecha_canalizacion)
                 VALUES (?, ?, ?, 'CANALIZADA', NOW())"
            );
            $ins->bind_param('iis', $idTutorado, $idTutor, $motivo);
            sist_db_ejecutar($ins, 'canalizar directo');
            $id = (int) $conn->insert_id;
            $ins->close();
        }

        if ($id <= 0) {
            throw new RuntimeException('No se obtuvo el folio.');
        }

        /* ---- Avisos (sin motivo ni datos sensibles) ---- */
        $tutor   = sist_nombre_personal($conn, $idTutor);
        $jefes   = sist_usuarios_por_rol($conn, 'JEFE_CARRERA', $al['carrera']);
        $admins  = sist_usuarios_por_rol($conn, 'ADMIN');
        $folio   = 'Folio ' . $id;
        $detalle = $al['alumno'] . ' (' . $al['matricula'] . ', ' . $al['carrera'] . ')';
        $sinJefe = empty($jefes);

        sist_notificar(
            $conn, [(int) $al['id_usuario']], 'PSICO_CANALIZADA', 'Tu tutor te canalizó a atención psicológica',
            $folio . '. Tu tutor ' . $tutor . ' te canalizó a atención psicológica. Un psicólogo tomará tu caso y te avisará '
                . 'por aquí la fecha, el horario y el lugar de tus sesiones. Aquí podrás ver el seguimiento de tu proceso.',
            'MOD_TUTORADO/psicologia/index.php'
        );
        sist_notificar(
            $conn, $psicologos, 'PSICO_CANALIZADA', 'Nueva canalización psicológica por atender',
            $folio . '. El tutor ' . $tutor . ' canalizó a ' . $detalle . '. Cualquier psicólogo puede tomar el caso: '
                . 'el primero que lo tome queda como responsable. Revisa el detalle en el portal.',
            'MOD_PSICOLOGOS/seguimiento/index.php?id=' . $id
        );
        sist_notificar(
            $conn, $admins, 'PSICO_CANALIZADA', 'Canalización psicológica registrada',
            $folio . '. El tutor ' . $tutor . ' canalizó a ' . $detalle . '. Está en espera de que un psicólogo tome el caso.'
                . ($sinJefe ? ' ATENCIÓN: no hay un jefe de carrera registrado para esta carrera.' : ''),
            'MOD_ADMIN/psicologia/index.php?id=' . $id
        );
        sist_notificar(
            $conn, $jefes, 'PSICO_CANALIZADA', 'Canalización psicológica de un alumno de tu carrera',
            $folio . '. El tutor ' . $tutor . ' canalizó a ' . $detalle . '. Podrás ver el historial de seguimiento.',
            'MOD_JEFESCARRERA/psicologia/index.php?id=' . $id
        );
        if ($idProfesorAviso > 0 && $idProfesorAviso !== $idTutor) {
            sist_notificar(
                $conn, [$idProfesorAviso], 'PSICO_CANALIZADA', 'Tu aviso fue atendido',
                $folio . '. El tutor ' . $tutor . ' canalizó a atención psicológica a ' . $al['alumno'] . ', tal como avisaste.',
                'MOD_DOCENTE/psicologia/index.php?id=' . $id
            );
        }

        $conn->commit();

        return ['ok' => true, 'error' => '', 'id' => $id, 'sin_jefe' => $sinJefe];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_psico_canalizar: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo registrar la canalización. Intenta de nuevo.'];
    }
}

/** El tutor decide no canalizar un aviso */
function sist_psico_descartar(mysqli $conn, int $idTutor, int $idPsico, string $motivo): array
{
    $motivo = trim($motivo);

    if (mb_strlen($motivo) > 300) {
        return ['ok' => false, 'error' => 'El motivo no puede pasar de 300 caracteres.'];
    }

    try {
        $conn->begin_transaction();

        $f = sist_db_filas(
            $conn,
            "SELECT c.id_psico, c.id_tutor, c.id_profesor_aviso, c.estado, " . sist_sql_nombre('t') . " AS alumno
             FROM psico_canalizaciones c JOIN tutorados t ON t.id_tutorado = c.id_tutorado
             WHERE c.id_psico = ? FOR UPDATE",
            'i',
            [$idPsico]
        );
        if (!$f || (int) $f[0]['id_tutor'] !== $idTutor) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese aviso no es de uno de tus tutorados.'];
        }
        if ($f[0]['estado'] !== 'AVISO') {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese aviso ya fue atendido.'];
        }

        $up = sist_db_preparar(
            $conn,
            "UPDATE psico_canalizaciones SET estado = 'DESCARTADA', motivo_descarte = ?, fecha_cierre = NOW()
             WHERE id_psico = ? AND estado = 'AVISO'"
        );
        $up->bind_param('si', $motivo, $idPsico);
        sist_db_ejecutar($up, 'descartar aviso');
        $up->close();

        $prof = (int) ($f[0]['id_profesor_aviso'] ?? 0);
        if ($prof > 0) {
            sist_notificar(
                $conn, [$prof], 'PSICO_DESCARTADA', 'El tutor revisó tu aviso',
                'Folio ' . $idPsico . '. El tutor revisó tu aviso sobre ' . $f[0]['alumno']
                    . ' y por ahora no lo canalizó. Si la situación cambia, puedes avisar de nuevo.',
                'MOD_DOCENTE/psicologia/index.php?id=' . $idPsico
            );
        }

        $conn->commit();

        return ['ok' => true, 'error' => ''];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_psico_descartar: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo descartar el aviso. Intenta de nuevo.'];
    }
}

/* =========================================================
   3. UN PSICÓLOGO TOMA EL CASO (el primero que lo toma queda como responsable)
========================================================= */
function sist_psico_tomar(mysqli $conn, int $idPsicologo, int $idPsico): array
{
    try {
        $conn->begin_transaction();

        if (!in_array($idPsicologo, sist_usuarios_por_rol($conn, 'PSICOLOGO'), true)) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Tu usuario no es un psicólogo activo.'];
        }

        $f = sist_db_filas(
            $conn,
            "SELECT c.id_psico, c.estado, c.id_psicologo, c.id_tutor, t.id_usuario AS id_usuario_alumno,
                    " . sist_sql_nombre('t') . " AS alumno
             FROM psico_canalizaciones c JOIN tutorados t ON t.id_tutorado = c.id_tutorado
             WHERE c.id_psico = ? FOR UPDATE",
            'i',
            [$idPsico]
        );
        if (!$f) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'La canalización no existe.'];
        }
        $c = $f[0];

        if ($c['estado'] !== 'CANALIZADA' || !empty($c['id_psicologo'])) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese caso ya fue tomado por otro psicólogo.'];
        }

        $up = sist_db_preparar(
            $conn,
            "UPDATE psico_canalizaciones
             SET estado = 'EN_ATENCION', id_psicologo = ?, fecha_toma = NOW()
             WHERE id_psico = ? AND estado = 'CANALIZADA' AND id_psicologo IS NULL"
        );
        $up->bind_param('ii', $idPsicologo, $idPsico);
        sist_db_ejecutar($up, 'tomar caso');
        if ($up->affected_rows !== 1) {
            $up->close();
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese caso ya fue tomado por otro psicólogo.'];
        }
        $up->close();

        $psicologo = sist_nombre_psicologo($conn, $idPsicologo);
        $folio     = 'Folio ' . $idPsico;

        sist_notificar(
            $conn, [(int) $c['id_usuario_alumno']], 'PSICO_TOMADA', 'Un psicólogo tomó tu caso',
            $folio . '. El psicólogo ' . $psicologo . ' tomó tu caso. Te avisará por aquí la fecha, el horario y el lugar de tus sesiones.',
            'MOD_TUTORADO/psicologia/index.php'
        );
        sist_notificar(
            $conn, [(int) $c['id_tutor']], 'PSICO_TOMADA', 'Un psicólogo tomó la canalización',
            $folio . '. El psicólogo ' . $psicologo . ' tomó el caso de ' . $c['alumno'] . '.',
            'MOD_TUTOR/psicologia/index.php?id=' . $idPsico
        );

        $conn->commit();

        return ['ok' => true, 'error' => ''];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_psico_tomar: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo tomar el caso. Intenta de nuevo.'];
    }
}

/* =========================================================
   4. EL PSICÓLOGO PROGRAMA UNA SESIÓN (fecha, horario y lugar) Y LUEGO LA CIERRA
========================================================= */
function sist_psico_programar_sesion(
    mysqli $conn,
    int $idPsicologo,
    int $idPsico,
    string $fecha,
    string $horaInicio,
    string $horaFin,
    string $lugar
): array {
    $lugar = trim($lugar);

    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$d || $d->format('Y-m-d') !== $fecha) {
        return ['ok' => false, 'error' => 'La fecha no es válida.'];
    }
    if ($fecha < date('Y-m-d')) {
        return ['ok' => false, 'error' => 'La fecha de la sesión no puede ser anterior a hoy.'];
    }
    foreach ([$horaInicio, $horaFin] as $h) {
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $h)) {
            return ['ok' => false, 'error' => 'La hora no es válida (usa formato HH:MM).'];
        }
    }
    if ($horaFin <= $horaInicio) {
        return ['ok' => false, 'error' => 'La hora de fin debe ser posterior a la de inicio.'];
    }
    if ($lugar === '' || mb_strlen($lugar) > 150) {
        return ['ok' => false, 'error' => 'Indica dónde será la sesión (máx. 150 caracteres).'];
    }

    try {
        $conn->begin_transaction();

        $f = sist_db_filas(
            $conn,
            "SELECT c.estado, c.id_psicologo, c.id_tutor, t.id_usuario AS id_usuario_alumno,
                    " . sist_sql_nombre('t') . " AS alumno
             FROM psico_canalizaciones c JOIN tutorados t ON t.id_tutorado = c.id_tutorado
             WHERE c.id_psico = ? FOR UPDATE",
            'i',
            [$idPsico]
        );
        if (!$f || (int) ($f[0]['id_psicologo'] ?? 0) !== $idPsicologo) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese caso no está asignado a ti.'];
        }
        if ($f[0]['estado'] !== 'EN_ATENCION') {
            $conn->rollback();
            return ['ok' => false, 'error' => 'El proceso ya está concluido.'];
        }
        $c = $f[0];

        /* No empalmar dos sesiones del mismo psicólogo */
        $choque = sist_db_valor(
            $conn,
            "SELECT id_sesion FROM psico_sesiones
             WHERE id_psicologo = ? AND fecha = ? AND estado = 'PROGRAMADA'
               AND hora_inicio < ? AND hora_fin > ? LIMIT 1",
            'isss',
            [$idPsicologo, $fecha, $horaFin . ':00', $horaInicio . ':00']
        );
        if ($choque) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ya tienes otra sesión programada que se empalma con ese horario.'];
        }

        $ins = sist_db_preparar(
            $conn,
            "INSERT INTO psico_sesiones (id_psico, id_psicologo, fecha, hora_inicio, hora_fin, lugar)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $ins->bind_param('iissss', $idPsico, $idPsicologo, $fecha, $horaInicio, $horaFin, $lugar);
        sist_db_ejecutar($ins, 'programar sesión');
        $ins->close();

        $psicologo = sist_nombre_psicologo($conn, $idPsicologo);
        $cuando    = sist_fecha_corta($fecha) . ' de ' . $horaInicio . ' a ' . $horaFin . ' en ' . $lugar;
        $folio     = 'Folio ' . $idPsico;

        sist_notificar(
            $conn, [(int) $c['id_usuario_alumno']], 'PSICO_SESION', 'Tienes una sesión de atención psicológica',
            $folio . '. Tu psicólogo ' . $psicologo . ' programó tu sesión: ' . $cuando . '.',
            'MOD_TUTORADO/psicologia/index.php'
        );
        sist_notificar(
            $conn, [(int) $c['id_tutor']], 'PSICO_SESION', 'Sesión psicológica programada',
            $folio . '. Sesión de ' . $c['alumno'] . ' con ' . $psicologo . ': ' . $cuando . '.',
            'MOD_TUTOR/psicologia/index.php?id=' . $idPsico
        );

        $conn->commit();

        return ['ok' => true, 'error' => ''];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_psico_programar_sesion: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo programar la sesión. Intenta de nuevo.'];
    }
}

/**
 * Cierra una sesión programada.
 * $estado: REALIZADA | NO_ASISTIO | CANCELADA.  La observación es GENERAL (la ven alumno, tutor,
 * jefe y administrador); lo clínico va en $notaPrivada (solo la lee este psicólogo).
 */
function sist_psico_cerrar_sesion(
    mysqli $conn,
    int $idPsicologo,
    int $idSesion,
    string $estado,
    string $observacion,
    string $notaPrivada
): array {
    $observacion = trim($observacion);
    $notaPrivada = trim($notaPrivada);

    if (!in_array($estado, ['REALIZADA', 'NO_ASISTIO', 'CANCELADA'], true)) {
        return ['ok' => false, 'error' => 'Elige cómo terminó la sesión.'];
    }
    if ($observacion === '' || mb_strlen($observacion) > 500) {
        return ['ok' => false, 'error' => 'Escribe la observación general (máx. 500 caracteres, sin datos sensibles).'];
    }
    if (mb_strlen($notaPrivada) > 4000) {
        return ['ok' => false, 'error' => 'La nota privada no puede pasar de 4000 caracteres.'];
    }
    if ($notaPrivada !== '' && $estado !== 'REALIZADA') {
        return ['ok' => false, 'error' => 'La nota privada solo aplica a una sesión realizada.'];
    }

    try {
        $conn->begin_transaction();

        $f = sist_db_filas(
            $conn,
            "SELECT s.id_sesion, s.id_psico, s.fecha, s.hora_inicio, s.hora_fin, s.lugar, s.estado,
                    c.id_psicologo, c.id_tutor, t.id_usuario AS id_usuario_alumno,
                    " . sist_sql_nombre('t') . " AS alumno
             FROM psico_sesiones s
             JOIN psico_canalizaciones c ON c.id_psico = s.id_psico
             JOIN tutorados t            ON t.id_tutorado = c.id_tutorado
             WHERE s.id_sesion = ? FOR UPDATE",
            'i',
            [$idSesion]
        );
        if (!$f || (int) ($f[0]['id_psicologo'] ?? 0) !== $idPsicologo) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Esa sesión no es de un caso asignado a ti.'];
        }
        $s = $f[0];

        if ($s['estado'] !== 'PROGRAMADA') {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Esa sesión ya fue cerrada.'];
        }
        if (in_array($estado, ['REALIZADA', 'NO_ASISTIO'], true) && $s['fecha'] > date('Y-m-d')) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'La sesión es de una fecha futura; si no se llevará a cabo, cancélala.'];
        }

        $up = sist_db_preparar(
            $conn,
            "UPDATE psico_sesiones SET estado = ?, observacion = ?, fecha_cierre = NOW()
             WHERE id_sesion = ? AND estado = 'PROGRAMADA'"
        );
        $up->bind_param('ssi', $estado, $observacion, $idSesion);
        sist_db_ejecutar($up, 'cerrar sesión');
        if ($up->affected_rows !== 1) {
            $up->close();
            $conn->rollback();
            return ['ok' => false, 'error' => 'Esa sesión ya fue cerrada.'];
        }
        $up->close();

        if ($notaPrivada !== '') {
            $np = sist_db_preparar($conn, "INSERT INTO psico_notas_privadas (id_sesion, nota) VALUES (?, ?)");
            $np->bind_param('is', $idSesion, $notaPrivada);
            sist_db_ejecutar($np, 'guardar nota privada');
            $np->close();
        }

        if ($estado === 'CANCELADA') {
            $cuando = sist_fecha_corta($s['fecha']) . ' de ' . sist_hora_corta($s['hora_inicio']) . ' a ' . sist_hora_corta($s['hora_fin']);
            sist_notificar(
                $conn, [(int) $s['id_usuario_alumno']], 'PSICO_SESION', 'Se canceló una sesión de atención psicológica',
                'Folio ' . $s['id_psico'] . '. Se canceló tu sesión del ' . $cuando . '. Tu psicólogo te avisará la nueva fecha.',
                'MOD_TUTORADO/psicologia/index.php'
            );
            sist_notificar(
                $conn, [(int) $s['id_tutor']], 'PSICO_SESION', 'Se canceló una sesión psicológica',
                'Folio ' . $s['id_psico'] . '. Se canceló la sesión de ' . $s['alumno'] . ' del ' . $cuando . '.',
                'MOD_TUTOR/psicologia/index.php?id=' . (int) $s['id_psico']
            );
        }

        $conn->commit();

        return ['ok' => true, 'error' => '', 'estado' => $estado, 'cancelada' => $estado === 'CANCELADA'];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_psico_cerrar_sesion: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo guardar la sesión. Intenta de nuevo.'];
    }
}

/* =========================================================
   5. EL PSICÓLOGO CONCLUYE EL PROCESO
========================================================= */
function sist_psico_concluir(mysqli $conn, int $idPsicologo, int $idPsico): array
{
    try {
        $conn->begin_transaction();

        $f = sist_db_filas(
            $conn,
            "SELECT c.estado, c.id_psicologo, c.id_tutor, t.id_usuario AS id_usuario_alumno,
                    " . sist_sql_nombre('t') . " AS alumno
             FROM psico_canalizaciones c JOIN tutorados t ON t.id_tutorado = c.id_tutorado
             WHERE c.id_psico = ? FOR UPDATE",
            'i',
            [$idPsico]
        );
        if (!$f || (int) ($f[0]['id_psicologo'] ?? 0) !== $idPsicologo) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese caso no está asignado a ti.'];
        }
        if ($f[0]['estado'] !== 'EN_ATENCION') {
            $conn->rollback();
            return ['ok' => false, 'error' => 'El proceso ya está concluido.'];
        }
        $c = $f[0];

        $pend = (int) sist_db_valor(
            $conn,
            "SELECT COUNT(*) FROM psico_sesiones WHERE id_psico = ? AND estado = 'PROGRAMADA'",
            'i',
            [$idPsico]
        );
        if ($pend > 0) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Tienes sesiones programadas sin cerrar; ciérralas o cancélalas antes de concluir.'];
        }

        $up = sist_db_preparar(
            $conn,
            "UPDATE psico_canalizaciones SET estado = 'CONCLUIDA', fecha_cierre = NOW()
             WHERE id_psico = ? AND estado = 'EN_ATENCION' AND id_psicologo = ?"
        );
        $up->bind_param('ii', $idPsico, $idPsicologo);
        sist_db_ejecutar($up, 'concluir proceso');
        if ($up->affected_rows !== 1) {
            $up->close();
            $conn->rollback();
            return ['ok' => false, 'error' => 'El proceso ya estaba concluido.'];
        }
        $up->close();

        $psicologo = sist_nombre_psicologo($conn, $idPsicologo);

        sist_notificar(
            $conn, [(int) $c['id_usuario_alumno']], 'PSICO_CONCLUIDA', 'Concluyó tu proceso de atención psicológica',
            'Folio ' . $idPsico . '. El psicólogo ' . $psicologo . ' concluyó tu proceso de atención. Si más adelante necesitas apoyo, platica con tu tutor.',
            'MOD_TUTORADO/psicologia/index.php'
        );
        sist_notificar(
            $conn, [(int) $c['id_tutor']], 'PSICO_CONCLUIDA', 'Concluyó una canalización psicológica',
            'Folio ' . $idPsico . '. El psicólogo ' . $psicologo . ' concluyó el proceso de ' . $c['alumno'] . '.',
            'MOD_TUTOR/psicologia/index.php?id=' . $idPsico
        );

        $conn->commit();

        return ['ok' => true, 'error' => ''];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_psico_concluir: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo concluir el proceso. Intenta de nuevo.'];
    }
}

/* =========================================================
   CONSULTAS (columnas según el perfil de quien mira)
========================================================= */

/**
 * @param string $perfil 'psicologo' (motivos), 'tutor' (motivos + descarte), 'profesor' (solo su motivo_aviso),
 *                       'publico' (alumno / jefe / administrador: NINGÚN dato sensible)
 * $f: id, id_tutor, id_psicologo, id_profesor_aviso, id_usuario_alumno, carrera, estado, abiertas,
 *     solo_canalizadas, sin_psicologo (casos que nadie ha tomado)
 * @return array ['total' => n, 'filas' => [...]]
 */
function sist_psico_listar(mysqli $conn, string $perfil, array $f, int $limite, int $offset): array
{
    $sensibles = [
        'psicologo' => ', c.motivo, c.motivo_aviso',
        'tutor'     => ', c.motivo, c.motivo_aviso, c.motivo_descarte',
        'profesor'  => ', c.motivo_aviso',
        'publico'   => '',
    ][$perfil] ?? '';

    $donde  = [];
    $tipos  = '';
    $params = [];

    foreach (['id' => 'c.id_psico', 'id_tutor' => 'c.id_tutor', 'id_psicologo' => 'c.id_psicologo',
              'id_profesor_aviso' => 'c.id_profesor_aviso', 'id_usuario_alumno' => 't.id_usuario'] as $k => $col) {
        if (!empty($f[$k])) {
            $donde[] = "$col = ?"; $tipos .= 'i'; $params[] = (int) $f[$k];
        }
    }
    if (!empty($f['sin_psicologo'])) {
        $donde[] = "c.id_psicologo IS NULL";
    }
    if (!empty($f['carrera'])) {
        $donde[] = 't.carrera = ?'; $tipos .= 's'; $params[] = (string) $f['carrera'];
    }
    if (!empty($f['estado']) && in_array($f['estado'], ['AVISO', 'CANALIZADA', 'EN_ATENCION', 'CONCLUIDA', 'DESCARTADA'], true)) {
        $donde[] = 'c.estado = ?'; $tipos .= 's'; $params[] = $f['estado'];
    }
    if (!empty($f['abiertas'])) {
        $donde[] = "c.estado IN ('AVISO','CANALIZADA','EN_ATENCION')";
    }
    /* Quien no es el tutor ni el profesor que avisó no ve avisos sin canalizar ni descartados */
    if (!empty($f['solo_canalizadas'])) {
        $donde[] = "c.estado IN ('CANALIZADA','EN_ATENCION','CONCLUIDA')";
    }

    $w = $donde ? ' WHERE ' . implode(' AND ', $donde) : '';

    $total = (int) sist_db_valor(
        $conn,
        "SELECT COUNT(*) FROM psico_canalizaciones c JOIN tutorados t ON t.id_tutorado = c.id_tutorado" . $w,
        $tipos,
        $params
    );

    $sql = "SELECT c.id_psico, c.estado, c.id_tutor, c.id_psicologo, c.id_profesor_aviso,
                   c.fecha_aviso, c.fecha_canalizacion, c.fecha_toma, c.fecha_cierre, c.fecha_creacion $sensibles,
                   " . sist_sql_nombre('t') . " AS alumno, t.matricula, t.carrera, t.grupo,
                   " . sist_sql_nombre('pt') . " AS tutor,
                   " . sist_sql_nombre('ap') . " AS psicologo,
                   " . sist_sql_nombre('pp') . " AS profesor_aviso
            FROM psico_canalizaciones c
            JOIN tutorados t ON t.id_tutorado = c.id_tutorado
            LEFT JOIN personal_academico pt ON pt.id_usuario = c.id_tutor
            LEFT JOIN administradores_psicologos ap ON ap.id_usuario = c.id_psicologo
            LEFT JOIN personal_academico pp ON pp.id_usuario = c.id_profesor_aviso"
         . $w . " ORDER BY FIELD(c.estado,'AVISO','CANALIZADA','EN_ATENCION','CONCLUIDA','DESCARTADA'), c.id_psico DESC
            LIMIT ? OFFSET ?";

    $filas = sist_db_filas($conn, $sql, $tipos . 'ii', array_merge($params, [$limite, $offset]));

    return ['total' => $total, 'filas' => $filas];
}

/**
 * Historial de seguimiento VISIBLE: fecha, horario, lugar, estado y observación general.
 * Lista las columnas una por una: nunca incluye notas privadas ni motivos.
 */
function sist_psico_historial(mysqli $conn, int $idPsico): array
{
    return sist_db_filas(
        $conn,
        "SELECT s.id_sesion, s.fecha, s.hora_inicio, s.hora_fin, s.lugar, s.estado, s.observacion,
                " . sist_sql_nombre('ap') . " AS psicologo
         FROM psico_sesiones s
         LEFT JOIN administradores_psicologos ap ON ap.id_usuario = s.id_psicologo
         WHERE s.id_psico = ? ORDER BY s.fecha DESC, s.hora_inicio DESC, s.id_sesion DESC",
        'i',
        [$idPsico]
    );
}

/** Notas privadas: SOLO las del psicólogo que las escribió en un caso que tiene asignado. [id_sesion => nota] */
function sist_psico_notas_privadas(mysqli $conn, int $idPsico, int $idPsicologo): array
{
    $filas = sist_db_filas(
        $conn,
        "SELECT s.id_sesion, n.nota
         FROM psico_sesiones s
         JOIN psico_notas_privadas n ON n.id_sesion = s.id_sesion
         JOIN psico_canalizaciones c ON c.id_psico = s.id_psico
         WHERE s.id_psico = ? AND s.id_psicologo = ? AND c.id_psicologo = ?",
        'iii',
        [$idPsico, $idPsicologo, $idPsicologo]
    );

    $salida = [];
    foreach ($filas as $f) {
        $salida[(int) $f['id_sesion']] = $f['nota'];
    }

    return $salida;
}
