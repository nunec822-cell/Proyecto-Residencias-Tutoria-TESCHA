<?php
/*=========================================================
    SIST - CONFIGURACIÓN CENTRAL
    Único lugar donde se definen rutas y URL base.
    Nada en el proyecto debe escribir "/SIST V.4.0/..." a mano.
=========================================================*/

if (defined('SIST_ROOT')) {
    return;
}

/* Ruta física de la raíz del proyecto */
define('SIST_ROOT', dirname(__DIR__));

/* Tiempo máximo de inactividad de la sesión (segundos) */
if (!defined('SIST_SESSION_TIMEOUT')) {
    define('SIST_SESSION_TIMEOUT', 3600);
}

/* Tamaño máximo de archivos subidos (bytes) */
if (!defined('SIST_MAX_SUBIDA')) {
    define('SIST_MAX_SUBIDA', 10 * 1024 * 1024);
}

/*---------------------------------------------------------
    URL BASE
    Se detecta sola comparando la carpeta del proyecto con el
    DocumentRoot, así funciona aunque cambie el nombre de la
    carpeta o se instale en la raíz del servidor.
    Se puede forzar con la variable de entorno SIST_BASE_URL.
---------------------------------------------------------*/
function sist_detectar_base_url(): string
{
    $forzada = getenv('SIST_BASE_URL');
    if ($forzada !== false && $forzada !== '') {
        return '/' . trim($forzada, '/');
    }

    $docroot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $raiz    = realpath(SIST_ROOT);

    if (!$docroot || !$raiz) {
        return '';
    }

    $docroot = str_replace('\\', '/', $docroot);
    $raiz    = str_replace('\\', '/', $raiz);

    if (strpos($raiz, $docroot) !== 0) {
        return '';
    }

    $relativa = trim(substr($raiz, strlen($docroot)), '/');

    if ($relativa === '') {
        return '';
    }

    $segmentos = array_map('rawurlencode', explode('/', $relativa));

    return '/' . implode('/', $segmentos);
}

define('BASE_URL', sist_detectar_base_url());          // ej: /sist
define('PORTAL_URL', BASE_URL . '/portal');            // ej: /sist/portal

/* URL absoluta (con http/https y host), para quien la necesite */
function sist_site_url(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443);

    return ($https ? 'https://' : 'http://')
        . ($_SERVER['HTTP_HOST'] ?? 'localhost')
        . BASE_URL;
}

/* Variable global para poder usar la URL del portal dentro de
   cadenas con comillas dobles: "{$GLOBALS['sist_portal_url']}/x" */
$GLOBALS['sist_base_url']   = BASE_URL;
$GLOBALS['sist_portal_url'] = PORTAL_URL;
