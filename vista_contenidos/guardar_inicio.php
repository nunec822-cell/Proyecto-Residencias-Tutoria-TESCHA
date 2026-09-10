<?php

include("../base_sist/conect_sist.php");

// 🔥 RECIBIR DATOS
$id = $_POST['id'];

$titulo_bienvenida = $_POST['titulo_bienvenida'];
$texto_bienvenida = $_POST['texto_bienvenida'];

$titulo_seccion1 = $_POST['titulo_seccion1'];
$texto_seccion1 = $_POST['texto_seccion1'];

$titulo_seccion2 = $_POST['titulo_seccion2'];
$texto_seccion2 = $_POST['texto_seccion2'];

$texto_completo_seccion2 = $_POST['texto_completo_seccion2'];


// 🔥 FUNCIÓN SUBIR IMAGEN
function subirImagen($campo, $actual){

    if(isset($_FILES[$campo]) && $_FILES[$campo]['name'] != ""){

        $nombre = $_FILES[$campo]['name'];

        $tmp = $_FILES[$campo]['tmp_name'];

        move_uploaded_file(
            $tmp,
            "../assets/" . $nombre
        );

        return $nombre;

    }

    return $actual;

}


// 🔥 IMÁGENES
$personaje_izq = subirImagen(
    "personaje_izq",
    $_POST['personaje_izq_actual']
);

$personaje_der = subirImagen(
    "personaje_der",
    $_POST['personaje_der_actual']
);

$slider1 = subirImagen(
    "slider1",
    $_POST['slider1_actual']
);

$slider2 = subirImagen(
    "slider2",
    $_POST['slider2_actual']
);

$slider3 = subirImagen(
    "slider3",
    $_POST['slider3_actual']
);

$slider4 = subirImagen(
    "slider4",
    $_POST['slider4_actual']
);

$imagen_seccion1 = subirImagen(
    "imagen_seccion1",
    $_POST['imagen_seccion1_actual']
);

$imagen_seccion2 = subirImagen(
    "imagen_seccion2",
    $_POST['imagen_seccion2_actual']
);


// 🔥 ACTUALIZAR
$sql = "

UPDATE inicio_contenidos SET

titulo_bienvenida = '$titulo_bienvenida',
texto_bienvenida = '$texto_bienvenida',

personaje_izq = '$personaje_izq',
personaje_der = '$personaje_der',

slider1 = '$slider1',
slider2 = '$slider2',
slider3 = '$slider3',
slider4 = '$slider4',

titulo_seccion1 = '$titulo_seccion1',
texto_seccion1 = '$texto_seccion1',

imagen_seccion1 = '$imagen_seccion1',

titulo_seccion2 = '$titulo_seccion2',
texto_seccion2 = '$texto_seccion2',

texto_completo_seccion2 = '$texto_completo_seccion2',

imagen_seccion2 = '$imagen_seccion2'

WHERE id = '$id'

";

// 🔥 EJECUTAR
mysqli_query($conexion, $sql);

// 🔥 REDIRECCIONAR
header("Location: index.php");
exit();

?>