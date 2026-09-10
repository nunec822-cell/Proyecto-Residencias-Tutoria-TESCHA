<?php

include("../../../base_pit/conect_pit.php");

header('Content-Type: application/json');

/*=====================================================
    DIRECTIVOS - GENERAL
=====================================================*/

$sql="SELECT

        d.no_empleado,
        d.nombre,
        d.apellido_p,
        d.apellido_m

    FROM directivos d
    WHERE d.activo=1
    ORDER BY

        d.nombre,
        d.apellido_p";

$resultado=$conn->query($sql);

$datos=[];

while($fila=$resultado->fetch_assoc()){

    $datos[]=$fila;

}

echo json_encode($datos);