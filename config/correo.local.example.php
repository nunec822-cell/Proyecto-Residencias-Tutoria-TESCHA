<?php
/*  Copia este archivo como config/correo.local.php y ajusta tus datos.
    config/correo.local.php está en .gitignore: nunca se sube al repositorio.

    Gmail:  activa la verificación en 2 pasos y crea una "contraseña de aplicación"
            (cuenta de Google > Seguridad > Contraseñas de aplicaciones). Úsala en 'clave'.
    PRUEBAS en tu equipo (smtp4dev, no envía nada a nadie): host '127.0.0.1', puerto 25 (o el
            que uses con --smtpport), usuario '', clave '', seguridad 'ninguna', de 'sist@sist.local'.
    Correo institucional: pide a sistemas el servidor SMTP, puerto y si usa tls o ssl. */
return [
    'activo'    => true,
    'host'      => 'smtp.gmail.com',
    'puerto'    => 587,
    'usuario'   => 'cuenta.del.sistema@gmail.com',
    'clave'     => 'CONTRASEÑA-DE-APLICACION',
    'seguridad' => 'tls',
    'de'        => 'cuenta.del.sistema@gmail.com',
    'nombre'    => 'SIST - Programa Institucional de Tutorías',
];
