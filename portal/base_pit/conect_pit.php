<?php
/*=========================================================
    CONEXIÓN BASE DE DATOS PIT_V4
    Las credenciales están en config/database.php
    Define $conn (mysqli). Si ya existe, se reutiliza.
=========================================================*/

require_once __DIR__ . '/../../core/bootstrap.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    $conn = sist_conectar('pit');
}
