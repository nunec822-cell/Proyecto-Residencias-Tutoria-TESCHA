<?php
require_once __DIR__ . '/../../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');

include("../../base_sist/conect_sist.php");

$titulo = sist_esc($_POST['titulo']);
$descripcion = sist_esc($_POST['descripcion']);
$link = sist_esc($_POST['link']);

$imagen = sist_nombre_subida($_FILES['imagen']['name']);

sist_mover_subida(
    $_FILES['imagen']['tmp_name'],
    "../../vista_inst_vinculantes/images/".$imagen
);

$sql = "
INSERT INTO instituciones_items
(
titulo,
descripcion,
imagen,
link
)
VALUES
(
'$titulo',
'$descripcion',
'$imagen',
'$link'
)
";

mysqli_query($conexion,$sql);

header("Location:index.php");
exit();

?>