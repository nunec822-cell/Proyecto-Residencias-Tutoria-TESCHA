<?php

include("../base_sist/conect_sist.php");

$id = $_POST['id'];

$titulo_principal = mysqli_real_escape_string(
    $conexion,
    $_POST['titulo_principal']
);

$descripcion_principal = mysqli_real_escape_string(
    $conexion,
    $_POST['descripcion_principal']
);

$sql = "
UPDATE instituciones_contenidos
SET

titulo_principal = '$titulo_principal',
descripcion_principal = '$descripcion_principal'

WHERE id = '$id'
";

mysqli_query($conexion, $sql);

header("Location: index.php");
exit();

?>