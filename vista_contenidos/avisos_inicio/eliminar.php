<?php
require_once __DIR__ . '/../../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');

include("../../base_sist/conect_sist.php");

$id = (int)($_GET['id']);

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
    file_exists("../../assets/avisos/".basename((string)$aviso['imagen']))
){
    unlink("../../assets/avisos/".basename((string)$aviso['imagen']));
}

/* BORRAR PDF */
if(
    !empty($aviso['pdf']) &&
    file_exists("../../assets/avisos/".basename((string)$aviso['pdf']))
){
    unlink("../../assets/avisos/".basename((string)$aviso['pdf']));
}

/* BORRAR REGISTRO */
mysqli_query(
    $conexion,
    "DELETE FROM avisos_inicio WHERE id='$id'"
);

header("Location:index.php");
exit;

?>