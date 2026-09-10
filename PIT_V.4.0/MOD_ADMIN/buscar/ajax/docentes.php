<?php

include("../../../base_pit/conect_pit.php");

header('Content-Type: application/json');

$modo = $_GET['modo'] ?? 'general';

/*=========================================
    DOCENTES GENERAL
=========================================*/
if($modo=="general"){

    $sql="SELECT
            u.id_usuario,
            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m,
            pa.carrera

        FROM usuarios u

        INNER JOIN usuario_tipo ut
            ON u.id_usuario=ut.id_usuario

        INNER JOIN tipos t
            ON ut.id_tipo=t.id_tipo

        INNER JOIN personal_academico pa
            ON pa.id_usuario=u.id_usuario

        WHERE
        t.nombre='DOCENTE'
        AND pa.activo=1
        AND ut.activo=1

        ORDER BY
            pa.nombre";

}

/*=========================================
    DOCENTES POR CARRERA
=========================================*/
else{

    $sql="SELECT
            pa.carrera,
            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m

        FROM usuarios u

        INNER JOIN usuario_tipo ut
            ON u.id_usuario=ut.id_usuario

        INNER JOIN tipos t
            ON ut.id_tipo=t.id_tipo

        INNER JOIN personal_academico pa
            ON pa.id_usuario=u.id_usuario

        WHERE
        t.nombre='DOCENTE'
        AND pa.activo=1
        AND ut.activo=1

        ORDER BY
            pa.carrera,
            pa.nombre";

}

$resultado=$conn->query($sql);

$datos=[];

while($fila=$resultado->fetch_assoc()){

    $datos[]=$fila;

}

echo json_encode($datos);