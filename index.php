<?php
// 🔥 CONEXIÓN A BASE DE DATOS
include("base_sist/conect_sist.php");

// 🔥 OBTENER CONTENIDO
$sql = "SELECT * FROM inicio_contenidos LIMIT 1";
$resultado = mysqli_query($conexion, $sql);

$datos = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIST</title>

    <!-- 🔥 CSS EXTERNO -->
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/estilogaleria.css?v=1">
</head>
<body>

<!-- HEADER -->
<?php include("includes/header.php"); ?>

<div class="separador"></div>

<!-- MENÚ -->
<?php include("includes/sidebar-sist.php"); ?>

<div class="separador"></div>

<div class="banner">

    <img 
        src="./assets/<?php echo $datos['slider1']; ?>" 
        class="active"
    >

    <img 
        src="./assets/<?php echo $datos['slider2']; ?>"
    >

    <img 
        src="./assets/<?php echo $datos['slider3']; ?>"
    >

    <img 
        src="./assets/<?php echo $datos['slider4']; ?>"
    >

</div>

<!-- 🔥 BIENVENIDO -->
<div class="bienvenido">

    <!-- 👈 PERSONAJE IZQUIERDO -->
    <div class="personaje izq">
    <img src="assets/<?php echo $datos['personaje_izq']; ?>">
</div>

<div class="personaje der">
    <img src="assets/<?php echo $datos['personaje_der']; ?>">
</div>

    <!-- 🔥 DINÁMICO -->
    <h1><?php echo $datos['titulo_bienvenida']; ?></h1>

    <p>
        <?php echo $datos['texto_bienvenida']; ?>
    </p>

</div>

<!-- 🔥 SECCIÓN 1 -->
<div class="seccion">

    <img src="./assets/<?php echo $datos['imagen_seccion1']; ?>">

    <div class="texto">

        <h3>
            <?php echo $datos['titulo_seccion1']; ?>
            <span class="audio">🔊</span>
        </h3>

        <p>
            <?php echo $datos['texto_seccion1']; ?>
        </p>

    </div>

</div>

<!-- 🔥 SECCIÓN 2 -->
<div class="seccion reverse">

    <img src="./assets/<?php echo $datos['imagen_seccion2']; ?>">

    <div class="texto">

        <h3>
            <?php echo $datos['titulo_seccion2']; ?>
            <span class="audio">🔊</span>
        </h3>

        <!-- RESUMEN -->
        <p class="resumen">
            <?php echo $datos['texto_seccion2']; ?>
        </p>

        <!-- TEXTO OCULTO -->
        <p class="completo">
            <?php echo $datos['texto_completo_seccion2']; ?>
        </p>

        <button class="btn-vermas" onclick="toggleTexto(this)">
            Ver más
        </button>

    </div>

</div>
<!-- =====================================
     AVISOS Y NOVEDADES
===================================== -->

<div class="avisos-container">

    <h2 class="avisos-titulo">
        Avisos y Novedades
    </h2>

    <?php

    $sqlAvisos = "
    SELECT *
    FROM avisos_inicio
    WHERE estado='Activo'
    ORDER BY fecha_publicacion DESC
    ";

    $resAvisos = mysqli_query($conexion,$sqlAvisos);

    if(mysqli_num_rows($resAvisos) > 0){

        while($aviso = mysqli_fetch_assoc($resAvisos)){
    ?>

        <div class="card-aviso">

            <?php if(!empty($aviso['imagen'])){ ?>

                <div class="aviso-imagen">

                    <img
                        src="assets/avisos/<?php echo $aviso['imagen']; ?>"
                        alt="<?php echo $aviso['titulo']; ?>"
                    >

                </div>

            <?php } ?>

            <div class="aviso-contenido">

    <div class="aviso-fecha">
        📅 Publicado el:
        <?php echo date("d/m/Y", strtotime($aviso['fecha_publicacion'])); ?>
    </div>

    <?php if(!empty($aviso['titulo'])){ ?>

        <h3>
            <?php echo $aviso['titulo']; ?>
        </h3>

    <?php } ?>

    <?php if(!empty($aviso['descripcion'])){ ?>

        <p>
            <?php echo nl2br($aviso['descripcion']); ?>
        </p>

    <?php } ?>

    <div class="aviso-botones">

        <?php if(!empty($aviso['enlace'])){ ?>

            <a
                href="<?php echo $aviso['enlace']; ?>"
                target="_blank"
                class="btn-aviso"
            >
                🔗 Ir al enlace
            </a>

        <?php } ?>

        <?php if(!empty($aviso['pdf'])){ ?>

            <a
                href="assets/avisos/<?php echo $aviso['pdf']; ?>"
                target="_blank"
                class="btn-aviso pdf"
            >
                📄 Ver PDF
            </a>

        <?php } ?>

    </div>

</div>



            </div>

        </div>

    <?php

        }

    }else{

    ?>

        <div class="sin-avisos">

            <h3>
                📢 No hay avisos ni novedades disponibles
            </h3>

            <p>
                Por el momento no existen publicaciones activas.
                Regresa más tarde para consultar nueva información.
            </p>

        </div>

    <?php } ?>

</div>
<!-- 🔥 GALERÍA INTERACTIVA -->
<div class="galeria-container">

    <h2 class="galeria-titulo">
        Explora el SIST
    </h2>

    <!-- ✨ MENSAJE DE BIENVENIDA -->
    <div class="galeria-bienvenida">

        <h3>
            ✨ Bienvenido a nuestra galería de fotos ✨
        </h3>

        <p>
            Sumérgete en un espacio donde cada imagen cuenta una historia.
            Aquí encontrarás momentos únicos, recuerdos especiales y capturas
            llenas de emoción que reflejan creatividad, belleza y pasión.
        </p>

        <p class="indicacion">
            Selecciona la imagen de tu interés 📸
        </p>

    </div>

    <div class="controles-galeria">
        <button id="btn-prev">&#10094;</button>
        <button id="btn-next">&#10095;</button>
    </div>

    <div class="galeria">

        <?php

$sqlGaleria = "SELECT * FROM galeria_inicio ORDER BY id DESC";
$resGaleria = mysqli_query($conexion, $sqlGaleria);

while($img = mysqli_fetch_assoc($resGaleria)){

    echo '
    <div class="card-galeria">
        <img src="assets/'.$img['imagen'].'">
    </div>
    ';

}

?>
    </div>

</div>

<!-- 🔥 JS GALERÍA -->
<script src="js/galeria.js" defer></script>

<!-- FOOTER -->
<?php include("includes/footer.php"); ?>

<!-- 🔥 JS EXTERNO -->
<script src="js/animaciones.js" defer></script>

</body>
</html>