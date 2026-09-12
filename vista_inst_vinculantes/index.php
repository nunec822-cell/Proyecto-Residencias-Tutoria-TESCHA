<?php

include("../base_sist/conect_sist.php");
include("../includes/header.php");

// 🔥 ENCABEZADO
$sql = "SELECT * FROM instituciones_contenidos LIMIT 1";
$resultado = mysqli_query($conexion, $sql);

$fila = mysqli_fetch_assoc($resultado);

// 🔥 INSTITUCIONES
$sqlInstituciones = "
SELECT *
FROM instituciones_items
ORDER BY id ASC
";

$resInstituciones = mysqli_query(
    $conexion,
    $sqlInstituciones
);

?>

<link rel="stylesheet" href="css/estilo.css">

<div class="separador"></div>

<!-- 🔥 SIDEBAR -->
<?php include("../includes/sidebar-sist.php"); ?>

<div class="separador"></div>

<!-- 🔥 CONTENIDO -->
<div class="vista-inst">

    <!-- 🔥 TÍTULO -->
    <div class="titulo-inst">

        <h1>
            <?php echo $fila['titulo_principal']; ?>
        </h1>

        <p>
            <?php echo $fila['descripcion_principal']; ?>
        </p>

    </div>

    <!-- 🔥 CONTENEDOR -->
    <div class="contenedor-inst">

        <?php while($institucion = mysqli_fetch_assoc($resInstituciones)){ ?>

            <div class="card-inst">

                <div class="logo-inst">

                    <img
                        src="images/<?php echo $institucion['imagen']; ?>"
                        alt="<?php echo $institucion['titulo']; ?>"
                    >

                </div>

                <div class="info-inst">

                    <h2>
                        <?php echo $institucion['titulo']; ?>
                    </h2>

                    <p>
                        <?php echo $institucion['descripcion']; ?>
                    </p>

                    <a
                        href="<?php echo $institucion['link']; ?>"
                        target="_blank"
                        class="btn-inst"
                    >
                        🌐 Visitar sitio oficial
                    </a>

                </div>

            </div>

        <?php } ?>

    </div>

</div>

<!-- 🔥 JS -->
<script src="js/animaciones.js"></script>

<!-- 🔥 FOOTER -->
<?php include("../includes/footer.php"); ?>