<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

require_once("../../sesion.php");

requerir_rol('ADMIN');

include(
    "../../base_pit/conect_pit.php"
);

$id =
(int)($_GET['id'] ?? 0);

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