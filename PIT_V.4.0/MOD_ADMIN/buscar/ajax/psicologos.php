<?php

include("../../../base_pit/conect_pit.php");

header('Content-Type: application/json');

/*=====================================================
    PSICÓLOGOS
=====================================================*/

$sql="SELECT

        ap.no_empleado,
        ap.nombre,
        ap.apellido_p,
        ap.apellido_m,
        ap.correo_institucional

    FROM administradores_psicologos ap

    INNER JOIN usuario_tipo ut
        ON ut.id_usuario = ap.id_usuario

    INNER JOIN tipos tp
        ON tp.id_tipo = ut.id_tipo

    WHERE
    tp.nombre='PSICOLOGO'
    AND ap.activo=1
    AND ut.activo=1

    ORDER BY

        ap.nombre,
        ap.apellido_p";

$resultado=$conn->query($sql);

$datos=[];

while($fila=$resultado->fetch_assoc()){

    $datos[]=$fila;

}

echo json_encode($datos);

exit();

?>