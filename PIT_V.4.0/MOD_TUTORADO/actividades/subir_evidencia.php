<?php

require_once("../../sesion.php");

if(
    !in_array(
        "TUTORADO",
        $_SESSION["roles"]
    )
){
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
MOSTRAR ERRORES (TEMPORAL)
=========================================*/

error_reporting(E_ALL);
ini_set("display_errors",1);

/*=========================================
OBTENER TUTORADO
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

$sqlTutorado = mysqli_query(
    $conn,
    "
    SELECT
        id_tutorado
    FROM tutorados
    WHERE id_usuario='$id_usuario'
    LIMIT 1
    "
);

if(mysqli_num_rows($sqlTutorado)==0){

    die("No se encontró el tutorado.");

}

$tutorado = mysqli_fetch_assoc($sqlTutorado);

$id_tutorado = $tutorado['id_tutorado'];

/*=========================================
DATOS
=========================================*/

$id_actividad = intval(
    $_POST['id_actividad'] ?? 0
);

if($id_actividad<=0){

    die("Actividad inválida.");

}

/*=========================================
VALIDAR ARCHIVO
=========================================*/

if(!isset($_FILES['pdf'])){

    die("SIN_ARCHIVO");

}

$pdf = $_FILES['pdf'];

if($pdf['error']!=0){

    die("ERROR_ARCHIVO");

}

/*=========================================
EXTENSIÓN
=========================================*/

$extension = strtolower(
    pathinfo(
        $pdf['name'],
        PATHINFO_EXTENSION
    )
);

if($extension!="pdf"){

    die("FORMATO");

}

/*=========================================
TAMAÑO
=========================================*/

if(
    $pdf['size'] >
    (2*1024*1024)
){

    die("PESO");

}

/*=========================================
CARPETA
=========================================*/

$carpeta = "uploads/evidencias/";

if(!is_dir($carpeta)){

    mkdir(
        $carpeta,
        0777,
        true
    );

}

/*=========================================
NOMBRE PDF
=========================================*/

$nombrePdf =
uniqid().
"_".
time().
".pdf";

/*=========================================
BUSCAR EVIDENCIA EXISTENTE
=========================================*/

$sqlExiste = mysqli_query(
    $conn,
    "
    SELECT
        *
    FROM evidencias_actividades
    WHERE
        id_actividad='$id_actividad'
    AND
        id_tutorado='$id_tutorado'
    LIMIT 1
    "
);

if(mysqli_num_rows($sqlExiste)>0){

    $anterior =
    mysqli_fetch_assoc(
        $sqlExiste
    );

    if(

        !empty($anterior['pdf'])

        &&

        file_exists(
            $carpeta.$anterior['pdf']
        )

    ){

        unlink(
            $carpeta.$anterior['pdf']
        );

    }

    if(
        !move_uploaded_file(
            $pdf['tmp_name'],
            $carpeta.$nombrePdf
        )
    ){

        die("No fue posible guardar el PDF.");

    }

    $id_evidencia =
    intval(
        $anterior['id_evidencia']
    );

    $actualizar =
    mysqli_query(
        $conn,
        "
        UPDATE evidencias_actividades
        SET

            pdf='$nombrePdf',

            fecha_entrega=NOW()

        WHERE

            id_evidencia=$id_evidencia
        "
    );

    if(!$actualizar){

        die(mysqli_error($conn));

    }

}
else{

    if(
        !move_uploaded_file(
            $pdf['tmp_name'],
            $carpeta.$nombrePdf
        )
    ){

        die("No fue posible guardar el PDF.");

    }

    $insertar =
    mysqli_query(
        $conn,
        "
        INSERT INTO
        evidencias_actividades(

            id_actividad,
            id_tutorado,
            pdf,
            fecha_entrega

        )

        VALUES(

            '$id_actividad',
            '$id_tutorado',
            '$nombrePdf',
            NOW()

        )
        "
    );

    if(!$insertar){

        die(mysqli_error($conn));

    }

}

echo "OK";

?>