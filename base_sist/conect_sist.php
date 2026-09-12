<?php

/* 🔥 CONFIGURACIÓN BASE DE DATOS */

$host = "localhost";
$usuario = "root";
$password = "";
$bd = "gestor_contenidos";


/* 🔥 CONEXIÓN */

$conexion = mysqli_connect($host, $usuario, $password, $bd);


/* 🔥 VALIDAR CONEXIÓN */

if (!$conexion) {

    die("
        <h2 style='color:red; text-align:center;'>
            Error de conexión a la base de datos
        </h2>
    ");

}


/* 🔥 UTF8 PARA ACENTOS */

mysqli_set_charset($conexion, "utf8");

?>