
<?php
/*=========================================================
    PORTAL INSTITUCIONAL DE TUTORÍAS (PIT)
    Archivo: inicio.php
    Módulo: Directivos
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../base_pit/conect_pit.php");


/*=========================================================
    VALIDAR SESIÓN
=========================================================*/

if (
    !isset($_SESSION["id_usuario"]) ||
    !isset($_SESSION["roles"])
) {

    header("Location: ../indexloguin.php");
    exit();

}


/*=========================================================
    VALIDAR ROL DIRECTIVO
=========================================================*/

if (
    !in_array(
        "DIRECTIVO",
        $_SESSION["roles"]
    )
) {

    header("Location: ../indexloguin.php");
    exit();

}


/*=========================================================
    DATOS DEL USUARIO
=========================================================*/

$nombre = $_SESSION["nombre"]
    ?? $_SESSION["usuario"]
    ?? "Directivo";

$apellido_p = $_SESSION["apellido_p"] ?? "";

$apellido_m = $_SESSION["apellido_m"] ?? "";


$nombre_completo = trim(
    $nombre . " " .
    $apellido_p . " " .
    $apellido_m
);


/*=========================================================
    RUTA BASE
=========================================================*/

$base_url =
    "http://" .
    $_SERVER["HTTP_HOST"] .
    "/SIST V.4.0/PIT_V.4.0";


/*=========================================================
    OBTENER AVISOS PARA DIRECTIVOS
=========================================================*/

/*
    id_tipo = 2
    corresponde a DIRECTIVO
*/

$sqlAvisos = "

    SELECT DISTINCT

        a.id_aviso,
        a.titulo,
        a.descripcion,
        a.link,
        a.imagen,
        a.pdf,
        a.estado,
        a.fecha_publicacion,
        a.fecha_expiracion,
        a.prioridad

    FROM avisos a

    INNER JOIN aviso_tipos at
        ON a.id_aviso = at.id_aviso

    INNER JOIN tipos t
        ON at.id_tipo = t.id_tipo

    WHERE

        t.id_tipo = 2

        AND a.estado = 'ACTIVO'

        AND (
            a.fecha_expiracion IS NULL
            OR a.fecha_expiracion >= NOW()
        )

    ORDER BY

        CASE a.prioridad

            WHEN 'ALTA' THEN 1

            WHEN 'MEDIA' THEN 2

            WHEN 'BAJA' THEN 3

            ELSE 4

        END,

        a.fecha_publicacion DESC

";


$resultadoAvisos = $conn->query(
    $sqlAvisos
);

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
        Inicio | Directivos
    </title>


    <!--=====================================================
        FONT AWESOME
    ======================================================-->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >


    <!--=====================================================
        CSS SIDEBAR
    ======================================================-->

    <link
        rel="stylesheet"
        href="<?php
            echo $base_url;
        ?>/includes_pit/sidebar_directivos.css"
    >


    <!--=====================================================
        CSS INICIO
    ======================================================-->

   <link rel="stylesheet" href="css/inicio.css">

</head>


<body>


<!--=========================================================
    SIDEBAR DIRECTIVOS
==========================================================-->

<?php

include_once(
    "../includes_pit/sidebar_directivos.php"
);

?>


<!--=========================================================
    CONTENIDO PRINCIPAL
==========================================================-->

