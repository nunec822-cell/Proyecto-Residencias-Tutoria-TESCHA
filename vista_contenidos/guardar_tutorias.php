<?php require_once __DIR__ . '/../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');
sist_requerir_csrf_post();   /* el formulario debe traer el token de la sesión */


include("../base_sist/conect_sist.php");

// 🔥 ID
$id = (int)($_POST['id']);



// =====================================================
// 🔥 TEXTOS BLOQUE 1
// =====================================================

$titulo_bloque1 = sist_esc($_POST['titulo_bloque1']);
$resumen_bloque1 = sist_esc($_POST['resumen_bloque1']);
$completo_bloque1 = sist_esc($_POST['completo_bloque1']);



// =====================================================
// 🔥 TEXTOS BLOQUE 2
// =====================================================

$titulo_bloque2 = sist_esc($_POST['titulo_bloque2']);
$resumen_bloque2 = sist_esc($_POST['resumen_bloque2']);
$completo_bloque2 = sist_esc($_POST['completo_bloque2']);



// =====================================================
// 🔥 TÍTULOS RIESGOS
// =====================================================

$titulo_riesgos = "RIESGOS ESCOLARES";
$subtitulo_riesgos = "Factores de riesgo para la deserción escolar";



// =====================================================
// 🔥 OBTENER DATOS ACTUALES
// =====================================================

$sqlActual = "
SELECT * 
FROM tutorias_contenidos 
WHERE id='$id'
";

$resActual = mysqli_query($conexion, $sqlActual);

$actual = mysqli_fetch_assoc($resActual);



// =====================================================
// 🔵 IMAGEN BLOQUE 1
// =====================================================

$imagen_bloque1 = $actual['imagen_bloque1'];

if(!empty(sist_nombre_subida($_FILES['imagen_bloque1']['name']))){

    $imagen_bloque1 = sist_nombre_subida($_FILES['imagen_bloque1']['name']);

    sist_mover_subida(
        $_FILES['imagen_bloque1']['tmp_name'],
        "../vista_tutorias/images/".$imagen_bloque1
    );
}



// =====================================================
// 🔴 IMAGEN BLOQUE 2
// =====================================================

$imagen_bloque2 = $actual['imagen_bloque2'];

if(!empty(sist_nombre_subida($_FILES['imagen_bloque2']['name']))){

    $imagen_bloque2 = sist_nombre_subida($_FILES['imagen_bloque2']['name']);

    sist_mover_subida(
        $_FILES['imagen_bloque2']['tmp_name'],
        "../vista_tutorias/images/".$imagen_bloque2
    );
}



// =====================================================
// 🔥 RIESGOS
// =====================================================

for($i=1; $i<=6; $i++){

    ${"titulo_riesgo".$i} = $_POST["titulo_riesgo".$i];
    ${"texto_riesgo".$i} = $_POST["texto_riesgo".$i];

    ${"imagen_riesgo".$i} = $actual["imagen_riesgo".$i];

    if(!empty(sist_nombre_subida($_FILES["imagen_riesgo".$i]['name']))){

        ${"imagen_riesgo".$i} = sist_nombre_subida($_FILES["imagen_riesgo".$i]['name']);

        sist_mover_subida(
            $_FILES["imagen_riesgo".$i]['tmp_name'],
            "../vista_tutorias/images/".${"imagen_riesgo".$i}
        );
    }
}



// =====================================================
// 🔥 UPDATE
// =====================================================

$sql = "
UPDATE tutorias_contenidos SET

titulo_bloque1='$titulo_bloque1',
resumen_bloque1='$resumen_bloque1',
completo_bloque1='$completo_bloque1',
imagen_bloque1='$imagen_bloque1',

titulo_bloque2='$titulo_bloque2',
resumen_bloque2='$resumen_bloque2',
completo_bloque2='$completo_bloque2',
imagen_bloque2='$imagen_bloque2',

titulo_riesgos='$titulo_riesgos',
subtitulo_riesgos='$subtitulo_riesgos',

titulo_riesgo1='$titulo_riesgo1',
texto_riesgo1='$texto_riesgo1',
imagen_riesgo1='$imagen_riesgo1',

titulo_riesgo2='$titulo_riesgo2',
texto_riesgo2='$texto_riesgo2',
imagen_riesgo2='$imagen_riesgo2',

titulo_riesgo3='$titulo_riesgo3',
texto_riesgo3='$texto_riesgo3',
imagen_riesgo3='$imagen_riesgo3',

titulo_riesgo4='$titulo_riesgo4',
texto_riesgo4='$texto_riesgo4',
imagen_riesgo4='$imagen_riesgo4',

titulo_riesgo5='$titulo_riesgo5',
texto_riesgo5='$texto_riesgo5',
imagen_riesgo5='$imagen_riesgo5',

titulo_riesgo6='$titulo_riesgo6',
texto_riesgo6='$texto_riesgo6',
imagen_riesgo6='$imagen_riesgo6'

WHERE id='$id'
";



// =====================================================
// 🔥 EJECUTAR
// =====================================================

mysqli_query($conexion, $sql);



// =====================================================
// 🔥 REDIRECCIÓN
// =====================================================

header("Location: index.php");

exit();

?>