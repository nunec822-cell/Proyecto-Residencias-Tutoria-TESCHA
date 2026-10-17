<?php
require_once __DIR__ . '/../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');

include("../base_sist/conect_sist.php");

$id = (int)($_POST['id']);

$titulo = sist_esc($_POST['titulo']);
$descripcion = sist_esc($_POST['descripcion']);
$enlace = sist_esc($_POST['enlace']);

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
    sist_nombre_subida($_FILES['imagen']['name']) != ""
){

    $imagen = time()."_".sist_nombre_subida($_FILES['imagen']['name']);

    sist_mover_subida(
        $_FILES['imagen']['tmp_name'],
        "../vista_tutorias/images/".$imagen
    );
}


// PDF

if(
    isset($_FILES['pdf']) &&
    sist_nombre_subida($_FILES['pdf']['name']) != ""
){

    $pdf = time()."_".sist_nombre_subida($_FILES['pdf']['name']);

    sist_mover_subida(
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