<div class="directivos-contenido">


    <!--=====================================================
        BIENVENIDA
    ======================================================-->

    <main class="bienvenida-directivo">


        <!--=================================================
            DECORACIONES
        ==================================================-->

        <div
            class="decoracion-directivo decoracion-directivo-1"
        ></div>


        <div
            class="decoracion-directivo decoracion-directivo-2"
        ></div>


        <div
            class="decoracion-directivo decoracion-directivo-3"
        ></div>


        <!--=================================================
            ILUSTRACIÓN
        ==================================================-->

        <div class="ilustracion-directivo">


            <!-- EDIFICIO -->

            <div class="edificio-directivo">


                <div class="techo-directivo"></div>


                <div class="edificio-cuerpo-directivo">


                    <span class="ventana-directivo ventana-d-1"></span>

                    <span class="ventana-directivo ventana-d-2"></span>

                    <span class="ventana-directivo ventana-d-3"></span>

                    <span class="ventana-directivo ventana-d-4"></span>


                    <span class="puerta-directivo"></span>


                </div>


            </div>


            <!-- PERSONAS -->

            <div class="persona-directivo persona-d-1">

                <span class="cabeza-directivo"></span>

                <span class="cuerpo-directivo cuerpo-d-1"></span>

            </div>


            <div class="persona-directivo persona-d-2">

                <span class="cabeza-directivo"></span>

                <span class="cuerpo-directivo cuerpo-d-2"></span>

            </div>


            <div class="persona-directivo persona-d-3">

                <span class="cabeza-directivo"></span>

                <span class="cuerpo-directivo cuerpo-d-3"></span>

            </div>


        </div>


        <!--=================================================
            TEXTO DE BIENVENIDA
        ==================================================-->

        <section class="texto-bienvenida-directivo">


            <span class="etiqueta-bienvenida-directivo">

                <span class="punto-directivo"></span>

                Portal Institucional de Tutorías

            </span>


            <h1>

                ¡Bienvenido,

                <span>

                    <?php

                    echo htmlspecialchars(
                        $nombre_completo,
                        ENT_QUOTES,
                        "UTF-8"
                    );

                    ?>

                </span>!

            </h1>


            <h2>

                Dirección Institucional

            </h2>


            <p>

                Es un gusto tenerte en el Portal Institucional
                de Tutorías. Desde este espacio podrás consultar
                información institucional, comunicados y avisos
                importantes dirigidos especialmente al personal
                directivo.

            </p>


            <!--=================================================
                MENSAJE INSTITUCIONAL
            ==================================================-->

            <div class="mensaje-institucional-directivo">


                <div class="mensaje-icono-directivo">

                    <i class="fas fa-building-columns"></i>

                </div>


                <div>

                    <strong>

                        Información para la toma de decisiones

                    </strong>


                    <span>

                        Consulta oportunamente los comunicados
                        institucionales destinados al personal
                        directivo.

                    </span>

                </div>


            </div>


        </section>


    </main>


    <!--=====================================================
        SECCIÓN DE AVISOS
    ======================================================-->

    <section class="seccion-avisos-directivo">


        <!--=================================================
            ENCABEZADO
        ==================================================-->

        <div class="encabezado-avisos-directivo">


            <div class="titulo-avisos-directivo">


                <div class="icono-titulo-avisos-directivo">

                    <i class="fas fa-bullhorn"></i>

                </div>


                <div>

                    <span>

                        INFORMACIÓN INSTITUCIONAL

                    </span>


                    <h2>

                        Avisos importantes

                    </h2>

                </div>


            </div>


            <p>

                Comunicados publicados por el Departamento
                de Desarrollo Académico dirigidos al personal
                directivo.

            </p>


        </div>


        <!--=================================================
            CONTENEDOR DE AVISOS
        ==================================================-->

        <div class="contenedor-avisos-directivo">


            <?php

            if (
                $resultadoAvisos &&
                $resultadoAvisos->num_rows > 0
            ):

                while (
                    $aviso =
                    $resultadoAvisos->fetch_assoc()
                ):

            ?>


                <!--=========================================
                    TARJETA
                ==========================================-->

                <article
                    class="aviso-card-directivo prioridad-<?php

                        echo strtolower(
                            $aviso["prioridad"]
                        );

                    ?>"
                >


                    <!--=====================================
                        IMAGEN
                    ======================================-->

                    <?php

                    if (
                        !empty(
                            $aviso["imagen"]
                        )
                    ):

                    ?>

                        <div class="aviso-imagen-directivo">

                            <img
                                src="<?php

                                echo $base_url .
                                    "/uploads/avisos/imagenes/" .
                                    rawurlencode(
                                        $aviso["imagen"]
                                    );

                                ?>"
                                alt="<?php

                                echo htmlspecialchars(
                                    $aviso["titulo"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                                ?>"
                            >

                        </div>

                    <?php

                    endif;

                    ?>


                    <!--=====================================
                        CONTENIDO
                    ======================================-->

                    <div class="aviso-contenido-directivo">


                        <!-- META -->

                        <div class="aviso-meta-directivo">


                            <span class="aviso-prioridad-directivo">


                                <?php

                                if (
                                    $aviso["prioridad"]
                                    === "ALTA"
                                ) {

                                    echo
                                        '<i class="fas fa-circle-exclamation"></i>';

                                }
                                elseif (
                                    $aviso["prioridad"]
                                    === "MEDIA"
                                ) {

                                    echo
                                        '<i class="fas fa-circle-info"></i>';

                                }
                                else {

                                    echo
                                        '<i class="fas fa-circle-check"></i>';

                                }

                                ?>


                                <?php

                                echo htmlspecialchars(
                                    $aviso["prioridad"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                                ?>

                            </span>


                            <span class="aviso-fecha-directivo">

                                <i class="far fa-calendar"></i>

                                <?php

                                echo date(
                                    "d/m/Y",
                                    strtotime(
                                        $aviso[
                                            "fecha_publicacion"
                                        ]
                                    )
                                );

                                ?>

                            </span>


                        </div>


                        <!-- TÍTULO -->

                        <h3>

                            <?php

                            echo htmlspecialchars(
                                $aviso["titulo"],
                                ENT_QUOTES,
                                "UTF-8"
                            );

                            ?>

                        </h3>


                        <!-- DESCRIPCIÓN -->

                        <?php

                        if (
                            !empty(
                                $aviso["descripcion"]
                            )
                        ):

                        ?>

                            <p>

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $aviso["descripcion"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    )
                                );

                                ?>

                            </p>

                        <?php

                        endif;

                        ?>


                        <!--=================================
                            ACCIONES
                        ==================================-->

                        <div class="aviso-acciones-directivo">


                            <!-- VER AVISO -->

                            <button
                                type="button"
                                class="btn-ver-aviso-directivo"

                                data-titulo="<?php

                                    echo htmlspecialchars(
                                        $aviso["titulo"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                ?>"

                                data-descripcion="<?php

                                    echo htmlspecialchars(
                                        $aviso["descripcion"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                ?>"

                                data-prioridad="<?php

                                    echo htmlspecialchars(
                                        $aviso["prioridad"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                ?>"

                                data-fecha="<?php

                                    echo date(
                                        "d/m/Y H:i",
                                        strtotime(
                                            $aviso[
                                                "fecha_publicacion"
                                            ]
                                        )
                                    );

                                ?>"
                            >

                                <i class="fas fa-eye"></i>

                                Ver aviso

                            </button>


                            <!-- LINK -->

                            <?php

                            if (
                                !empty(
                                    $aviso["link"]
                                )
                            ):

                            ?>

                                <a
                                    href="<?php

                                        echo htmlspecialchars(
                                            $aviso["link"],
                                            ENT_QUOTES,
                                            "UTF-8"
                                        );

                                    ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn-link-aviso-directivo"
                                >

                                    <i class="fas fa-link"></i>

                                    Abrir enlace

                                </a>

                            <?php

                            endif;
                            ?>


                            <!-- PDF -->

                            <?php

                            if (
                                !empty(
                                    $aviso["pdf"]
                                )
                            ):

                            ?>

                                <a
                                    href="<?php

                                        echo $base_url .
                                            "/uploads/avisos/pdfs/" .
                                            rawurlencode(
                                                $aviso["pdf"]
                                            );

                                    ?>"
                                    target="_blank"
                                    class="btn-pdf-aviso-directivo"
                                >

                                    <i class="fas fa-file-pdf"></i>

                                    Ver PDF

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


                <!--=========================================
                    SIN AVISOS
                ==========================================-->

                <div class="sin-avisos-directivo">


                    <div class="sin-avisos-icono-directivo">

                        <i class="far fa-bell-slash"></i>

                    </div>


                    <h3>

                        No hay avisos por el momento

                    </h3>


                    <p>

                        Actualmente no existen comunicados
                        publicados para el personal directivo.

                    </p>


                </div>


            <?php

            endif;

            ?>


        </div>


    </section>


    <!--=====================================================
        FRASE INSTITUCIONAL
    ======================================================-->

    <section class="frase-institucional-directivo">


        <div class="linea-directivo"></div>


        <p>

            "La visión institucional se fortalece cuando
            la información llega a quienes tienen la
            responsabilidad de transformar."

        </p>


        <div class="linea-directivo"></div>


    </section>


</div>


<!--=========================================================
    MODAL AVISO
==========================================================-->

<div
    id="modalAvisoDirectivo"
    class="modal-aviso-directivo"
>


    <div class="modal-contenido-directivo">


        <button
            type="button"
            id="cerrarModalAvisoDirectivo"
            class="modal-cerrar-directivo"
        >

            <i class="fas fa-xmark"></i>

        </button>


        <div class="modal-icono-directivo">

            <i class="fas fa-bullhorn"></i>

        </div>


        <span
            id="modalPrioridadDirectivo"
            class="modal-prioridad-directivo"
        ></span>


        <h2
            id="modalTituloDirectivo"
        ></h2>


        <div
            id="modalFechaDirectivo"
            class="modal-fecha-directivo"
        ></div>


        <div
            id="modalDescripcionDirectivo"
            class="modal-descripcion-directivo"
        ></div>


    </div>


</div>


<!--=========================================================
    JAVASCRIPT SIDEBAR
==========================================================-->

<script
    src="<?php
        echo $base_url;
    ?>/includes_pit/sidebar_directivos.js"
></script>


<!--=========================================================
    JAVASCRIPT INICIO
==========================================================-->

<script src="directivos.js"></script>


</body>

</html>


<?php

include '../../includes/footer.php';

?>



