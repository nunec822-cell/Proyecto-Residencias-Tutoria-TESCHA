<?php
require_once __DIR__ . '/../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');
sist_requerir_csrf_post();   /* el formulario debe traer el token de la sesión */

include("../base_sist/conect_sist.php");

// 🔥 RECIBIR DATOS
$id = (int)($_POST['id']);

$titulo_bienvenida = sist_esc($_POST['titulo_bienvenida']);
$texto_bienvenida = sist_esc($_POST['texto_bienvenida']);

$titulo_seccion1 = sist_esc($_POST['titulo_seccion1']);
$texto_seccion1 = sist_esc($_POST['texto_seccion1']);

$titulo_seccion2 = sist_esc($_POST['titulo_seccion2']);
$texto_seccion2 = sist_esc($_POST['texto_seccion2']);

$texto_completo_seccion2 = sist_esc($_POST['texto_completo_seccion2']);


// 🔥 FUNCIÓN SUBIR IMAGEN
function subirImagen($campo, $actual){

    if(isset($_FILES[$campo]) && sist_nombre_subida($_FILES[$campo]['name']) != ""){

        $nombre = sist_nombre_subida($_FILES[$campo]['name']);

        $tmp = $_FILES[$campo]['tmp_name'];

        sist_mover_subida(
            $tmp,
            "../assets/" . $nombre
        );

        return $nombre;

    }

    return $actual;

}


// 🔥 IMÁGENES
$personaje_izq = sist_esc(subirImagen(
    "personaje_izq",
    $_POST['personaje_izq_actual']
));

$personaje_der = sist_esc(subirImagen(
    "personaje_der",
    $_POST['personaje_der_actual']
));

$slider1 = sist_esc(subirImagen(
    "slider1",
    $_POST['slider1_actual']
));

$slider2 = sist_esc(subirImagen(
    "slider2",
    $_POST['slider2_actual']
));

$slider3 = sist_esc(subirImagen(
    "slider3",
    $_POST['slider3_actual']
));

$slider4 = sist_esc(subirImagen(
    "slider4",
    $_POST['slider4_actual']
));

$imagen_seccion1 = sist_esc(subirImagen(
    "imagen_seccion1",
    $_POST['imagen_seccion1_actual']
));

$imagen_seccion2 = sist_esc(subirImagen(
    "imagen_seccion2",
    $_POST['imagen_seccion2_actual']
));


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