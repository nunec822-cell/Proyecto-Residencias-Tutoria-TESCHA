<?php

include("../../base_sist/conect_sist.php");

$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$link = $_POST['link'];

$imagen = $_FILES['imagen']['name'];

move_uploaded_file(
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