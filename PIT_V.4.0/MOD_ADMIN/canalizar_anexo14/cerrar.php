<?php

require_once("../../sesion.php");

if(
    !in_array(
        "ADMIN",
        $_SESSION["roles"]
    )
){
    exit();
}

include(
    "../../base_pit/conect_pit.php"
);

$id =
$_GET['id'] ?? 0;

mysqli_query(
    $conn,
    "
    UPDATE canalizaciones
    SET

        estado='CERRADO',
        fecha_cierre=NOW(),
        nuevos_reportes=0

    WHERE id_tutorado='$id'
    AND estado='ATENDIDO'
    "
);

header(
    "Location: ../revision_anexo14/index.php"
);