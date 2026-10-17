<?php
/*=========================================================
    SIST - AUTENTICACIÓN Y CONTROL DE ACCESO

    Uso en cualquier archivo protegido (después del bootstrap):

        requerir_login();                      // cualquier usuario con sesión
        requerir_rol('TUTORADO');              // uno o varios roles
        requerir_permiso('usuarios.gestionar');// RECOMENDADO

    Si no se cumple: las páginas redirigen al login (o muestran
    403) y los endpoints de datos responden 401/403 en JSON.
=========================================================*/

function sist_iniciar_sesion(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

/* Responde "no autorizado" y termina. 401 = sin sesión, 403 = sin permiso */
function sist_denegar(int $codigo = 403, string $mensaje = ''): void
{
    if ($mensaje === '') {
        $mensaje = ($codigo === 401)
            ? 'Debes iniciar sesión para continuar.'
            : 'No tienes permiso para realizar esta acción.';
    }

    $datos = sist_es_peticion_ajax();

    if (!headers_sent()) {
        if ($datos) {
            sist_json(['success' => false, 'message' => $mensaje], $codigo);
        }

        if ($codigo === 401) {
            header('Location: ' . PORTAL_URL . '/login.php');
            exit;
        }

        http_response_code($codigo);
    }

    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<title>Acceso denegado</title></head>'
       . '<body style="font-family:Arial,sans-serif;text-align:center;padding:60px 20px;">'
       . '<h1>' . (int) $codigo . '</h1><p>' . e($mensaje) . '</p>'
       . '<p><a href="' . e(PORTAL_URL) . '/login.php">Ir al inicio de sesión</a></p>'
       . '</body></html>';
    exit;
}

function usuario_autenticado(): bool
{
    sist_iniciar_sesion();

    return ($_SESSION['autenticado'] ?? false) === true
        && isset($_SESSION['id_usuario']);
}

function requerir_login(): void
{
    sist_iniciar_sesion();

    if (!usuario_autenticado()) {
        sist_denegar(401);
    }

    $ahora  = time();
    $ultima = $_SESSION['ultima_actividad'] ?? $ahora;

    if (($ahora - $ultima) > SIST_SESSION_TIMEOUT) {
        sist_cerrar_sesion();
        sist_iniciar_sesion();
        $_SESSION['error_login'] = 'Tu sesión expiró por inactividad.';
        sist_denegar(401, 'Tu sesión expiró por inactividad.');
    }

    $_SESSION['ultima_actividad'] = $ahora;

    /* Contraseña asignada por el administrador: no se usa el portal hasta cambiarla */
    if (!empty($_SESSION['debe_cambiar_password'])
        && basename($_SERVER['SCRIPT_NAME'] ?? '') !== 'cambiar_password.php') {
        if (sist_es_peticion_ajax()) {
            sist_json(['success' => false, 'message' => 'Debes cambiar tu contraseña para continuar.'], 403);
        }
        header('Location: ' . PORTAL_URL . '/cambiar_password.php');
        exit;
    }
}

/** Página de inicio según el rol (el primero que coincida); null si ninguno tiene destino */
function sist_destino_por_rol(array $roles): ?string
{
    $destinos = [
        'ADMIN'        => 'MOD_ADMIN/inicioadmin.php',
        'DIRECTIVO'    => 'MOD_DIRECTIVOS/inicio.php',
        'JEFE_CARRERA' => 'MOD_JEFESCARRERA/iniciojefescarrera.php',
        'PSICOLOGO'    => 'MOD_PSICOLOGOS/inicio.php',
        'TUTORADO'     => 'MOD_TUTORADO/Inicio_tutorado.php',
        'DOCENTE'      => 'MOD_DOCENTE/iniciodocente.php',
        'TUTOR'        => 'MOD_DOCENTE/iniciodocente.php',
        'COORDINADOR'  => 'MOD_DOCENTE/iniciodocente.php',
    ];

    foreach ($destinos as $rol => $ruta) {
        if (in_array($rol, $roles, true)) {
            return $ruta;
        }
    }

    return null;
}

function sist_roles(): array
{
    sist_iniciar_sesion();

    return is_array($_SESSION['roles'] ?? null) ? $_SESSION['roles'] : [];
}

function tiene_rol(string ...$roles): bool
{
    return count(array_intersect($roles, sist_roles())) > 0;
}

function requerir_rol(string ...$roles): void
{
    requerir_login();

    if (!tiene_rol(...$roles)) {
        sist_denegar(403);
    }
}

function sist_cerrar_sesion(): void
{
    sist_iniciar_sesion();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }

    session_destroy();
}

/*---------------------------------------------------------
    Límite de intentos de login (requiere la tabla
    login_intentos de la migración 001; si no existe, se omite)
---------------------------------------------------------*/
function sist_ip_cliente(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);
}

function sist_login_bloqueado(mysqli $conn, string $usuario): bool
{
    try {
        $ip   = sist_ip_cliente();
        $stmt = $conn->prepare(
            "SELECT
                SUM(usuario = ?) AS por_usuario,
                SUM(ip = ?)      AS por_ip
             FROM login_intentos
             WHERE fecha > (NOW() - INTERVAL 15 MINUTE)"
        );
        $stmt->bind_param('ss', $usuario, $ip);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();

        return ((int) $fila['por_usuario'] >= 5) || ((int) $fila['por_ip'] >= 25);
    } catch (Throwable $e) {
        return false;
    }
}

function sist_login_registrar_fallo(mysqli $conn, string $usuario): void
{
    try {
        $ip   = sist_ip_cliente();
        $stmt = $conn->prepare("INSERT INTO login_intentos (usuario, ip, fecha) VALUES (?, ?, NOW())");
        $stmt->bind_param('ss', $usuario, $ip);
        $stmt->execute();
    } catch (Throwable $e) {
    }
}

function sist_login_limpiar(mysqli $conn, string $usuario): void
{
    try {
        $stmt = $conn->prepare("DELETE FROM login_intentos WHERE usuario = ?");
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $conn->query("DELETE FROM login_intentos WHERE fecha < (NOW() - INTERVAL 1 DAY)");
    } catch (Throwable $e) {
    }
}
