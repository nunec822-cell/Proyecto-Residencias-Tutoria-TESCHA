<?php

include("../base_sist/conect_sist.php");

$id = $_POST['id'];

$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$enlace = $_POST['enlace'];

$sqlActual = "
SELECT *
FROM modelo_educativo
WHERE id='$id'
";

$resActual = mysqli_query(
    $conexion,
    $sqlActual
);

$actual = mysqli_fetch_assoc(
    $resActual
);

$imagen = $actual['imagen'];
$pdf = $actual['pdf'];


// IMAGEN

if(
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['name'] != ""
){

    $imagen = time()."_".$_FILES['imagen']['name'];

    move_uploaded_file(
        $_FILES['imagen']['tmp_name'],
        "../vista_tutorias/images/".$imagen
    );
}


// PDF

if(
    isset($_FILES['pdf']) &&
    $_FILES['pdf']['name'] != ""
){

    $pdf = time()."_".$_FILES['pdf']['name'];

    move_uploaded_file(
        $_FILES['pdf']['tmp_name'],
        "../vista_tutorias/images/".$pdf
    );
}

$sql = "
UPDATE modelo_educativo
SET

titulo='$titulo',
descripcion='$descripcion',
enlace='$enlace',
imagen='$imagen',
pdf='$pdf'

WHERE id='$id'
";

mysqli_query(
    $conexion,
    $sql
);

header(
    "Location:index.php"
);