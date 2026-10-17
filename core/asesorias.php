<?php
/*=========================================================
    SIST - CANALIZACIÓN DE ASESORÍAS ACADÉMICAS

    Flujo (tablas en database/migraciones/002_asesorias_notificaciones.sql):
      1. sist_asesoria_crear()      el TUTOR canaliza a un alumno suyo   -> PENDIENTE
         avisa a: jefe(s) de carrera de la carrera del alumno, alumno, administradores
      2. sist_asesoria_asignar()    el JEFE DE CARRERA asigna profesor,
         fecha, hora y lugar                                              -> ASIGNADA
         avisa a: alumno, administradores, tutor y profesor asignado
      3. sist_asesoria_seguimiento() el PROFESOR registra notas           -> EN_SEGUIMIENTO / FINALIZADA

    Cada función hace TODO en una transacción, comprueba cada consulta
    (funciona igual en PHP 8.0 y 8.1+) y devuelve:
        ['ok' => bool, 'error' => string, ...datos]
    Los correos se envían después del commit (la pantalla llama a
    sist_correos_procesar()); si el correo falla, la asesoría ya quedó guardada.
=========================================================*/

const SIST_ASESORIA_ESTADOS_ABIERTOS = ['PENDIENTE', 'ASIGNADA', 'EN_SEGUIMIENTO'];

