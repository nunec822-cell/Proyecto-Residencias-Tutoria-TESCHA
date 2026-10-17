<?php
/*=========================================================
    SIST - CAMBIO DE CONTRASEÑA
    El administrador registra al usuario con una contraseña inicial; en su primer inicio de
    sesión el usuario debe cambiarla por una propia (usuarios.debe_cambiar_password = 1).

    Todo es tolerante: si la migración 004 aún no se aplicó, la marca simplemente no existe
    y el portal se comporta como antes (nada se rompe).
=========================================================*/

/** ¿Existe la columna usuarios.debe_cambiar_password? (migración 004) */
function sist_pw_listo(mysqli $conn): bool
{
    static $listo = null;

    if ($listo === null) {
        try {
            $r = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'debe_cambiar_password'");
            $listo = ($r instanceof mysqli_result) && $r->num_rows > 0;
        } catch (Throwable $e) {
            $listo = false;
        }
    }

    return $listo;
}

/** Marca (o desmarca) que el usuario debe cambiar su contraseña en su siguiente acceso */
function sist_pw_obligar(mysqli $conn, int $idUsuario, bool $obligar = true): void
{
    if (!sist_pw_listo($conn)) {
        return;
    }

    $v  = $obligar ? 1 : 0;
    $st = sist_db_preparar($conn, 'UPDATE usuarios SET debe_cambiar_password = ? WHERE id_usuario = ?');
    $st->bind_param('ii', $v, $idUsuario);
    sist_db_ejecutar($st, 'marcar cambio de contraseña');
    $st->close();
}

/** ¿Este usuario debe cambiar su contraseña? */
function sist_pw_debe_cambiar(mysqli $conn, int $idUsuario): bool
{
    if (!sist_pw_listo($conn)) {
        return false;
    }

    return (int) sist_db_valor($conn, 'SELECT debe_cambiar_password FROM usuarios WHERE id_usuario = ?', 'i', [$idUsuario]) === 1;
}

/** Reglas de la nueva contraseña. Devuelve el texto del error o '' si es válida. */
function sist_pw_validar_nueva(string $nueva, string $usuario = ''): string
{
    if (strlen($nueva) < 8) {
        return 'La contraseña nueva debe tener al menos 8 caracteres.';
    }
    if (strlen($nueva) > 72) {
        return 'La contraseña nueva no puede pasar de 72 caracteres.';
    }
    if (!preg_match('/[A-Za-z]/', $nueva) || !preg_match('/[0-9]/', $nueva)) {
        return 'La contraseña nueva debe incluir letras y números.';
    }
    if ($usuario !== '' && mb_strtoupper($nueva, 'UTF-8') === mb_strtoupper($usuario, 'UTF-8')) {
        return 'La contraseña nueva no puede ser igual a tu usuario.';
    }

    return '';
}

/**
 * El propio usuario cambia su contraseña (pide la actual). Quita la marca de "debe cambiar".
 * @return array ['ok' => bool, 'error' => string]
 */
function sist_pw_cambiar(mysqli $conn, int $idUsuario, string $actual, string $nueva, string $confirmar): array
{
    $f = sist_db_filas($conn, 'SELECT usuario, password FROM usuarios WHERE id_usuario = ? AND activo = 1', 'i', [$idUsuario]);
    if (!$f) {
        return ['ok' => false, 'error' => 'No se encontró tu cuenta.'];
    }
    $usuario = (string) $f[0]['usuario'];

    if (sist_login_bloqueado($conn, $usuario)) {
        return ['ok' => false, 'error' => 'Demasiados intentos fallidos. Espera 15 minutos e inténtalo de nuevo.'];
    }
    if (!password_verify($actual, (string) $f[0]['password'])) {
        sist_login_registrar_fallo($conn, $usuario);

        return ['ok' => false, 'error' => 'La contraseña actual no es correcta.'];
    }
    if ($nueva !== $confirmar) {
        return ['ok' => false, 'error' => 'La confirmación no coincide con la contraseña nueva.'];
    }
    if (password_verify($nueva, (string) $f[0]['password'])) {
        return ['ok' => false, 'error' => 'La contraseña nueva debe ser diferente de la actual.'];
    }
    $err = sist_pw_validar_nueva($nueva, $usuario);
    if ($err !== '') {
        return ['ok' => false, 'error' => $err];
    }

    try {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $st   = sist_db_preparar($conn, 'UPDATE usuarios SET password = ? WHERE id_usuario = ?');
        $st->bind_param('si', $hash, $idUsuario);
        sist_db_ejecutar($st, 'cambiar contraseña');
        $st->close();

        sist_pw_obligar($conn, $idUsuario, false);
        sist_login_limpiar($conn, $usuario);
    } catch (Throwable $e) {
        sist_log('sist_pw_cambiar: ' . $e->getMessage());

        return ['ok' => false, 'error' => 'No se pudo guardar la contraseña. Intenta de nuevo.'];
    }

    return ['ok' => true, 'error' => ''];
}
