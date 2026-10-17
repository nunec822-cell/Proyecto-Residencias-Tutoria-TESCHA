<?php
/*=========================================================
    SIST - NOTIFICACIONES (campanita del portal + correo)

    sist_notificar($conn, $ids, $tipo, $titulo, $mensaje, $ruta)
        Crea la notificación interna de cada destinatario y, si tiene
        correo válido (usuarios.correo), deja el correo en la cola.
        Úsala DENTRO de la transacción del flujo: si algo falla y se
        hace rollback, tampoco quedan avisos a medias.

    sist_correos_procesar($conn)
        Envía los correos en cola. Llamar DESPUÉS del commit. Nunca
        lanza excepciones: un correo caído no cancela el trabajo.

    sist_notificaciones_no_leidas($conn, $idUsuario)  -> int (campanita)

    Requiere la migración 002 (tablas notificaciones y correos_pendientes).
=========================================================*/

/** Ejecuta un statement y lanza excepción si falla (igual en PHP 8.0 y 8.1+) */
function sist_db_ejecutar(mysqli_stmt $stmt, string $contexto = ''): void
{
    if (!$stmt->execute()) {
        throw new RuntimeException(trim($contexto . ' ' . $stmt->error));
    }
}

function sist_db_preparar(mysqli $conn, string $sql): mysqli_stmt
{
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        throw new RuntimeException('SQL: ' . $conn->error);
    }

    return $stmt;
}

/** URL absoluta a una ruta del portal (para los correos) */
function sist_url_portal_absoluta(string $ruta): string
{
    $ruta = ltrim($ruta, '/');

    return sist_site_url() . '/portal/' . $ruta;
}

/**
 * @param int[]  $idsUsuario destinatarios (se descartan repetidos e inválidos)
 * @param string $ruta       ruta relativa al portal, ej. 'MOD_TUTOR/asesorias/index.php'
 * @return int cantidad de destinatarios notificados
 */
function sist_notificar(
    mysqli $conn,
    array $idsUsuario,
    string $tipo,
    string $titulo,
    string $mensaje,
    string $ruta = ''
): int {
    $ids = array_values(array_unique(array_filter(array_map('intval', $idsUsuario), fn($i) => $i > 0)));

    if (empty($ids)) {
        return 0;
    }

    $titulo  = mb_substr($titulo, 0, 150);
    $mensaje = mb_substr($mensaje, 0, 600);
    $ruta    = mb_substr($ruta, 0, 255);

    /* Correos de los destinatarios (solo los que tengan uno válido) */
    $correos = [];
    $marcas  = implode(',', array_fill(0, count($ids), '?'));
    $stmt    = sist_db_preparar($conn, "SELECT id_usuario, correo FROM usuarios WHERE id_usuario IN ($marcas)");
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    sist_db_ejecutar($stmt, 'leer correos');
    $res = $stmt->get_result();
    while ($f = $res->fetch_assoc()) {
        if (sist_correo_valido($f['correo'] ?? '')) {
            $correos[(int) $f['id_usuario']] = $f['correo'];
        }
    }
    $stmt->close();

    [$html, $texto] = sist_correo_plantilla(
        $titulo,
        $mensaje,
        $ruta !== '' ? sist_url_portal_absoluta($ruta) : sist_url_portal_absoluta('login.php')
    );

    $insNot = sist_db_preparar(
        $conn,
        "INSERT INTO notificaciones (id_usuario, tipo, titulo, mensaje, url) VALUES (?, ?, ?, ?, ?)"
    );
    $insCor = sist_db_preparar(
        $conn,
        "INSERT INTO correos_pendientes (id_notificacion, destinatario, asunto, cuerpo_html, cuerpo_texto)
         VALUES (?, ?, ?, ?, ?)"
    );

    foreach ($ids as $idUsuario) {
        $insNot->bind_param('issss', $idUsuario, $tipo, $titulo, $mensaje, $ruta);
        sist_db_ejecutar($insNot, 'crear notificación');
        $idNot = (int) $conn->insert_id;

        if (isset($correos[$idUsuario])) {
            $asunto = '[SIST] ' . $titulo;
            $insCor->bind_param('issss', $idNot, $correos[$idUsuario], $asunto, $html, $texto);
            sist_db_ejecutar($insCor, 'encolar correo');
        }
    }

    $insNot->close();
    $insCor->close();

    return count($ids);
}

