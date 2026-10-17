<?php
/*=========================================================
    PORTAL INSTITUCIONAL DE TUTORÍAS (PIT)
    Archivo: sesion.php

    Verifica que exista una sesión válida (login + inactividad).
    Para verificar ADEMÁS un rol o permiso usa, a continuación:
        requerir_rol('TUTORADO');
        requerir_permiso('clave.permiso');
=========================================================*/

require_once __DIR__ . '/../core/bootstrap.php';

requerir_login();
