<?php

include("../../base_sist/conect_sist.php");

$id = $_GET['id'];

/* OBTENER ARCHIVOS */
$sql = "
SELECT imagen,pdf
FROM avisos_inicio
WHERE id='$id'
";

$res = mysqli_query($conexion,$sql);

$aviso = mysqli_fetch_assoc($res);

/* BORRAR IMAGEN */
if(
    !empty($aviso['imagen']) &&
    file_exists("../../assets/avisos/".$aviso['imagen'])
){
    unlink("../../assets/avisos/".$aviso['imagen']);
}

/* BORRAR PDF */
if(
    !empty($aviso['pdf']) &&
    file_exists("../../assets/avisos/".$aviso['pdf'])
){
    unlink("../../assets/avisos/".$aviso['pdf']);
}

/* BORRAR REGISTRO */
mysqli_query(
    $conexion,
    "DELETE FROM avisos_inicio WHERE id='$id'"
);

header("Location:index.php");
exit;

?>