/** ¿Está aplicada la migración 002? */
function sist_asesorias_listo(mysqli $conn): bool
{
    static $listo = null;

    if ($listo === null) {
        $listo = false;
        try {
            foreach (['asesorias_canalizaciones', 'asesorias_seguimiento', 'notificaciones', 'correos_pendientes'] as $t) {
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

/**
 * Normaliza el nombre de una carrera para compararla entre tablas:
 * 'Ingeniería Electromecánica' y 'INGENIERIA EN ELECTROMECANICA' dan lo mismo.
 */
function sist_carrera_norm(string $carrera): string
{
    $c = mb_strtoupper(trim($carrera), 'UTF-8');
    $c = strtr($c, ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N']);
    $c = preg_replace('/\s+/', ' ', $c);

    return trim(str_replace(' EN ', ' ', $c));
}

function sist_fecha_corta(?string $fecha): string
{
    if (!$fecha) {
        return '';
    }
    $t = strtotime($fecha);

    return $t ? date('d/m/Y', $t) : '';
}

function sist_hora_corta(?string $hora): string
{
    return $hora ? substr($hora, 0, 5) : '';
}

/** Expresión SQL "Nombre ApellidoP ApellidoM" */
function sist_sql_nombre(string $alias): string
{
    return "CONCAT_WS(' ', NULLIF(TRIM($alias.nombre), ''), NULLIF(TRIM($alias.apellido_p), ''), NULLIF(TRIM($alias.apellido_m), ''))";
}

/** Un valor (o null) de una consulta preparada */
function sist_db_valor(mysqli $conn, string $sql, string $tipos = '', array $params = [])
{
    $stmt = sist_db_preparar($conn, $sql);
    if ($tipos !== '') {
        $stmt->bind_param($tipos, ...$params);
    }
    sist_db_ejecutar($stmt, 'consulta');
    $f = $stmt->get_result()->fetch_row();
    $stmt->close();

    return $f ? $f[0] : null;
}

/** Filas de una consulta preparada */
function sist_db_filas(mysqli $conn, string $sql, string $tipos = '', array $params = []): array
{
    $stmt = sist_db_preparar($conn, $sql);
    if ($tipos !== '') {
        $stmt->bind_param($tipos, ...$params);
    }
    sist_db_ejecutar($stmt, 'consulta');
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $filas;
}

/**
 * ids de usuario con cierto rol (tipos.nombre), activos.
 * Con $carrera: solo quienes son personal académico de esa carrera.
 * @return int[]
 */
function sist_usuarios_por_rol(mysqli $conn, string $rol, ?string $carrera = null): array
{
    $sql = "SELECT DISTINCT ut.id_usuario
            FROM usuario_tipo ut
            JOIN tipos t    ON t.id_tipo = ut.id_tipo
            JOIN usuarios u ON u.id_usuario = ut.id_usuario
            WHERE t.nombre = ? AND ut.activo = 1 AND u.activo = 1";
    $tipos  = 's';
    $params = [$rol];

    if ($carrera !== null) {
        $sql .= " AND EXISTS (SELECT 1 FROM personal_academico pa
                              WHERE pa.id_usuario = ut.id_usuario AND pa.activo = 1 AND pa.carrera = ?)";
        $tipos  .= 's';
        $params[] = $carrera;
    }

    return array_map(fn($f) => (int) $f['id_usuario'], sist_db_filas($conn, $sql, $tipos, $params));
}

/** Nombre del personal académico por id_usuario (o el usuario de acceso) */
function sist_nombre_personal(mysqli $conn, int $idUsuario): string
{
    $n = sist_db_valor(
        $conn,
        "SELECT " . sist_sql_nombre('pa') . " FROM personal_academico pa WHERE pa.id_usuario = ?",
        'i',
        [$idUsuario]
    );

    if ($n) {
        return (string) $n;
    }

    return (string) (sist_db_valor($conn, "SELECT usuario FROM usuarios WHERE id_usuario = ?", 'i', [$idUsuario]) ?? '');
}

/** Consulta base de asesorías con los nombres ya resueltos */
function sist_asesorias_sql_base(): string
{
    return "SELECT a.*,
                   " . sist_sql_nombre('t') . "  AS alumno,
                   t.matricula, t.carrera, t.grupo, t.id_usuario AS id_usuario_alumno,
                   m.nombre_materia,
                   " . sist_sql_nombre('pt') . " AS tutor,
                   " . sist_sql_nombre('pp') . " AS profesor
            FROM asesorias_canalizaciones a
            JOIN tutorados t  ON t.id_tutorado = a.id_tutorado
            JOIN materias m   ON m.id_materia  = a.id_materia
            LEFT JOIN personal_academico pt ON pt.id_usuario = a.id_tutor
            LEFT JOIN personal_academico pp ON pp.id_usuario = a.id_profesor";
}

/**
 * Lista asesorías con filtros opcionales.
 * $f: id, id_tutor, id_profesor, carrera, estado ('PENDIENTE'…), abiertas (bool)
 * @return array ['total' => n, 'filas' => [...]]
 */
function sist_asesorias_listar(mysqli $conn, array $f, int $limite, int $offset): array
{
    $donde  = [];
    $tipos  = '';
    $params = [];

    if (!empty($f['id'])) {
        $donde[] = 'a.id_asesoria = ?'; $tipos .= 'i'; $params[] = (int) $f['id'];
    }
    if (!empty($f['id_tutor'])) {
        $donde[] = 'a.id_tutor = ?'; $tipos .= 'i'; $params[] = (int) $f['id_tutor'];
    }
    if (!empty($f['id_profesor'])) {
        $donde[] = 'a.id_profesor = ?'; $tipos .= 'i'; $params[] = (int) $f['id_profesor'];
    }
    if (!empty($f['carrera'])) {
        $donde[] = 't.carrera = ?'; $tipos .= 's'; $params[] = (string) $f['carrera'];
    }
    if (!empty($f['estado']) && in_array($f['estado'], ['PENDIENTE', 'ASIGNADA', 'EN_SEGUIMIENTO', 'FINALIZADA'], true)) {
        $donde[] = 'a.estado = ?'; $tipos .= 's'; $params[] = $f['estado'];
    }
    if (!empty($f['abiertas'])) {
        $donde[] = "a.estado <> 'FINALIZADA'";
    }

    $w = $donde ? ' WHERE ' . implode(' AND ', $donde) : '';

    $total = (int) sist_db_valor(
        $conn,
        "SELECT COUNT(*) FROM asesorias_canalizaciones a JOIN tutorados t ON t.id_tutorado = a.id_tutorado" . $w,
        $tipos,
        $params
    );

    $tipos2  = $tipos . 'ii';
    $params2 = array_merge($params, [$limite, $offset]);
    $filas   = sist_db_filas(
        $conn,
        sist_asesorias_sql_base() . $w . " ORDER BY FIELD(a.estado,'PENDIENTE','ASIGNADA','EN_SEGUIMIENTO','FINALIZADA'), a.id_asesoria DESC LIMIT ? OFFSET ?",
        $tipos2,
        $params2
    );

    return ['total' => $total, 'filas' => $filas];
}

function sist_asesoria_notas(mysqli $conn, int $idAsesoria): array
{
    return sist_db_filas(
        $conn,
        "SELECT s.nota, s.estado, s.fecha, " . sist_sql_nombre('pa') . " AS autor
         FROM asesorias_seguimiento s
         LEFT JOIN personal_academico pa ON pa.id_usuario = s.id_usuario
         WHERE s.id_asesoria = ? ORDER BY s.id_seguimiento DESC",
        'i',
        [$idAsesoria]
    );
}

/** Materias activas de la carrera del alumno (compara normalizando acentos y "EN") */
function sist_materias_de_carrera(mysqli $conn, string $carreraAlumno): array
{
    $norm  = sist_carrera_norm($carreraAlumno);
    $salida = [];

    foreach (sist_db_filas($conn, "SELECT id_materia, nombre_materia, carrera FROM materias WHERE estado = 'ACTIVA' ORDER BY nombre_materia") as $m) {
        if (sist_carrera_norm($m['carrera']) === $norm) {
            $salida[] = $m;
        }
    }

    return $salida;
}

/* =========================================================
   1. EL TUTOR CANALIZA
========================================================= */
function sist_asesoria_crear(mysqli $conn, int $idTutor, int $idTutorado, int $idMateria, string $motivo): array
{
    $motivo = trim($motivo);

    if ($idTutorado <= 0 || $idMateria <= 0) {
        return ['ok' => false, 'error' => 'Selecciona al alumno y la materia.'];
    }
    if (mb_strlen($motivo) > 1000) {
        return ['ok' => false, 'error' => 'El motivo no puede pasar de 1000 caracteres.'];
    }

    try {
        $conn->begin_transaction();

        /* El alumno debe ser tutorado de ESTE tutor y estar activo */
        $al = sist_db_filas(
            $conn,
            "SELECT t.id_tutorado, t.id_usuario, t.matricula, t.carrera, " . sist_sql_nombre('t') . " AS alumno
             FROM tutorados t WHERE t.id_tutorado = ? AND t.id_tutor = ? AND t.activo = 1 FOR UPDATE",
            'ii',
            [$idTutorado, $idTutor]
        );
        if (!$al) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ese alumno no es tu tutorado o está inactivo.'];
        }
        $al = $al[0];

        $mat = sist_db_filas(
            $conn,
            "SELECT nombre_materia, carrera FROM materias WHERE id_materia = ? AND estado = 'ACTIVA'",
            'i',
            [$idMateria]
        );
        if (!$mat || sist_carrera_norm($mat[0]['carrera']) !== sist_carrera_norm($al['carrera'])) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'La materia no existe, está inactiva o no es de la carrera del alumno.'];
        }
        $materia = $mat[0]['nombre_materia'];

        /* Evitar duplicados abiertos del mismo alumno y materia */
        $dup = sist_db_valor(
            $conn,
            "SELECT id_asesoria FROM asesorias_canalizaciones
             WHERE id_tutorado = ? AND id_materia = ? AND estado IN ('PENDIENTE','ASIGNADA','EN_SEGUIMIENTO') LIMIT 1",
            'ii',
            [$idTutorado, $idMateria]
        );
        if ($dup) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Ya existe una asesoría abierta de ese alumno en esa materia (folio ' . (int) $dup . ').'];
        }

        $ins = sist_db_preparar(
            $conn,
            "INSERT INTO asesorias_canalizaciones (id_tutorado, id_tutor, id_materia, motivo, estado)
             VALUES (?, ?, ?, ?, 'PENDIENTE')"
        );
        $ins->bind_param('iiis', $idTutorado, $idTutor, $idMateria, $motivo);
        sist_db_ejecutar($ins, 'crear asesoría');
        $idAsesoria = (int) $conn->insert_id;
        $ins->close();

        if ($idAsesoria <= 0) {
            throw new RuntimeException('No se obtuvo el folio de la asesoría.');
        }

        /* ---- Avisos ---- */
        $tutor   = sist_nombre_personal($conn, $idTutor);
        $jefes   = sist_usuarios_por_rol($conn, 'JEFE_CARRERA', $al['carrera']);
        $admins  = sist_usuarios_por_rol($conn, 'ADMIN');
        $aviso   = [];
        $folio   = 'Folio ' . $idAsesoria;
        $detalle = $al['alumno'] . ' (' . $al['matricula'] . ', ' . $al['carrera'] . ') · materia: ' . $materia;

        $sinJefe = empty($jefes);
        $notaJefe = $sinJefe ? ' ATENCIÓN: no hay un jefe de carrera registrado para esta carrera.' : '';

        $aviso['jefes'] = sist_notificar(
            $conn, $jefes, 'ASESORIA_NUEVA', 'Nueva canalización a asesoría',
            $folio . '. El tutor ' . $tutor . ' canalizó a asesoría a ' . $detalle . '. Falta asignar profesor, fecha y hora.',
            'MOD_JEFESCARRERA/asesorias/index.php?id=' . $idAsesoria
        );
        $aviso['admins'] = sist_notificar(
            $conn, $admins, 'ASESORIA_NUEVA', 'Nueva canalización a asesoría',
            $folio . '. El tutor ' . $tutor . ' canalizó a asesoría a ' . $detalle . '.' . $notaJefe,
            'MOD_ADMIN/asesorias/index.php?id=' . $idAsesoria
        );
        $aviso['alumno'] = sist_notificar(
            $conn, [(int) $al['id_usuario']], 'ASESORIA_NUEVA', 'Tu tutor te canalizó a asesoría',
            $folio . '. Tu tutor ' . $tutor . ' te canalizó a asesoría de ' . $materia
                . '. El jefe de carrera te asignará profesor, fecha y hora; te avisaremos en cuanto esté listo.',
            'notificaciones/index.php'
        );

        $conn->commit();

        return ['ok' => true, 'error' => '', 'id' => $idAsesoria, 'avisos' => $aviso, 'sin_jefe' => $sinJefe];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_asesoria_crear: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo registrar la canalización. Intenta de nuevo.'];
    }
}

