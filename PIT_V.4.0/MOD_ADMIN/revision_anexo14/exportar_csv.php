<?php

require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
FILTROS
=========================================*/

$carrera = $_GET['carrera'] ?? '';
$grupo   = $_GET['grupo'] ?? '';

if(
    empty($carrera) ||
    empty($grupo)
){
    die(
        "Datos inválidos."
    );
}

/*=========================================
MATERIAS
=========================================*/

$materias = [];

$sql_materias = mysqli_query(
    $conn,
    "
    SELECT
        id_materia,
        nombre_materia
    FROM materias
    WHERE carrera='$carrera'
    AND estado='ACTIVA'
    ORDER BY nombre_materia
    "
);

while(
    $fila =
    mysqli_fetch_assoc(
        $sql_materias
    )
){

    $materias[] = $fila;

}

/*=========================================
ALUMNOS
=========================================*/

$sql_alumnos = mysqli_query(
    $conn,
    "
    SELECT *
    FROM tutorados
    WHERE grupo='$grupo'
    AND activo=1
    ORDER BY
        apellido_p,
        apellido_m,
        nombre
    "
);

/*=========================================
ARCHIVO
=========================================*/

$nombre_archivo =
"anexo14_" .
$grupo .
"_" .
date("Ymd_His") .
".csv";

header(
    'Content-Type: text/csv; charset=utf-8'
);

header(
    'Content-Disposition: attachment; filename="'.$nombre_archivo.'"'
);

$salida = fopen(
    'php://output',
    'w'
);

/*=========================================
ENCABEZADOS
=========================================*/

$encabezados = [

    'MATRICULA',
    'ALUMNO',
    'RIESGO'

];

foreach(
    $materias
    as
    $materia
){

    $encabezados[] =
    strtoupper(
        $materia[
            'nombre_materia'
        ]
    );

}

fputcsv(
    $salida,
    $encabezados
);

/*=========================================
REGISTROS
=========================================*/

while(
    $alumno =
    mysqli_fetch_assoc(
        $sql_alumnos
    )
){

    $id_tutorado =
    $alumno[
        'id_tutorado'
    ];

    /*=====================================
    SEMAFORO
    =====================================*/

    $sql_riesgo =
    mysqli_query(
        $conn,
        "
        SELECT
            COUNT(
                DISTINCT unidad
            ) AS total
        FROM anexo14_reportes
        WHERE id_tutorado=
        '$id_tutorado'
        "
    );

    $riesgo =
    mysqli_fetch_assoc(
        $sql_riesgo
    );

    $total =
    $riesgo[
        'total'
    ];

    if($total == 0){

        $semaforo =
        "SIN RIESGO";

    }
    elseif(
        $total == 1
    ){

        $semaforo =
        "RIESGO BAJO";

    }
    elseif(
        $total == 2
    ){

        $semaforo =
        "RIESGO MEDIO";

    }
    else{

        $semaforo =
        "RIESGO ALTO";

    }

    /*=====================================
    FILA
    =====================================*/

    $fila = [];

    $fila[] =
    $alumno[
        'matricula'
    ];

    $fila[] =
    strtoupper(

        $alumno[
            'apellido_p'
        ].' '.

        $alumno[
            'apellido_m'
        ].' '.

        $alumno[
            'nombre'
        ]

    );

    $fila[] =
    $semaforo;

    /*=====================================
    MATERIAS
    =====================================*/

    foreach(
        $materias
        as
        $materia
    ){

        $id_materia =
        $materia[
            'id_materia'
        ];

        $sql_unidades =
        mysqli_query(
            $conn,
            "
            SELECT
                DISTINCT unidad
            FROM anexo14_reportes
            WHERE id_tutorado=
            '$id_tutorado'
            AND id_materia=
            '$id_materia'
            ORDER BY unidad
            "
        );

        $unidades = [];

        while(
            $u =
            mysqli_fetch_assoc(
                $sql_unidades
            )
        ){

            $unidades[] =
            "U".$u[
                'unidad'
            ];

        }

        if(
            empty(
                $unidades
            )
        ){

            $fila[] = "-";

        }
        else{

            $fila[] =
            implode(
                ", ",
                $unidades
            );

        }

    }

    /*=====================================
    ESCRIBIR FILA
    =====================================*/

    fputcsv(
        $salida,
        $fila
    );

}

fclose(
    $salida
);

exit();

?>