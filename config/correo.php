<?php
/*=========================================================
    SIST - CONFIGURACIÓN DE CORREO (SMTP)

    Los correos de aviso NO se envían hasta que 'activo' sea true.
    Mientras tanto quedan en la cola (tabla correos_pendientes) y se
    envían solos cuando se configure esto. Las notificaciones internas
    (campanita) funcionan siempre, con o sin correo.

    Opciones (la primera que exista gana):
      1. Variables de entorno: SIST_MAIL_ACTIVO, SIST_MAIL_HOST,
         SIST_MAIL_PORT, SIST_MAIL_USER, SIST_MAIL_PASS,
         SIST_MAIL_SEGURIDAD (tls|ssl|ninguna), SIST_MAIL_DE, SIST_MAIL_NOMBRE
      2. Archivo config/correo.local.php (no se sube a git; ver
         config/correo.local.example.php)
=========================================================*/

$config = [
    'activo'    => in_array(strtolower((string) getenv('SIST_MAIL_ACTIVO')), ['1', 'true', 'si', 'sí'], true),
    'host'      => getenv('SIST_MAIL_HOST')      ?: 'smtp.gmail.com',
    'puerto'    => (int) (getenv('SIST_MAIL_PORT') ?: 587),
    'usuario'   => getenv('SIST_MAIL_USER')      ?: '',
    'clave'     => getenv('SIST_MAIL_PASS')      !== false ? getenv('SIST_MAIL_PASS') : '',
    'seguridad' => getenv('SIST_MAIL_SEGURIDAD') ?: 'tls',     // tls (587) | ssl (465) | ninguna
    'de'        => getenv('SIST_MAIL_DE')        ?: '',        // si queda vacío se usa 'usuario'
    'nombre'    => getenv('SIST_MAIL_NOMBRE')    ?: 'SIST - Programa Institucional de Tutorías',
    'timeout'   => 8,                                          // segundos máximos por envío
    'max_intentos' => 5,
];

$local = __DIR__ . '/correo.local.php';
if (is_file($local)) {
    $config = array_merge($config, (array) require $local);
}

return $config;