/**
 * Envía los correos en cola (después del commit).
 * @return array ['enviados' => n, 'fallidos' => n, 'pendientes' => n]
 */
function sist_correos_procesar(mysqli $conn, int $limite = 10): array
{
    $r = ['enviados' => 0, 'fallidos' => 0, 'pendientes' => 0];

    try {
        $cfg = sist_correo_config();
        $max = (int) $cfg['max_intentos'];

        if (sist_correo_activo()) {
            $inicio = time();
            $sel = sist_db_preparar(
                $conn,
                "SELECT id_correo, destinatario, asunto, cuerpo_html, cuerpo_texto, intentos
                 FROM correos_pendientes
                 WHERE estado = 'PENDIENTE' AND intentos < ?
                 ORDER BY id_correo
                 LIMIT ?"
            );
            $sel->bind_param('ii', $max, $limite);
            sist_db_ejecutar($sel, 'leer cola');
            $filas = $sel->get_result()->fetch_all(MYSQLI_ASSOC);
            $sel->close();

            foreach ($filas as $f) {
                if ((time() - $inicio) > 20) {
                    break; /* no retener la petición del usuario */
                }

                /* Reservar el correo: si otro proceso ya lo tomó, se omite */
                $id  = (int) $f['id_correo'];
                $int = (int) $f['intentos'];
                $res = sist_db_preparar(
                    $conn,
                    "UPDATE correos_pendientes SET intentos = intentos + 1
                     WHERE id_correo = ? AND estado = 'PENDIENTE' AND intentos = ?"
                );
                $res->bind_param('ii', $id, $int);
                sist_db_ejecutar($res, 'reservar correo');
                $tomado = $res->affected_rows === 1;
                $res->close();

                if (!$tomado) {
                    continue;
                }

                [$ok, $error] = sist_correo_enviar($f['destinatario'], $f['asunto'], $f['cuerpo_html'], $f['cuerpo_texto']);

                if ($ok) {
                    $up = sist_db_preparar(
                        $conn,
                        "UPDATE correos_pendientes SET estado = 'ENVIADO', error = NULL, fecha_envio = NOW() WHERE id_correo = ?"
                    );
                    $up->bind_param('i', $id);
                    $r['enviados']++;
                } else {
                    $estado = ($int + 1 >= $max) ? 'ERROR' : 'PENDIENTE';
                    $error  = mb_substr($error, 0, 250);
                    $up = sist_db_preparar(
                        $conn,
                        "UPDATE correos_pendientes SET estado = ?, error = ? WHERE id_correo = ?"
                    );
                    $up->bind_param('ssi', $estado, $error, $id);
                    $r['fallidos']++;
                    sist_log('correo ' . $id . ' no enviado: ' . $error);
                }

                sist_db_ejecutar($up, 'actualizar correo');
                $up->close();
            }
        }

        $q = $conn->query("SELECT COUNT(*) FROM correos_pendientes WHERE estado = 'PENDIENTE' AND intentos < " . (int) $max);
        if ($q) {
            $r['pendientes'] = (int) $q->fetch_row()[0];
        }
    } catch (Throwable $e) {
        sist_log('sist_correos_procesar: ' . $e->getMessage());
    }

    return $r;
}

/** Notificaciones sin leer (campanita). Devuelve 0 si la migración 002 no está aplicada. */
function sist_notificaciones_no_leidas(mysqli $conn, int $idUsuario): int
{
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM notificaciones WHERE id_usuario = ? AND leida = 0");

        if ($stmt === false) {
            return 0;
        }

        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        $n = (int) $stmt->get_result()->fetch_row()[0];
        $stmt->close();

        return $n;
    } catch (Throwable $e) {
        return 0;
    }
}
