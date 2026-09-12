<?php
session_start();

require_once '../base_pit/conect_pit.php';

// Ruta base para imágenes
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/SIST V.4.0/PIT_V.4.0/";
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Inicio | Tutorado</title>

    <link rel="stylesheet" href="css/estilo.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<?php include("../includes_pit/sidebar_tutorado.php"); ?>


<div class="admin-container">

    <!--==================================
            HERO
    ===================================-->

    <section class="hero">

        <div class="hero-text">

            <div class="hero-top">

                <div class="hero-icon">
                    🎓
                </div>

                <div>

                    <h2 id="saludo">
                        Cargando saludo...
                    </h2>

                    <span class="badge-admin">

                        Portal del Tutorado

                    </span>

                </div>

            </div>

            <h1 class="titulo-sistema">

                Programa Institucional de Tutorías

            </h1>

            <p class="subtitulo">

                Bienvenido al Portal del Tutorado.<br>

                Consulta avisos, actividades y comunicados
                publicados por el Departamento de Desarrollo Académico.

            </p>

            <div class="fecha-hora">

                <span id="fecha"></span>

                <span id="hora"></span>

            </div>

        </div>

        <div class="hero-image">

            <img src="<?php echo $base_url;?>../assets_pit/alumnotutorado.jpeg">

        </div>

    </section>

    <section class="bienvenida-principal">

    <div class="bienvenida-icono">
        <i class="fa-solid fa-user-graduate"></i>
    </div>

    <div class="bienvenida-texto">

        <h2>
            Bienvenido al Portal del Tutorado
        </h2>

        <p>

            Este espacio ha sido diseñado para mantener una comunicación directa entre
            el Departamento de Desarrollo Académico y los estudiantes del
            Tecnológico de Estudios Superiores de Chalco.

        </p>

        <p>

            Desde esta página podrás consultar los avisos institucionales que sean
            publicados especialmente para los tutorados. Próximamente también podrás
            acceder a nuevas herramientas y servicios relacionados con tu proceso de
            tutoría académica mediante el menú principal del sistema.

        </p>

        <div class="mensaje-espera">

            <i class="fa-solid fa-bell"></i>

            <span>

                Mantente atento a esta sección, ya que aquí aparecerán los avisos
                importantes emitidos por tu tutor y por el Departamento de Desarrollo
                Académico.

            </span>

        </div>

    </div>

</section>
    



    <!--==================================
        AVISOS
===================================-->

<section class="avisos-section">

    <div class="avisos-header">

        <div>

            <h2>
                📢 Avisos para Tutorados
            </h2>

            <p>

                Consulta aquí los comunicados publicados por el
                Departamento de Desarrollo Académico.

            </p>

        </div>

    </div>

    <div class="grid-avisos">

<?php

$sql = "

SELECT DISTINCT a.*

FROM avisos a

INNER JOIN aviso_tipos at
ON a.id_aviso = at.id_aviso

INNER JOIN tipos t
ON at.id_tipo = t.id_tipo

WHERE

t.nombre='TUTORADO'

AND a.estado='ACTIVO'

ORDER BY

a.prioridad DESC,
a.fecha_publicacion DESC

";

$result = $conn->query($sql);

if($result && $result->num_rows>0){

while($row=$result->fetch_assoc()){

?>

<div class="aviso-card">

    <!-- CABECERA -->

    <div class="aviso-top">

    <div>

        <i class="fa-solid fa-bullhorn"></i>

        Aviso Institucional

    </div>

    <span class="fecha-aviso">

        <i class="fa-solid fa-calendar-days"></i>

        <?php echo date("d/m/Y",strtotime($row['fecha_publicacion'])); ?>

    </span>

</div>

    <!-- IMAGEN -->

<?php

if(!empty($row['imagen'])){

?>

<img
class="aviso-imagen"
src="../uploads/avisos/imagenes/<?php echo $row['imagen']; ?>">

<?php

}else{

?>

<img
class="aviso-imagen"
src="<?php echo $base_url; ?>assets_pit/tescha.png">

<?php

}

?>

<div class="aviso-contenido">

<h3>

<?php echo $row['titulo']; ?>

</h3>

<p>

<?php

if(!empty($row['descripcion'])){

echo nl2br($row['descripcion']);

}else{

echo "Este aviso no contiene descripción.";

}

?>

</p>

<div class="aviso-recursos">

<?php if(!empty($row['pdf'])){ ?>

<a

class="btn-pdf"

href="../uploads/avisos/pdfs/<?php echo $row['pdf']; ?>"

target="_blank">

<i class="fa-solid fa-file-pdf"></i>

Descargar PDF

</a>

<?php } ?>

<?php if(!empty($row['link'])){ ?>

<a

class="btn-link"

href="<?php echo $row['link']; ?>"

target="_blank">

<i class="fa-solid fa-link"></i>

Más información

</a>

<?php } ?>

</div>

</div>

</div>

<?php

}

}else{

?>

<div class="sin-avisos">

<i class="fa-regular fa-bell"></i>

<h3>

No existen avisos.

</h3>

<p>

Cuando el Departamento de Desarrollo Académico
publique información dirigida a los tutorados,
aparecerá aquí.

</p>

</div>

<?php

}

?>

    </div>

</section>


    <!--==================================
        FRASE
    ===================================-->

    <section class="frase">

        <p>

            "El éxito académico comienza con la constancia y el compromiso."

        </p>

    </section>

</div>

<?php include '../../includes/footer.php'; ?>

<script src="js/animaciones.js"></script>

</body>
</html>