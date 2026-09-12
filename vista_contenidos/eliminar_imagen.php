<?php

include("../base_sist/conect_sist.php");

$id = $_GET['id'];

// 🔥 OBTENER IMAGEN
$sql = "SELECT * FROM galeria_inicio WHERE id='$id'";
$res = mysqli_query($conexion, $sql);

$img = mysqli_fetch_assoc($res);

// 🔥 ELIMINAR ARCHIVO
$ruta = "../assets/" . $img['imagen'];

if(file_exists($ruta)){
    unlink($ruta);
}

// 🔥 ELIMINAR REGISTRO
mysqli_query(
    $conexion,
    "DELETE FROM galeria_inicio WHERE id='$id'"
);

header("Location: editar_inicio.php?id=1");
exit();

?>