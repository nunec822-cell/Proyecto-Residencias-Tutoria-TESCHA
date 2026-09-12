<?php

require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
DATOS
=========================================*/

$id_tutorado   =
mysqli_real_escape_string(
    $conn,
    $_POST['id_tutorado']
);

$id_tutor =
mysqli_real_escape_string(
    $conn,
    $_POST['id_tutor']
);

$observaciones =
mysqli_real_escape_string(
    $conn,
    trim(
        $_POST['observaciones']
    )
);

$id_admin =
$_SESSION['id_usuario'];

/*=========================================
VALIDACIONES
=========================================*/

if(
    empty($id_tutorado) ||
    empty($id_tutor)
){

    echo "ERROR";
    exit();

}

/*=========================================
VERIFICAR CANALIZACIÓN
=========================================*/

$consulta =
mysqli_query(
    $conn,
    "
    SELECT *
    FROM canalizaciones
    WHERE id_tutorado=
    '$id_tutorado'
    AND estado IN(
        'PENDIENTE',
        'EN PROCESO'
    )
    "
);

if(
    mysqli_num_rows(
        $consulta
    ) > 0
){

    echo "EXISTE";
    exit();

}

/*=========================================
INSERTAR
=========================================*/

$sql = "
INSERT INTO canalizaciones(

    id_tutorado,
    id_tutor,
    id_admin,
    observaciones

)
VALUES(

    '$id_tutorado',
    '$id_tutor',
    '$id_admin',
    '$observaciones'

)
";

if(mysqli_query($conn,$sql)){

    $id_canalizacion =
    mysqli_insert_id($conn);

    mysqli_query(
        $conn,
        "
        UPDATE anexo14_reportes
        SET id_canalizacion =
        '$id_canalizacion'
        WHERE id_tutorado =
        '$id_tutorado'
        AND id_canalizacion IS NULL
        "
    );

    echo "OK";

}
else{

    echo "ERROR";

}

?>