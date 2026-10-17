<?php
require_once __DIR__ . '/../../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');

include("../../base_sist/conect_sist.php");

$id = (int)($_POST['id']);

$titulo = sist_esc($_POST['titulo']);
$descripcion = sist_esc($_POST['descripcion']);
$link = sist_esc($_POST['link']);

$imagen = sist_esc($_POST['imagen_actual']);

if(!empty(sist_nombre_subida($_FILES['imagen']['name']))){

    $imagen = sist_nombre_subida($_FILES['imagen']['name']);

    sist_mover_subida(
        $_FILES['imagen']['tmp_name'],
        "../../vista_inst_vinculantes/images/".$imagen
    );
}

$sql = "
UPDATE instituciones_items SET

titulo='$titulo',
descripcion='$descripcion',
imagen='$imagen',
link='$link'

WHERE id='$id'
";

mysqli_query($conexion,$sql);

header("Location:index.php");
exit();

?>