/* =========================================================
   2. EL JEFE DE CARRERA ASIGNA
   $carreraJefe: carrera del jefe (restringe a su carrera); null = sin restricción (administrador)
========================================================= */
function sist_asesoria_asignar(
    mysqli $conn,
    int $idAsesoria,
    int $idJefe,
    ?string $carreraJefe,
    int $idProfesor,
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
        return ['ok' => false, 'error' => 'La fecha de la asesoría no puede ser anterior a hoy.'];
    }
    foreach ([$horaInicio, $horaFin] as $h) {
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $h)) {
            return ['ok' => false, 'error' => 'La hora no es válida (usa formato HH:MM).'];
        }
    }
    if ($horaFin <= $horaInicio) {
        return ['ok' => false, 'error' => 'La hora de fin debe ser posterior a la de inicio.'];
    }
    if (mb_strlen($lugar) > 150) {
        return ['ok' => false, 'error' => 'El lugar no puede pasar de 150 caracteres.'];
    }
    if ($idProfesor <= 0) {
        return ['ok' => false, 'error' => 'Selecciona al profesor que dará la asesoría.'];
    }

    try {
        $conn->begin_transaction();

        $as = sist_db_filas(
            $conn,
            "SELECT a.id_asesoria, a.estado, a.id_tutor, a.id_materia, m.nombre_materia,
                    t.id_usuario AS id_usuario_alumno, t.carrera, t.matricula,
                    " . sist_sql_nombre('t') . " AS alumno
             FROM asesorias_canalizaciones a
             JOIN tutorados t ON t.id_tutorado = a.id_tutorado
             JOIN materias m  ON m.id_materia = a.id_materia
             WHERE a.id_asesoria = ? FOR UPDATE",
            'i',
            [$idAsesoria]
        );
        if (!$as) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'La asesoría no existe.'];
        }
        $as = $as[0];

        if ($carreraJefe !== null && sist_carrera_norm($carreraJefe) !== sist_carrera_norm($as['carrera'])) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Esa asesoría no es de tu carrera.'];
        }
        if ($as['estado'] !== 'PENDIENTE') {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Esa asesoría ya fue atendida (estado: ' . $as['estado'] . ').'];
        }

        /* El profesor debe ser docente activo */
        $prof = sist_db_valor(
            $conn,
            "SELECT " . sist_sql_nombre('pa') . "
             FROM personal_academico pa
             JOIN usuarios u       ON u.id_usuario = pa.id_usuario AND u.activo = 1
             JOIN usuario_tipo ut  ON ut.id_usuario = pa.id_usuario AND ut.activo = 1
             JOIN tipos t          ON t.id_tipo = ut.id_tipo AND t.nombre = 'DOCENTE'
             WHERE pa.id_usuario = ? AND pa.activo = 1 LIMIT 1",
            'i',
            [$idProfesor]
        );
        if (!$prof) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'El profesor seleccionado no es un docente activo.'];
        }

        $up = sist_db_preparar(
            $conn,
            "UPDATE asesorias_canalizaciones
             SET estado = 'ASIGNADA', id_jefe = ?, id_profesor = ?, fecha_asesoria = ?,
                 hora_inicio = ?, hora_fin = ?, lugar = ?, fecha_asignacion = NOW()
             WHERE id_asesoria = ? AND estado = 'PENDIENTE'"
        );
        $up->bind_param('iissssi', $idJefe, $idProfesor, $fecha, $horaInicio, $horaFin, $lugar, $idAsesoria);
        sist_db_ejecutar($up, 'asignar asesoría');
        if ($up->affected_rows !== 1) {
            $up->close();
            $conn->rollback();
            return ['ok' => false, 'error' => 'La asesoría ya había sido asignada.'];
        }
        $up->close();

        /* ---- Avisos ---- */
        $cuando  = sist_fecha_corta($fecha) . ' de ' . $horaInicio . ' a ' . $horaFin . ($lugar !== '' ? ' en ' . $lugar : '');
        $folio   = 'Folio ' . $idAsesoria;
        $aviso   = [];

        $aviso['alumno'] = sist_notificar(
            $conn, [(int) $as['id_usuario_alumno']], 'ASESORIA_ASIGNADA', 'Tu asesoría ya tiene profesor y horario',
            $folio . '. Asesoría de ' . $as['nombre_materia'] . ' con ' . $prof . ': ' . $cuando . '.',
            'notificaciones/index.php'
        );
        $aviso['admins'] = sist_notificar(
            $conn, sist_usuarios_por_rol($conn, 'ADMIN'), 'ASESORIA_ASIGNADA', 'Asesoría asignada',
            $folio . '. ' . $as['alumno'] . ' (' . $as['matricula'] . ') · ' . $as['nombre_materia']
                . ' · profesor: ' . $prof . ' · ' . $cuando . '.',
            'MOD_ADMIN/asesorias/index.php?id=' . $idAsesoria
        );
        $aviso['tutor'] = sist_notificar(
            $conn, [(int) $as['id_tutor']], 'ASESORIA_ASIGNADA', 'Tu canalización a asesoría fue atendida',
            $folio . '. ' . $as['alumno'] . ' · ' . $as['nombre_materia'] . ' · profesor: ' . $prof . ' · ' . $cuando . '.',
            'MOD_TUTOR/asesorias/index.php?id=' . $idAsesoria
        );
        $aviso['profesor'] = sist_notificar(
            $conn, [$idProfesor], 'ASESORIA_PROFESOR', 'Se te asignó una asesoría',
            $folio . '. Impartirás asesoría de ' . $as['nombre_materia'] . ' a ' . $as['alumno'] . ' (' . $as['matricula'] . '): ' . $cuando . '.',
            'MOD_DOCENTE/asesorias/index.php?id=' . $idAsesoria
        );

        $conn->commit();

        return ['ok' => true, 'error' => '', 'avisos' => $aviso];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_asesoria_asignar: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo guardar la asignación. Intenta de nuevo.'];
    }
}

