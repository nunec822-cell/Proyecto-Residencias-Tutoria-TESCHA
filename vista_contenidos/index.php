
<?php
require_once("../PIT_V.4.0/sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../PIT_V.4.0/indexloguin.php");
    exit();
}
include("../base_sist/conect_sist.php");
include("../PIT_V.4.0/includes_pit/sidebar_admin.php");


// 🔥 INICIO
$sqlInicio = "SELECT * FROM inicio_contenidos LIMIT 1";
$resInicio = mysqli_query($conexion, $sqlInicio);

$filaInicio = mysqli_fetch_assoc($resInicio);

// 🔥 TUTORÍAS
$sqlTutorias = "SELECT * FROM tutorias_contenidos LIMIT 1";
$resTutorias = mysqli_query($conexion, $sqlTutorias);

$filaTutorias = mysqli_fetch_assoc($resTutorias);

// 🔥 INSTITUCIONES VINCULANTES
$sqlInstituciones = "SELECT * FROM instituciones_contenidos LIMIT 1";
$resInstituciones = mysqli_query($conexion, $sqlInstituciones);

$filaInstituciones = mysqli_fetch_assoc($resInstituciones);
?>
<div class="separador"></div>
<div class="separador"></div>
<link rel="stylesheet" href="css/estilo.css">
<!-- ICONOS (opcional pero recomendado) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<div class="contenedor-admin">

    <h1>Gestor de Contenidos</h1>

    <!-- 🔥 GRID -->
    <div class="grid-vistas">

        <!-- 🔵 VISTA INICIO -->
        <div class="card-admin">

            <h2>VISTA INICIO</h2>
            <a href="editar_sidebar.php">
            Gestionar estilo barra principal
            </a>
            <hr>
            <a href="editar_inicio.php?id=<?php echo $filaInicio['id']; ?>">
                Editar contenido
            </a>

            <hr>

            <h3>
                <?php echo $filaInicio['titulo_bienvenida']; ?>
            </h3>

            <p>
                <?php echo $filaInicio['texto_bienvenida']; ?>
            </p>
            <h2>Avisos y Novedades</h2>

    <a href="avisos_inicio/index.php">
        Administrar avisos
    </a>

    <hr>

    <h3>
        Publicaciones dinámicas
    </h3>

    <p>
        Desde aquí puedes publicar avisos, noticias,
        convocatorias, enlaces, imágenes y documentos PDF
        que aparecerán en la página principal del SIST.
    </p>
        </div>

        <!-- 🔴 VISTA TUTORÍAS -->
        <div class="card-admin">

            <h2>VISTA TUTORIAS Y SUS MODALIDADES </h2>

            <a href="editar_tutorias.php?id=<?php echo $filaTutorias['id']; ?>">
                Editar contenido
            </a>

            <hr>

            <h3>
                <?php echo $filaTutorias['titulo_bloque1']; ?>
            </h3>

            <p>
                <?php echo $filaTutorias['resumen_bloque1']; ?>
            </p>
            <h2>Modelo Educativo</h2>

    <a href="editar_modelo.php">
        Editar contenido
    </a>

    <hr>

    <?php

    $sqlModelo = "
    SELECT *
    FROM modelo_educativo
    LIMIT 1
    ";

    $resModelo = mysqli_query(
        $conexion,
        $sqlModelo
    );

    $modelo = mysqli_fetch_assoc(
        $resModelo
    );

    ?>

    <h3>
        <?php echo $modelo['titulo']; ?>
    </h3>

    <p>
        <?php echo substr(
            $modelo['descripcion'],
            0,
            120
        ); ?>...
    </p>
        </div>


        <!-- 🟢 VISTA INSTITUCIONES -->
        <div class="card-admin">

            <h2>VISTA INSTITUCIONES VINCULANTES </h2>

            <!-- 🔥 Editar encabezado -->
            <a href="editar_instituciones.php?id=<?php echo $filaInstituciones['id']; ?>">
                Editar encabezado
            </a>

            <br>
            
            <br>
            
            <!-- 🔥 Administrar instituciones -->
            <a href="instituciones/index.php">
                Administrar instituciones
            </a>

            <hr>

            <h3>
                <?php echo $filaInstituciones['titulo_principal']; ?>
            </h3>

            <p>
                <?php echo $filaInstituciones['descripcion_principal']; ?>
            </p>

        </div>
        

    </div>



</div>

<?php include("../includes/footer.php"); ?>