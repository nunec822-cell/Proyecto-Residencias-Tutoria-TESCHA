<?php
require_once __DIR__ . '/../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');

include("../base_sist/conect_sist.php");

if(isset($_FILES['imagen'])){

    $nombre = sist_nombre_subida($_FILES['imagen']['name']);
    $tmp = $_FILES['imagen']['tmp_name'];

    // 🔥 GUARDAR EN ASSETS
    sist_mover_subida(
        $tmp,
        "../assets/" . $nombre
    );

    // 🔥 INSERTAR EN BD
    $sql = "
    INSERT INTO galeria_inicio(imagen)
    VALUES('$nombre')
    ";

    mysqli_query($conexion, $sql);

}

header("Location: editar_inicio.php?id=1");
exit();

?>