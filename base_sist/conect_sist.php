<?php
/*=========================================================
    CONEXIÓN BASE DE DATOS gestor_contenidos (sitio público)
    Las credenciales están en config/database.php
    Define $conexion (mysqli). Si ya existe, se reutiliza.
=========================================================*/

require_once __DIR__ . '/../core/bootstrap.php';

if (!isset($conexion) || !($conexion instanceof mysqli)) {
    $conexion = sist_conectar('gestor');
}
