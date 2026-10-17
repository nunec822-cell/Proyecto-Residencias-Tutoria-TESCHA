<?php
require_once __DIR__ . '/../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');

include("../base_sist/conect_sist.php");

$id = (int)($_GET['id']);

// 🔥 OBTENER IMAGEN
$sql = "SELECT * FROM galeria_inicio WHERE id='$id'";
$res = mysqli_query($conexion, $sql);

$img = mysqli_fetch_assoc($res);

// 🔥 ELIMINAR ARCHIVO
$ruta = "../assets/" . basename((string) ($img['imagen'] ?? ''));  // basename: evita salir de la carpeta

if(is_file($ruta)){
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