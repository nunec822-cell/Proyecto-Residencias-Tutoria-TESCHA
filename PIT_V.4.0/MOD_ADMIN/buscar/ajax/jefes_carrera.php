<?php

include("../../../base_pit/conect_pit.php");

header('Content-Type: application/json');

$modo = $_GET['modo'] ?? '';

/*=====================================================
    JEFES DE CARRERA - GENERAL
=====================================================*/

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
            ON ut.id_usuario=u.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        INNER JOIN personal_academico pa
            ON pa.id_usuario=u.id_usuario

        WHERE
        tp.nombre='JEFE_CARRERA'
        AND pa.activo=1
        AND ut.activo=1

        ORDER BY

            pa.nombre";

    $resultado=$conn->query($sql);

    $datos=[];

    while($fila=$resultado->fetch_assoc()){

        $datos[]=$fila;

    }

    echo json_encode($datos);

    exit();

}


/*=====================================================
    JEFES DE CARRERA - POR CARRERA
=====================================================*/

if($modo=="carrera"){

    $sql="SELECT

            pa.no_empleado,

            pa.nombre,

            pa.apellido_p,

            pa.apellido_m,

            pa.carrera

        FROM usuarios u

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=u.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        INNER JOIN personal_academico pa
            ON pa.id_usuario=u.id_usuario

        WHERE
        tp.nombre='JEFE_CARRERA'
        AND pa.activo=1
        AND ut.activo=1

        ORDER BY

            pa.carrera,
            pa.nombre";

    $resultado=$conn->query($sql);

    $datos=[];

    while($fila=$resultado->fetch_assoc()){

        $datos[]=$fila;

    }

    echo json_encode($datos);

    exit();

}

echo json_encode([]);