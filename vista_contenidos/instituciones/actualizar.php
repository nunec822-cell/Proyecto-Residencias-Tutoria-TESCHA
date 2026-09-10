<?php

include("../../base_sist/conect_sist.php");

$id = $_POST['id'];

$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$link = $_POST['link'];

$imagen = $_POST['imagen_actual'];

if(!empty($_FILES['imagen']['name'])){

    $imagen = $_FILES['imagen']['name'];

    move_uploaded_file(
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