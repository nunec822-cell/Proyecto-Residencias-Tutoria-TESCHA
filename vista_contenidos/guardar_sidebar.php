<?php

include("../base_sist/conect_sist.php");

$id = $_POST['id'];
$tema = $_POST['tema'];

$sql = "
UPDATE tema_sidebar
SET tema='$tema'
WHERE id='$id'
";

mysqli_query(
    $conexion,
    $sql
);

header(
    "Location: editar_sidebar.php?ok=1"
);
exit;