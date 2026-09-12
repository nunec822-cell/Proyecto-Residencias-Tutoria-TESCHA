<?php

require_once("../../sesion.php");

if(
    !in_array(
        "TUTOR",
        $_SESSION["roles"]
    )
){
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
OBTENER TUTOR
=========================================*/

$id_usuario =
$_SESSION['id_usuario'];

$sqlTutor =
mysqli_query(
    $conn,
    "
    SELECT
        id_personal
    FROM personal_academico
    WHERE id_usuario='$id_usuario'
    LIMIT 1
    "
);

if(
    mysqli_num_rows($sqlTutor)==0
){
    exit();
}

$tutor =
mysqli_fetch_assoc(
    $sqlTutor
);

$id_tutor =
$tutor['id_personal'];

/*=========================================
ID ACTIVIDAD
=========================================*/

$id_actividad =
mysqli_real_escape_string(
    $conn,
    $_POST['id_actividad'] ?? ""
);

if(
    empty($id_actividad)
){

    echo "ERROR";

    exit();

}

/*=========================================
OBTENER ACTIVIDAD
=========================================*/

$sql =
mysqli_query(
    $conn,
    "
    SELECT *

    FROM actividades_tutor

    WHERE

        id_actividad='$id_actividad'

        AND

        id_tutor='$id_tutor'

    LIMIT 1
    "
);

if(
    mysqli_num_rows($sql)==0
){

    echo "ERROR";

    exit();

}

$actividad =
mysqli_fetch_assoc($sql);

/*=========================================
ELIMINAR PDF
=========================================*/

if(
    !empty(
        $actividad['pdf']
    )
){

    $archivo =
    "uploads/pdf/".
    $actividad['pdf'];

    if(
        file_exists(
            $archivo
        )
    ){

        unlink(
            $archivo
        );

    }

}

/*=========================================
ELIMINAR IMAGENES
=========================================*/

$imagenes = [

    $actividad['imagen1'],

    $actividad['imagen2'],

    $actividad['imagen3']

];

foreach(
    $imagenes
    as
    $imagen
){

    if(
        !empty($imagen)
    ){

        $ruta =
        "uploads/imagenes/".
        $imagen;

        if(
            file_exists(
                $ruta
            )
        ){

            unlink(
                $ruta
            );

        }

    }

}

/*=========================================
ELIMINAR REGISTRO
=========================================*/

if(

    mysqli_query(

        $conn,

        "

        DELETE FROM actividades_tutor

        WHERE id_actividad='$id_actividad'

        "

    )

){

    echo "OK";

}
else{

    echo "ERROR";

}

?>