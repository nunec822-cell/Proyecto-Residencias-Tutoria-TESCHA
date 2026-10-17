<?php
/*=========================================================
    SIST - CONEXIONES A BASE DE DATOS
    Las credenciales viven en config/database.php
=========================================================*/

function sist_db_config(): array
{
    static $config = null;

    if ($config === null) {
        $config = require SIST_ROOT . '/config/database.php';
    }

    return $config;
}

/**
 * Abre una conexión.
 * @param string $base 'pit' (PIT_V4) o 'gestor' (gestor_contenidos)
 */
function sist_conectar(string $base = 'pit'): mysqli
{
    $cfg    = sist_db_config();
    $nombre = ($base === 'gestor') ? $cfg['gestor'] : $cfg['pit'];

    try {
        $conexion = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $nombre);
    } catch (Throwable $e) {
        error_log('[SIST] Error de conexión (' . $nombre . '): ' . $e->getMessage());
        http_response_code(500);
        die('No fue posible conectar con la base de datos.');
    }

    if ($conexion->connect_error) {
        error_log('[SIST] Error de conexión (' . $nombre . '): ' . $conexion->connect_error);
        http_response_code(500);
        die('No fue posible conectar con la base de datos.');
    }

    $conexion->set_charset('utf8mb4');

    /* Se recuerda la última conexión para sist_esc() */
    $GLOBALS['sist_ultima_conexion'] = $conexion;

    return $conexion;
}
