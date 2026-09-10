<?php

include("../base_sist/conect_sist.php");

if(isset($_FILES['imagen'])){

    $nombre = $_FILES['imagen']['name'];
    $tmp = $_FILES['imagen']['tmp_name'];

    // 🔥 GUARDAR EN ASSETS
    move_uploaded_file(
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