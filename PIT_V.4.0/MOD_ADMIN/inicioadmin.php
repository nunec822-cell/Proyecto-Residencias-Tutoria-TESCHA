<?php
require_once("../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../indexloguin.php");
    exit();
}



// CONEXIÓN A BD
require_once '../base_pit/conect_pit.php';

// BASE URL (opcional para assets si lo necesitas después)
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/SIST V.4.0/PIT_V.4.0/";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio Admin | PIT</title>

    <!-- CSS DEL MÓDULO -->
    <link rel="stylesheet" href="css/estilo.css">

    <!-- ICONOS (opcional pero recomendado) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>


<?php include("../includes_pit/sidebar_admin.php"); ?>
<!-- CONTENEDOR PRINCIPAL -->
<div class="separador"></div>
<div class="admin-container">


<!-- HERO / BANNER INSTITUCIONAL -->
<!-- ============================= -->
<section class="hero">

    <!-- TEXTO -->
    <div class="hero-text">

        <div class="hero-top">

            <!-- ICONO -->
            <div class="hero-icon">
                🌙
            </div>

            <!-- SALUDO -->
            <div>

                <h2 id="saludo">
                    Cargando saludo...
                </h2>

                <span class="badge-admin">
                    🛡 Panel del Administrador
                </span>

            </div>

        </div>

        <!-- TÍTULO -->
        <h1 class="titulo-sistema">
            Programa Institucional de Tutorías
        </h1>

        <!-- DESCRIPCIÓN -->
        <p class="subtitulo">

            Departamento de Desarrollo Académico<br>

            Tecnológico de Estudios Superiores de Chalco<br>

            Administra docentes, tutorados, coordinadores y avisos institucionales.

        </p>

        <!-- FECHA -->
        <div class="fecha-hora">

            <span id="fecha">
                📅 Cargando...
            </span>

            <span id="hora">
                🕒 Cargando...
            </span>

        </div>

    </div>
    

    <!-- TARJETA DEL LOGO -->
    <div class="hero-image">

        <img src="<?php echo $base_url; ?>../assets_pit/desarrollo.webp" alt="TESCHA">

    </div>

</section>
    

    <!-- ============================= -->
    <!-- SECCIÓN DE AVISOS -->
    <!-- ============================= -->
    <section class="avisos-section">

        <div class="avisos-header">
            
            <h2>📢 Avisos Institucionales 👋Aquí podrás publicar
avisos institucionales para docentes,
tutores y coordinadores.</h2>
            
            <a href="nuevo_aviso.php" class="btn-crear">
                + Publicar Aviso
            </a>

        </div>

        <!-- CONTENEDOR DINÁMICO DE AVISOS -->
        <div id="contenedor-avisos" class="grid-avisos">

<?php

$sql = "SELECT * FROM avisos 
        WHERE estado = 'ACTIVO'
        ORDER BY fecha_publicacion DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

?>

    <div class="aviso-card">

    <div class="aviso-top">

        <span class="badge-prioridad">
            <?php echo $row['prioridad']; ?>
        </span>

        <span class="fecha-aviso">
            <i class="fa-solid fa-calendar-days"></i>
            <?php echo date("d/m/Y",strtotime($row['fecha_publicacion'])); ?>
        </span>

    </div>

    <?php if(!empty($row['imagen'])){ ?>

        <img class="aviso-imagen"
        src="../uploads/avisos/imagenes/<?php echo $row['imagen']; ?>">

    <?php } ?>

    <div class="aviso-contenido">

        <h3><?php echo $row['titulo']; ?></h3>

        <p>

            <?php
            if(!empty($row['descripcion']))
                echo $row['descripcion'];
            else
                echo "Este aviso no contiene una descripción.";
            ?>

        </p>

        <div class="aviso-recursos">

            <?php if(!empty($row['pdf'])){ ?>

                <a class="btn-pdf"
                href="../uploads/avisos/pdfs/<?php echo $row['pdf']; ?>"
                target="_blank">

                    <i class="fa-solid fa-file-pdf"></i> PDF

                </a>

            <?php } ?>

            <?php if(!empty($row['link'])){ ?>

                <a class="btn-link"
                href="<?php echo $row['link']; ?>"
                target="_blank">

                    <i class="fa-solid fa-link"></i> Abrir

                </a>

            <?php } ?>

        </div>

    </div>

    <div class="aviso-footer">

        <a class="btn-editar"
        href="editar_aviso.php?id=<?php echo $row['id_aviso']; ?>">

            <i class="fa-solid fa-pen"></i>
            Editar

        </a>

        <a class="btn-eliminar"
        href="eliminar_aviso.php?id=<?php echo $row['id_aviso']; ?>"
        onclick="return confirm('¿Eliminar este aviso?');">

            <i class="fa-solid fa-trash"></i>
            Eliminar

        </a>

    </div>

</div>
<?php
    }

} else {
?>

    <div class="sin-avisos">
        <i class="fa-regular fa-bell"></i>
        <p>No hay avisos publicados</p>
        <span>Cuando el administrador cree avisos aparecerán aquí</span>
    </div>

<?php
}
?>

</div>

    </section>

    <!-- ============================= -->
    <!-- FRASE INSTITUCIONAL -->
    <!-- ============================= -->
    <section class="frase">

        <p id="frase-texto">
            "La educación transforma vidas."
        </p>

    </section>

</div>

<?php include '../../includes/footer.php'; ?>

<!-- JS DEL MÓDULO -->
<script src="js/animaciones.js"></script>

</body>
</html>