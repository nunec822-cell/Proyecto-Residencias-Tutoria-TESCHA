<?php
/*=========================================================
    SIST - CONFIGURACIÓN DE BASES DE DATOS
    Las credenciales NO deben quedar escritas en el código.

    Opciones (la primera que exista gana):
      1. Variables de entorno: SIST_DB_HOST, SIST_DB_USER,
         SIST_DB_PASS, SIST_DB_PIT, SIST_DB_GESTOR
      2. Archivo config/local.php (no se sube a git)
      3. Valores por defecto de desarrollo (XAMPP)
=========================================================*/

$config = [
    'host'   => getenv('SIST_DB_HOST')   ?: 'localhost',
    'user'   => getenv('SIST_DB_USER')   ?: 'root',
    'pass'   => getenv('SIST_DB_PASS')   !== false ? getenv('SIST_DB_PASS') : '',
    'pit'    => getenv('SIST_DB_PIT')    ?: 'PIT_V4',
    'gestor' => getenv('SIST_DB_GESTOR') ?: 'gestor_contenidos',
];

$local = __DIR__ . '/local.php';
if (is_file($local)) {
    $config = array_merge($config, (array) require $local);
}

return $config;