/* =========================================================
   3. EL PROFESOR DA SEGUIMIENTO
========================================================= */
function sist_asesoria_seguimiento(mysqli $conn, int $idAsesoria, int $idProfesor, string $nota, bool $finalizar): array
{
    $nota = trim($nota);

    if ($nota === '' || mb_strlen($nota) > 2000) {
        return ['ok' => false, 'error' => 'Escribe la nota de seguimiento (máx. 2000 caracteres).'];
    }

    try {
        $conn->begin_transaction();

        $as = sist_db_filas(
            $conn,
            "SELECT estado, id_profesor FROM asesorias_canalizaciones WHERE id_asesoria = ? FOR UPDATE",
            'i',
            [$idAsesoria]
        );
        if (!$as || (int) $as[0]['id_profesor'] !== $idProfesor) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'Esa asesoría no está asignada a ti.'];
        }
        if (!in_array($as[0]['estado'], ['ASIGNADA', 'EN_SEGUIMIENTO'], true)) {
            $conn->rollback();
            return ['ok' => false, 'error' => 'La asesoría ya está finalizada.'];
        }

        $estado = $finalizar ? 'FINALIZADA' : 'EN_SEGUIMIENTO';

        $ins = sist_db_preparar($conn, "INSERT INTO asesorias_seguimiento (id_asesoria, id_usuario, nota, estado) VALUES (?, ?, ?, ?)");
        $ins->bind_param('iiss', $idAsesoria, $idProfesor, $nota, $estado);
        sist_db_ejecutar($ins, 'guardar seguimiento');
        $ins->close();

        $up = sist_db_preparar(
            $conn,
            "UPDATE asesorias_canalizaciones
             SET estado = ?, fecha_cierre = IF(? = 'FINALIZADA', NOW(), NULL)
             WHERE id_asesoria = ?"
        );
        $up->bind_param('ssi', $estado, $estado, $idAsesoria);
        sist_db_ejecutar($up, 'actualizar estado');
        $up->close();

        $conn->commit();

        return ['ok' => true, 'error' => '', 'estado' => $estado];
    } catch (Throwable $e) {
        try { $conn->rollback(); } catch (Throwable $e2) {}
        sist_log('sist_asesoria_seguimiento: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo guardar el seguimiento. Intenta de nuevo.'];
    }
}
