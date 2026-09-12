<?php
/*=========================================
  CONEXIÓN BASE DE DATOS PIT_V4
=========================================*/

$host = "localhost";
$usuario = "root";
$password = "";
$bd = "PIT_V4";

/*=========================================
  CREAR CONEXIÓN
=========================================*/
$conn = new mysqli(
    $host,
    $usuario,
    $password,
    $bd
);

/*=========================================
  VALIDAR CONEXIÓN
=========================================*/
if ($conn->connect_error) {
    die(
        "Error de conexión a la base de datos: "
        . $conn->connect_error
    );
}

/*=========================================
  CONFIGURAR UTF8
=========================================*/
$conn->set_charset("utf8mb4");

/*=========================================
  FECHA LOCAL
=========================================*/
date_default_timezone_set('America/Mexico_City');
?>