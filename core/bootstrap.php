<?php
/*=========================================================
    SIST - BOOTSTRAP
    Se incluye UNA vez al inicio de cada archivo PHP:

        require_once __DIR__ . '/../../core/bootstrap.php';

    Carga configuración, base de datos, autenticación y
    permisos. No imprime nada ni inicia la sesión por sí solo.
=========================================================*/

if (defined('SIST_BOOTSTRAP')) {
    return;
}
define('SIST_BOOTSTRAP', true);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/password.php';
require_once __DIR__ . '/permisos.php';
require_once __DIR__ . '/correo.php';
require_once __DIR__ . '/notificaciones.php';
require_once __DIR__ . '/asesorias.php';
require_once __DIR__ . '/psicologia.php';

date_default_timezone_set('America/Mexico_City');
