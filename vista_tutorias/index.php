<?php

include("../base_sist/conect_sist.php");
include("../includes/header.php");

// 🔥 OBTENER CONTENIDO TUTORÍAS
$sql = "SELECT * FROM tutorias_contenidos LIMIT 1";
$resultado = mysqli_query($conexion, $sql);
$datos = mysqli_fetch_assoc($resultado);

// 🔥 MODELO EDUCATIVO
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

<link rel="stylesheet" href="css/estilotutorias.css">

<div class="separador"></div>

<?php include("../includes/sidebar-sist.php"); ?>

<!-- =====================================
     CONTENIDO TUTORÍAS
===================================== -->
<div class="vista-tutorias">

    <div class="contenedor-tutorias">

        <!-- 🔵 BLOQUE 1 -->
        <div class="tutorias-box">

            <div class="tutorias-img">
                <img
                    src="images/<?php echo $datos['imagen_bloque1']; ?>"
                    alt="Tutorías"
                >
            </div>

            <div class="tutorias-texto">

                <h2>
                    <?php echo $datos['titulo_bloque1']; ?>
                </h2>

                <p class="resumen">
                    <?php echo $datos['resumen_bloque1']; ?>
                </p>

                <p class="completo">
                    <?php echo $datos['completo_bloque1']; ?>
                </p>

                <button
                    class="btn-vermas"
                    onclick="toggleTexto(this)"
                >
                    Ver más
                </button>

            </div>

        </div>

        <!-- 🔴 BLOQUE 2 -->
        <div class="tutorias-box reverse">

            <div class="tutorias-img">

                <img
                    src="images/<?php echo $datos['imagen_bloque2']; ?>"
                    alt="Modalidades"
                >

            </div>

            <div class="tutorias-texto">

                <h2>
                    <?php echo $datos['titulo_bloque2']; ?>
                </h2>

                <p class="resumen">
                    <?php echo $datos['resumen_bloque2']; ?>
                </p>

                <p class="completo">
                    <?php echo $datos['completo_bloque2']; ?>
                </p>

                <button
                    class="btn-vermas"
                    onclick="toggleTexto(this)"
                >
                    Ver más
                </button>

            </div>
            
        </div>
        <!-- =====================================
     MODELO EDUCATIVO
===================================== -->
<div class="modelo-section">

    <?php if(!empty($modelo['imagen'])){ ?>

        <div class="modelo-imagen">

            <img
                src="images/<?php echo $modelo['imagen']; ?>"
                alt="Modelo Educativo"
            >

        </div>

    <?php } ?>

    <div class="modelo-contenido">

        <h2>
            <?php echo $modelo['titulo']; ?>
        </h2>

        <p>
            <?php echo nl2br($modelo['descripcion']); ?>
        </p>

        <?php if(!empty($modelo['pdf'])){ ?>

            <p class="contador-descargas">
                📥 Descargas:
                <strong>
                    <?php echo $modelo['descargas']; ?>
                </strong>
            </p>

        <?php } ?>

        <div class="modelo-botones">

            <?php if(!empty($modelo['pdf'])){ ?>

                <a
                    href="visor_pdf.php"
                    target="_blank"
                    class="btn-modelo"
                >
                    👁 Abrir PDF
                </a>

                <a
                    href="descargar_pdf.php"
                    class="btn-modelo descargar"
                >
                    ⬇ Descargar PDF
                </a>

            <?php } ?>

            <?php if(!empty($modelo['enlace'])){ ?>

                <a
                    href="<?php echo $modelo['enlace']; ?>"
                    target="_blank"
                    class="btn-modelo"
                >
                    🔗 Más información
                </a>

            <?php } ?>

        </div>

    </div>

</div>

    

</div>
    </div>

</div>



<!-- =====================================
     RIESGOS ESCOLARES
===================================== -->
<div class="riesgos-section">

    <h2 class="titulo-riesgos">
        <?php echo $datos['titulo_riesgos']; ?>
    </h2>

    <p class="subtitulo-riesgos">
        <?php echo $datos['subtitulo_riesgos']; ?>
    </p>

    <div class="grid-riesgos">

        <!-- RIESGO 1 -->
        <div class="riesgo-card">

            <img src="images/<?php echo $datos['imagen_riesgo1']; ?>">

            <h3>
                <?php echo $datos['titulo_riesgo1']; ?>
            </h3>

            <p>
                <?php echo $datos['texto_riesgo1']; ?>
            </p>

        </div>

        <!-- RIESGO 2 -->
        <div class="riesgo-card">

            <img src="images/<?php echo $datos['imagen_riesgo2']; ?>">

            <h3>
                <?php echo $datos['titulo_riesgo2']; ?>
            </h3>

            <p>
                <?php echo $datos['texto_riesgo2']; ?>
            </p>

        </div>

        <!-- RIESGO 3 -->
        <div class="riesgo-card">

            <img src="images/<?php echo $datos['imagen_riesgo3']; ?>">

            <h3>
                <?php echo $datos['titulo_riesgo3']; ?>
            </h3>

            <p>
                <?php echo $datos['texto_riesgo3']; ?>
            </p>

        </div>

        <!-- RIESGO 4 -->
        <div class="riesgo-card">

            <img src="images/<?php echo $datos['imagen_riesgo4']; ?>">

            <h3>
                <?php echo $datos['titulo_riesgo4']; ?>
            </h3>

            <p>
                <?php echo $datos['texto_riesgo4']; ?>
            </p>

        </div>

        <!-- RIESGO 5 -->
        <div class="riesgo-card">

            <img src="images/<?php echo $datos['imagen_riesgo5']; ?>">

            <h3>
                <?php echo $datos['titulo_riesgo5']; ?>
            </h3>

            <p>
                <?php echo $datos['texto_riesgo5']; ?>
            </p>

        </div>

        <!-- RIESGO 6 -->
        <div class="riesgo-card">

            <img src="images/<?php echo $datos['imagen_riesgo6']; ?>">

            <h3>
                <?php echo $datos['titulo_riesgo6']; ?>
            </h3>

            <p>
                <?php echo $datos['texto_riesgo6']; ?>
            </p>

        </div>

    </div>

</div>

<script src="js/animacionestutorias.js"></script>

<?php include("../includes/footer.php"); ?>