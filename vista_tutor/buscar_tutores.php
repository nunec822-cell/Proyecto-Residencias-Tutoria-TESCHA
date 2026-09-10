
<?php

/*=========================================================
    CONEXIÓN A LA BASE DE DATOS
=========================================================*/

/*
    ARCHIVO ACTUAL:

    C:\XAMPPP\htdocs\SIST V.4.0\vista_tutor\buscar_tutores.php


    ARCHIVO DE CONEXIÓN:

    C:\XAMPPP\htdocs\SIST V.4.0\PIT_V.4.0\base_pit\conect_pit.php
*/

require_once __DIR__ . "/../PIT_V.4.0/base_pit/conect_pit.php";


/*=========================================================
    HEADER
=========================================================*/

include __DIR__ . "/../includes/header.php";


/*=========================================================
    BÚSQUEDA
=========================================================*/

$busqueda = isset($_GET["buscar"])
    ? trim($_GET["buscar"])
    : "";


/*=========================================================
    CONSULTA BASE
=========================================================*/

$sql = "
SELECT DISTINCT

    p.id_personal,
    p.nombre,
    p.apellido_p,
    p.apellido_m,
    p.carrera,
    p.fotografia

FROM personal_academico p

INNER JOIN usuarios u
    ON p.id_usuario = u.id_usuario

INNER JOIN usuario_tipo ut
    ON u.id_usuario = ut.id_usuario

INNER JOIN tipos t
    ON ut.id_tipo = t.id_tipo

WHERE
    p.activo = 1
    AND u.activo = 1
    AND ut.activo = 1
    AND t.nombre = 'TUTOR'
";


/*=========================================================
    APLICAR BÚSQUEDA
=========================================================*/

if ($busqueda !== "") {

    $busqueda_sql = "%" . $conn->real_escape_string($busqueda) . "%";

    $sql .= "

    AND (

        p.nombre LIKE '$busqueda_sql'

        OR p.apellido_p LIKE '$busqueda_sql'

        OR p.apellido_m LIKE '$busqueda_sql'

        OR p.carrera LIKE '$busqueda_sql'

        OR CONCAT(
            p.nombre,
            ' ',
            p.apellido_p,
            ' ',
            p.apellido_m
        ) LIKE '$busqueda_sql'

        OR EXISTS (

            SELECT 1

            FROM tutorados tu

            WHERE
                tu.id_tutor = p.id_personal
                AND tu.activo = 1
                AND tu.matricula LIKE '$busqueda_sql'

        )

    )

    ";
}


/*=========================================================
    ORDEN
=========================================================*/

$sql .= "

ORDER BY
    p.apellido_p ASC,
    p.apellido_m ASC,
    p.nombre ASC

";


/*=========================================================
    EJECUTAR CONSULTA
=========================================================*/

$resultado = $conn->query($sql);


/*=========================================================
    CONTADOR
=========================================================*/

$total_tutores = 0;

if ($resultado) {

    $total_tutores = $resultado->num_rows;

}


/*=========================================================
    RUTA DE FOTOGRAFÍAS
=========================================================*/

/*
    LAS FOTOGRAFÍAS SE ENCUENTRAN EN:

    C:\XAMPPP\htdocs\SIST V.4.0\PIT_V.4.0\uploads\tutores\


    LA URL PÚBLICA ES:

    http://localhost/SIST%20V.4.0/PIT_V.4.0/uploads/tutores/


    LA BASE DE DATOS SOLO GUARDA EL NOMBRE:

    tutor_3_8b82770b72bcd16c.jpg
*/

$base_fotografias = "/SIST%20V.4.0/PIT_V.4.0/uploads/tutores/";

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Buscar Tutores | PIT
    </title>


    <!--=====================================================
        CSS
    ======================================================-->

    <link
        rel="stylesheet"
        href="css/estilo.css"
    >


    <!--=====================================================
        FONT AWESOME
    ======================================================-->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

</head>


<body>


<!--=======================================================
    CONTENEDOR PRINCIPAL
========================================================-->

