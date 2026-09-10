<?php

require_once("../../sesion.php");

if (!in_array("DOCENTE", $_SESSION["roles"])) {
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
DOCENTE
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

$sql_docente = mysqli_query($conn,"
    SELECT id_personal
    FROM personal_academico
    WHERE id_usuario='$id_usuario'
    LIMIT 1
");

$docente = mysqli_fetch_assoc($sql_docente);

$id_docente = $docente['id_personal'];

/*=========================================
FILTROS
=========================================*/

$grupo  = $_GET['grupo'] ?? '';
$unidad = $_GET['unidad'] ?? 'TODAS';

if(empty($grupo)){

    die("Grupo no válido.");

}

/*=========================================
FUNCIÓN
=========================================*/

function obtenerProblemas(
    $conn,
    $id_tutorado,
    $id_materia,
    $unidad
){

    $sql = mysqli_query(
        $conn,
        "
        SELECT *
        FROM anexo14_reportes
        WHERE id_tutorado='$id_tutorado'
        AND id_materia='$id_materia'
        AND unidad='$unidad'
        "
    );

    $problemas = [];

    while($fila = mysqli_fetch_assoc($sql)){

        if($fila['competencia_no_alcanzada']){
            $problemas[] = "Competencia";
        }

        if($fila['inasistencias']){
            $problemas[] = "Inasistencias";
        }

        if($fila['indisciplina']){
            $problemas[] = "Indisciplina";
        }

        if($fila['no_entrega_trabajos']){
            $problemas[] = "No entrega";
        }

        if($fila['apoyo_psicologico']){
            $problemas[] = "Psicológico";
        }

        if($fila['apoyo_economico']){
            $problemas[] = "Económico";
        }

        if(!empty($fila['otro'])){
            $problemas[] = $fila['otro'];
        }

    }

    if(empty($problemas)){

        return "-";

    }

    return implode(
        " | ",
        array_unique($problemas)
    );

}

/*=========================================
CONSULTA PRINCIPAL
=========================================*/

$sql = "

SELECT DISTINCT

    t.id_tutorado,

    t.matricula,

    CONCAT(
        t.apellido_p,' ',
        t.apellido_m,' ',
        t.nombre
    ) AS alumno,

    m.id_materia,
    m.nombre_materia

FROM anexo14_reportes ar

INNER JOIN tutorados t
    ON ar.id_tutorado=t.id_tutorado

INNER JOIN materias m
    ON ar.id_materia=m.id_materia

WHERE ar.id_docente='$id_docente'
AND ar.grupo='$grupo'

ORDER BY alumno

";

$resultado = mysqli_query($conn,$sql);

/*=========================================
NOMBRE DEL ARCHIVO
=========================================*/

$nombre_archivo =
"anexo14_" .
$grupo .
"_unidad_" .
$unidad .
".csv";

/*=========================================
ENCABEZADOS HTTP
=========================================*/

header(
    'Content-Type: text/csv; charset=utf-8'
);

header(
    'Content-Disposition: attachment; filename="' .
    $nombre_archivo .
    '"'
);

/*=========================================
SALIDA
=========================================*/

$salida = fopen(
    'php://output',
    'w'
);

/*=========================================
ENCABEZADOS CSV
=========================================*/

if($unidad == "TODAS"){

    fputcsv($salida,[

        'MATRICULA',
        'ALUMNO',
        'MATERIA',

        'U1',
        'U2',
        'U3',
        'U4',
        'U5',
        'U6'

    ]);

}else{

    fputcsv($salida,[

        'MATRICULA',
        'ALUMNO',
        'MATERIA',
        'UNIDAD_'.$unidad

    ]);

}

/*=========================================
REGISTROS
=========================================*/

while(
    $fila =
    mysqli_fetch_assoc(
        $resultado
    )
){

    if($unidad == "TODAS"){

        fputcsv($salida,[

            $fila['matricula'],

            strtoupper(
                $fila['alumno']
            ),

            strtoupper(
                $fila['nombre_materia']
            ),

            obtenerProblemas(
                $conn,
                $fila['id_tutorado'],
                $fila['id_materia'],
                1
            ),

            obtenerProblemas(
                $conn,
                $fila['id_tutorado'],
                $fila['id_materia'],
                2
            ),

            obtenerProblemas(
                $conn,
                $fila['id_tutorado'],
                $fila['id_materia'],
                3
            ),

            obtenerProblemas(
                $conn,
                $fila['id_tutorado'],
                $fila['id_materia'],
                4
            ),

            obtenerProblemas(
                $conn,
                $fila['id_tutorado'],
                $fila['id_materia'],
                5
            ),

            obtenerProblemas(
                $conn,
                $fila['id_tutorado'],
                $fila['id_materia'],
                6
            )

        ]);

    }else{

        fputcsv($salida,[

            $fila['matricula'],

            strtoupper(
                $fila['alumno']
            ),

            strtoupper(
                $fila['nombre_materia']
            ),

            obtenerProblemas(
                $conn,
                $fila['id_tutorado'],
                $fila['id_materia'],
                $unidad
            )

        ]);

    }

}

fclose($salida);
exit();

?>