<?php
/*=========================================================
    PORTAL INSTITUCIONAL DE TUTORÍAS (PIT)
    Archivo: cerrar_sesion.php
=========================================================*/

require_once __DIR__ . '/../core/bootstrap.php';

sist_cerrar_sesion();

header('Location: ' . PORTAL_URL . '/login.php');
exit();
