<?php
/*=========================================================
    PORTAL INSTITUCIONAL DE TUTORÍAS (PIT)
    Archivo: sesion.php

    Verifica que exista una sesión válida
=========================================================*/



session_start();

if (
    !isset($_SESSION["autenticado"]) ||
    $_SESSION["autenticado"] !== true
) {

    header("Location: /SIST%20V.4.0/PIT_V.4.0/indexloguin.php");
    exit();
}

if (!isset($_SESSION["id_usuario"])) {

    session_destroy();

    header("Location: /SIST%20V.4.0/PIT_V.4.0/indexloguin.php");
    exit();
}