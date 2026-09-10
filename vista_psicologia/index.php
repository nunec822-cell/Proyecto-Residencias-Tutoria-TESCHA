<?php
/*=========================================================
        CONFIGURACIÓN
=========================================================*/

$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/SIST V.4.0";


/*=========================================================
        CONEXIÓN A BASE DE DATOS
=========================================================*/

require_once("../PIT_V.4.0/base_pit/conect_pit.php");


/*=========================================================
        OBTENER AVISOS PUBLICADOS
=========================================================*/

$sqlAvisos = "

    SELECT
        id_aviso,
        id_psicologo,
        titulo,
        descripcion,
        imagen,
        pdf,
        enlace,
        categoria,
        estado,
        fecha_publicacion

    FROM avisos_psicologia

    WHERE estado = 'PUBLICADO'

    ORDER BY fecha_publicacion DESC

";

$resultadoAvisos = $conn->query($sqlAvisos);

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Departamento de Psicología | TESCHA
</title>


<!--==================================================
                    GOOGLE FONTS
===================================================-->

<link
rel="preconnect"
href="https://fonts.googleapis.com">

<link
rel="preconnect"
href="https://fonts.gstatic.com"
crossorigin>

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
rel="stylesheet">


<!--==================================================
                    FONT AWESOME
===================================================-->

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<!--==================================================
                    CSS PRINCIPAL
===================================================-->

<link
rel="stylesheet"
href="<?= $base_url ?>/vista_psicologia/css/estilo.css">

</head>


<body>


<!--==================================================
                    HEADER
===================================================-->

<?php include("../includes/header.php"); ?>


<div class="separador"></div>


<!--==================================================
                    SIDEBAR
===================================================-->

<?php include("../includes/sidebar-sist.php"); ?>


<!--==================================================
                    HERO
===================================================-->

<main class="hero">

    <div class="hero-texto">

        <span class="badge">

            Departamento de Psicología

        </span>


        <h1>

            Tu bienestar emocional
            también forma parte
            de tu éxito académico.

        </h1>


        <p>

            Bienvenido al espacio de acompañamiento psicológico
            del Tecnológico de Estudios Superiores de Chalco.

        </p>


        <!-- BOTÓN HERO -->

        <a
        href="#avisos"
        class="hero-boton">

            <i class="fa-solid fa-bullhorn"></i>

            Conoce nuestros avisos

        </a>

    </div>


    <div class="hero-imagen">

        <div class="circulo circulo1"></div>

        <div class="circulo circulo2"></div>

        <div class="circulo circulo3"></div>


        <img
        src="<?= $base_url ?>/vista_psicologia/img/hero2.png"
        alt="Departamento de Psicología">

    </div>

</main>



<!--==================================================
                ESPACIO SEGURO
===================================================-->

<section class="espacio-seguro">

    <div class="espacio-grid">


        <!--========================================
                    IMAGEN
        =========================================-->

        <div class="panel-imagen">

            <div class="circulo c1"></div>

            <div class="circulo c2"></div>

            <div class="circulo c3"></div>


            <img
            src="<?= $base_url ?>/vista_psicologia/img/bienvenida.jpg"
            alt="Departamento de Psicología">

        </div>



        <!--========================================
                    TEXTO
        =========================================-->

        <div class="panel-texto">

            <span class="etiqueta">

                Departamento de Psicología

            </span>


            <h2>

                Bienvenido a un
                <br>
                espacio seguro

            </h2>


            <p>

                En el Departamento de Psicología del
                <strong>TESCHA</strong>, cuidamos tu bienestar
                emocional como base para tu desarrollo académico
                y personal.

            </p>


            <p>

                Encontrarás un espacio de confianza, escucha y
                acompañamiento para crecer durante tu trayectoria
                universitaria.

            </p>


            <div class="beneficios">

                <div>

                    ❤️

                    <span>
                        Apoyo
                    </span>

                </div>


                <div>

                    🌱

                    <span>
                        Bienestar
                    </span>

                </div>


                <div>

                    💬

                    <span>
                        Escucha
                    </span>

                </div>

            </div>

        </div>

    </div>

</section>



<!--==================================================
                AVISOS
===================================================-->

