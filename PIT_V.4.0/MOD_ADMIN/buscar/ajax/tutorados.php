<?php

include("../../../base_pit/conect_pit.php");

header('Content-Type: application/json');

$modo = $_GET['modo'] ?? '';

/*=====================================================
    TUTORADOS POR CARRERA
=====================================================*/
if($modo=="carrera"){

    $sql="SELECT

            t.carrera,
            t.matricula,
            t.nombre,
            t.apellido_p,
            t.apellido_m,
            t.grupo,

            pa.nombre AS tutor_nombre,
            pa.apellido_p AS tutor_apellido_p,
            pa.apellido_m AS tutor_apellido_m,
            pa.no_empleado

        FROM tutorados t

        LEFT JOIN personal_academico pa
            ON pa.id_usuario=t.id_tutor

        WHERE t.activo=1

        ORDER BY
            t.carrera,
            t.grupo,
            t.apellido_p,
            t.nombre";

    $resultado = $conn->query($sql);

    $datos = [];

    while($fila = $resultado->fetch_assoc()){

        $datos[] = $fila;

    }

    echo json_encode($datos);
    exit();

}


/*=====================================================
    TUTORADOS POR TUTOR
=====================================================*/
if($modo=="tutor"){

    $sql="SELECT

            pa.id_usuario,

            pa.no_empleado,

            pa.nombre AS tutor_nombre,

            pa.apellido_p AS tutor_apellido_p,

            pa.apellido_m AS tutor_apellido_m,

            pa.carrera,

            t.matricula,

            t.nombre,

            t.apellido_p,

            t.apellido_m,

            t.grupo

        FROM tutorados t

        INNER JOIN personal_academico pa
            ON pa.id_usuario=t.id_tutor

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=pa.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE

            tp.nombre='TUTOR'

            AND t.activo=1

        ORDER BY

            pa.nombre,

            pa.apellido_p,

            t.grupo,

            t.apellido_p,

            t.nombre";

    $resultado = $conn->query($sql);

    $datos = [];

    while($fila = $resultado->fetch_assoc()){

        $datos[] = $fila;

    }

    echo json_encode($datos);
    exit();

}


/*=====================================================
    TUTORADOS POR GRUPO
=====================================================*/

if($modo=="grupo"){

    $sql="SELECT

            t.grupo,
            t.carrera,
            t.matricula,
            t.nombre,
            t.apellido_p,
            t.apellido_m,

            pa.nombre AS tutor_nombre,
            pa.apellido_p AS tutor_apellido_p,
            pa.apellido_m AS tutor_apellido_m,
            pa.no_empleado

        FROM tutorados t

        LEFT JOIN personal_academico pa
            ON pa.id_usuario=t.id_tutor

        WHERE t.activo=1

        ORDER BY

            t.grupo,
            t.apellido_p,
            t.nombre";

    $resultado = $conn->query($sql);

    $datos = [];

    while($fila = $resultado->fetch_assoc()){

        $datos[] = $fila;

    }

    echo json_encode($datos);

    exit();

}


/*=====================================================
    SI NO EXISTE EL MODO
=====================================================*/

echo json_encode([]);