<div class="contenedor-tutores">


    <!--===================================================
        ENCABEZADO
    ====================================================-->

    <header class="encabezado-tutores">

        <div class="icono-encabezado">

            <i class="fa-solid fa-user-tie"></i>

        </div>


        <div>

            <h1>
                Contacta a tu Tutor
            </h1>

            <p>
                Encuentra a tu Tutor Institucional
            </p>

        </div>

    </header>



    <!--===================================================
        BUSCADOR
    ====================================================-->

    <section class="buscador-section">


        <div class="buscador-titulo">

            <h2>

                <i class="fa-solid fa-magnifying-glass"></i>

                Buscar Tutor

            </h2>


            <p>

                Puedes buscar por nombre, apellidos, carrera
                o matrícula.

            </p>

        </div>



        <form
            method="GET"
            action="buscar_tutores.php"
            class="form-busqueda"
        >


            <div class="campo-busqueda">

                <i class="fa-solid fa-magnifying-glass"></i>


                <input
                    type="text"
                    name="buscar"
                    value="<?php echo htmlspecialchars($busqueda); ?>"
                    placeholder="Nombre, apellidos, carrera o matrícula..."
                    autocomplete="off"
                >

            </div>



            <button
                type="submit"
                class="btn-buscar"
            >

                <i class="fa-solid fa-search"></i>

                Buscar

            </button>



            <?php if ($busqueda !== "") { ?>

                <a
                    href="buscar_tutores.php"
                    class="btn-limpiar"
                >

                    <i class="fa-solid fa-xmark"></i>

                    Limpiar

                </a>

            <?php } ?>


        </form>



        <!--===============================================
            AYUDA
        ================================================-->

        <div class="ayuda-busqueda">


            <span>

                <i class="fa-solid fa-user"></i>

                Nombre

            </span>


            <span>

                <i class="fa-solid fa-id-card"></i>

                Matrícula

            </span>


            <span>

                <i class="fa-solid fa-graduation-cap"></i>

                Carrera

            </span>


        </div>


    </section>



    <!--===================================================
        RESULTADOS
    ====================================================-->

    <section class="resultados-section">


        <div class="resultados-header">


            <div>


                <h2>

                    <?php

                    if ($busqueda !== "") {

                        echo "Resultados de búsqueda";

                    } else {

                        echo "Tutores Institucionales";

                    }

                    ?>

                </h2>



                <?php if ($busqueda !== "") { ?>


                    <p>

                        Resultados para:

                        <strong>

                            "<?php echo htmlspecialchars($busqueda); ?>"

                        </strong>

                    </p>


                <?php } else { ?>


                    <p>

                        Consulta la información de los Tutores Institucionales.

                    </p>


                <?php } ?>


            </div>



            <!--=============================================
                CONTADOR
            ==============================================-->

            <div class="contador-tutores">

                <i class="fa-solid fa-users"></i>

                <?php echo $total_tutores; ?>

                <?php

                echo $total_tutores == 1
                    ? " Tutor"
                    : " Tutores";

                ?>

            </div>


        </div>



        <!--=================================================
            TARJETAS DE TUTORES
        ==================================================-->

        <?php if ($resultado && $resultado->num_rows > 0) { ?>


            <div class="grid-tutores">


                <?php while ($tutor = $resultado->fetch_assoc()) { ?>


                    <?php

                    /*=========================================
                        NOMBRE COMPLETO
                    =========================================*/

                    $nombre_completo = trim(

                        $tutor["nombre"] . " " .

                        $tutor["apellido_p"] . " " .

                        $tutor["apellido_m"]

                    );


                    /*=========================================
                        FOTOGRAFÍA
                    =========================================*/

                    $fotografia = trim(
                        $tutor["fotografia"] ?? ""
                    );


                    $foto_url = "";


                    if ($fotografia !== "") {

                        /*
                            La BD guarda solamente:

                            tutor_3_8b82770b72bcd16c.jpg

                            Por lo tanto se agrega directamente
                            a la URL de fotografías.
                        */

                        $foto_url =
                            $base_fotografias .
                            rawurlencode($fotografia);

                    }

                    ?>


                    <article class="tarjeta-tutor">


                        <!--=================================
                            FOTO
                        ==================================-->

                        <div class="foto-tutor">


                            <?php if ($foto_url !== "") { ?>


                                <img
                                    src="<?php echo htmlspecialchars($foto_url); ?>"
                                    alt="Fotografía de <?php echo htmlspecialchars($nombre_completo); ?>"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >


                                <!--
                                    ESTE CONTENEDOR APARECE
                                    SI LA IMAGEN NO CARGA.
                                -->

                                <div
                                    class="sin-fotografia"
                                    style="display:none;"
                                >

                                    <i class="fa-solid fa-user-tie"></i>

                                    <span>

                                        Fotografía no disponible

                                    </span>

                                </div>


                            <?php } else { ?>


                                <div class="sin-fotografia">

                                    <i class="fa-solid fa-user-tie"></i>

                                    <span>

                                        Fotografía no disponible

                                    </span>

                                </div>


                            <?php } ?>


                        </div>



                        <!--=================================
                            INFORMACIÓN
                        ==================================-->

                        <div class="informacion-tutor">


                            <div class="etiqueta-tutor">

                                <i class="fa-solid fa-shield-halved"></i>

                                Tutor Institucional

                            </div>



                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $nombre_completo
                                );

                                ?>

                            </h3>



                            <div class="dato-tutor">


                                <i class="fa-solid fa-graduation-cap"></i>


                                <div>


                                    <small>

                                        Carrera

                                    </small>


                                    <span>

                                        <?php

                                        echo htmlspecialchars(
                                            $tutor["carrera"]
                                        );

                                        ?>

                                    </span>


                                </div>


                            </div>


                        </div>


                    </article>


                <?php } ?>


            </div>


        <?php } else { ?>


            <!--=================================================
                SIN RESULTADOS
            ==================================================-->

            <div class="sin-resultados">


                <div class="sin-resultados-icono">

                    <i class="fa-solid fa-user-slash"></i>

                </div>


                <h3>

                    No se encontraron tutores

                </h3>



                <?php if ($busqueda !== "") { ?>


                    <p>

                        No encontramos un Tutor Institucional
                        relacionado con:

                        <strong>

                            "<?php echo htmlspecialchars($busqueda); ?>"

                        </strong>

                    </p>


                <?php } else { ?>


                    <p>

                        Actualmente no hay Tutores Institucionales registrados.

                    </p>


                <?php } ?>



                <?php if ($busqueda !== "") { ?>


                    <a
                        href="buscar_tutores.php"
                        class="btn-ver-todos"
                    >

                        <i class="fa-solid fa-users"></i>

                        Ver todos los tutores

                    </a>


                <?php } ?>


            </div>


        <?php } ?>


    </section>



    <!--===================================================
        INFORMACIÓN
    ====================================================-->

    <section class="informacion-contacto">


        <div class="informacion-icono">

            <i class="fa-solid fa-circle-info"></i>

        </div>



        <div>


            <h3>

                ¿No sabes quién es tu Tutor?

            </h3>



            <p>

                Introduce tu <strong>matrícula</strong> en el
                buscador y el sistema mostrará al Tutor
                Institucional que tienes asignado.

            </p>


        </div>


    </section>


</div>


</body>

</html>



<?php

/*=========================================================
    FOOTER
=========================================================*/

include __DIR__ . "/../includes/footer.php";

?>