<section
class="seccion-avisos"
id="avisos">


    <!--==============================================
                    ENCABEZADO
    ===============================================-->

    <div class="avisos-encabezado">

        <div class="avisos-titulo">

            <span class="avisos-etiqueta">

                <i class="fa-solid fa-bullhorn"></i>

                Comunicación institucional

            </span>


            <h2>

                Avisos del Departamento

            </h2>


            <p>

                Mantente informado sobre talleres, cursos,
                campañas, conferencias y actividades del
                Departamento de Psicología.

            </p>

        </div>


        <div class="avisos-decoracion">

            <i class="fa-solid fa-bell"></i>

        </div>

    </div>



    <!--==============================================
                    CONTENEDOR DE AVISOS
    ===============================================-->

    <div class="avisos-publicos">


        <?php

        if (
            $resultadoAvisos &&
            $resultadoAvisos->num_rows > 0
        ):

            while (
                $aviso = $resultadoAvisos->fetch_assoc()
            ):

        ?>


        <!--========================================
                    TARJETA
        =========================================-->

        <article class="aviso-publico-card">


            <!--====================================
                        IMAGEN
            =====================================-->

            <div class="aviso-publico-imagen">


                <?php

                if (!empty($aviso["imagen"])):

                ?>

                    <img
                    src="<?= $base_url ?>/PIT_V.4.0/MOD_PSICOLOGOS/uploads/imagenes/<?= htmlspecialchars($aviso["imagen"]) ?>"
                    alt="<?= htmlspecialchars($aviso["titulo"]) ?>">

                <?php

                else:

                ?>

                    <div class="aviso-sin-imagen">

                        <i class="fa-solid fa-heart-pulse"></i>

                        <span>
                            Psicología TESCHA
                        </span>

                    </div>

                <?php

                endif;

                ?>


                <!-- CATEGORÍA -->

                <span class="aviso-categoria">

                    <?= htmlspecialchars(
                        $aviso["categoria"]
                    ) ?>

                </span>


            </div>



            <!--====================================
                        CONTENIDO
            =====================================-->

            <div class="aviso-publico-contenido">


                <!-- FECHA -->

                <div class="aviso-fecha">

                    <i class="fa-regular fa-calendar"></i>

                    <?= date(
                        "d/m/Y",
                        strtotime(
                            $aviso["fecha_publicacion"]
                        )
                    ) ?>

                </div>



                <!-- TÍTULO -->

                <h3>

                    <?= htmlspecialchars(
                        $aviso["titulo"]
                    ) ?>

                </h3>



                <!-- DESCRIPCIÓN -->

                <p>

                    <?= nl2br(
                        htmlspecialchars(
                            $aviso["descripcion"]
                        )
                    ) ?>

                </p>



                <!--================================
                        ACCIONES
                =================================-->

                <div class="aviso-acciones">


                    <?php

                    if (!empty($aviso["pdf"])):

                    ?>

                    <a
                    href="<?= $base_url ?>/PIT_V.4.0/MOD_PSICOLOGOS/uploads/imagenes/pdf/<?= htmlspecialchars($aviso["pdf"]) ?>"
                    target="_blank"
                    class="btn-aviso btn-pdf-publico">

                        <i class="fa-solid fa-file-pdf"></i>

                        Ver PDF

                    </a>

                    <?php

                    endif;

                    ?>


                    <?php

                    if (!empty($aviso["enlace"])):

                    ?>

                    <a
                    href="<?= htmlspecialchars(
                        $aviso["enlace"]
                    ) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="btn-aviso btn-enlace-publico">

                        <i class="fa-solid fa-arrow-up-right-from-square"></i>

                        Ver información

                    </a>

                    <?php

                    endif;

                    ?>

                </div>

            </div>

        </article>


        <?php

            endwhile;

        else:

        ?>


        <!--========================================
                    SIN AVISOS
        =========================================-->

        <div class="sin-avisos-publicos">

            <div class="sin-avisos-icono">

                <i class="fa-regular fa-bell-slash"></i>

            </div>


            <h3>

                Por el momento no hay avisos

            </h3>


            <p>

                Aquí aparecerá la información importante
                del Departamento de Psicología.

            </p>

        </div>


        <?php

        endif;

        ?>

    </div>

</section>



<!--==================================================
                AGENDAR CITA
===================================================-->

<section class="seccion-agendar">


    <div class="agendar-contenedor">


        <!--========================================
                    DECORACIÓN
        =========================================-->

        <div class="agendar-decoracion decoracion-1"></div>

        <div class="agendar-decoracion decoracion-2"></div>



        <!--========================================
                    ICONO
        =========================================-->

        <div class="agendar-icono-grande">

            <i class="fa-solid fa-calendar-check"></i>

        </div>



        <!--========================================
                    CONTENIDO
        =========================================-->

        <div class="agendar-contenido">


            <span class="agendar-etiqueta">

                <i class="fa-solid fa-heart"></i>

                Departamento de Psicología

            </span>


            <h2>

                ¿Necesitas hablar con alguien?

            </h2>


            <h3>

                Agenda tu cita psicológica

            </h3>


            <p>

                Tu bienestar emocional es importante.
                Solicita una cita con uno de nuestros
                psicólogos y encuentra un espacio de
                confianza, escucha y acompañamiento.

            </p>



            <!-- BENEFICIOS -->

            <div class="agendar-beneficios">


                <div>

                    <i class="fa-solid fa-shield-heart"></i>

                    <span>
                        Espacio seguro
                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-user-doctor"></i>

                    <span>
                        Atención psicológica
                    </span>

                </div>


                <div>

                    <i class="fa-solid fa-calendar-days"></i>

                    <span>
                        Horarios disponibles
                    </span>

                </div>

            </div>



            <!--====================================
                        BOTÓN
            =====================================-->

            <a
            href="<?= $base_url ?>/PIT_V.4.0/MOD_PSICOLOGOS/solicitud_cita/index.php"
            class="btn-agendar">

                <span>

                    <i class="fa-solid fa-calendar-plus"></i>

                    Agendar mi cita

                </span>


                <i
                class="fa-solid fa-arrow-right flecha">
                </i>

            </a>


            <small class="agendar-nota">

                Puedes consultar los horarios disponibles
                y elegir el espacio que mejor se adapte a ti.

            </small>

        </div>

    </div>

</section>



<!--==================================================
                    FRASE FINAL
===================================================-->

<section class="frase">

    <i class="fa-solid fa-quote-left"></i>

    <span>

        La orientación oportuna puede cambiar
        el rumbo de una vida.

    </span>

    <i class="fa-solid fa-quote-right"></i>

</section>



<!--==================================================
                    FOOTER
===================================================-->

<?php include("../includes/footer.php"); ?>



<!--==================================================
                    JAVASCRIPT
===================================================-->

<script
src="<?= $base_url ?>/vista_psicologia/js/animaciones.js">
</script>


</body>

</html>