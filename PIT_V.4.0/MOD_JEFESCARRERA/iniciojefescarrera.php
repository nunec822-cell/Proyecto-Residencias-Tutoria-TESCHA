
<?php
/*=========================================================
    PORTAL INSTITUCIONAL DE TUTORÍAS (PIT)
    Archivo: iniciojefescarrera.php
    Módulo: Jefatura de Carrera
=========================================================*/

session_start();


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
    VALIDAR ROL JEFE DE CARRERA
=========================================================*/

if (
    !in_array(
        "JEFE_CARRERA",
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
    ?? "Jefe de Carrera";

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
    OBTENER AVISOS PARA JEFE DE CARRERA
=========================================================*/

/*
    id_tipo = 3
    corresponde a JEFE_CARRERA
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

        t.id_tipo = 3

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
        Inicio | Jefe de Carrera
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
        ?>/includes_pit/sidebar_jefescarrera.css"
    >


    <!--=====================================================
        CSS INICIO
    ======================================================-->

    <link
        rel="stylesheet"
        href="jefes.css"
    >


</head>


<body>


<!--=========================================================
    SIDEBAR
==========================================================-->

<?php

include_once(
    "../includes_pit/sidebar_jefescarrera.php"
);

?>


<!--=========================================================
    CONTENIDO
==========================================================-->

<div class="jefes-contenido">


    <!--=====================================================
        BIENVENIDA
    ======================================================-->

    <main class="bienvenida-jefe">


        <!-- DECORACIONES -->

        <div
            class="decoracion decoracion-1"
        ></div>


        <div
            class="decoracion decoracion-2"
        ></div>


        <div
            class="decoracion decoracion-3"
        ></div>



        <!--=================================================
            ILUSTRACIÓN
        ==================================================-->

        <div class="ilustracion-jefe">


            <div class="icono-edificio">


                <span
                    class="ventana ventana-1"
                ></span>


                <span
                    class="ventana ventana-2"
                ></span>


                <span
                    class="ventana ventana-3"
                ></span>


                <span
                    class="ventana ventana-4"
                ></span>


                <span
                    class="puerta"
                ></span>


            </div>


            <div class="base-edificio"></div>


            <div class="persona persona-1">

                <span class="cabeza"></span>

                <span class="cuerpo"></span>

            </div>


            <div class="persona persona-2">

                <span class="cabeza"></span>

                <span class="cuerpo"></span>

            </div>


        </div>



        <!--=================================================
            TEXTO
        ==================================================-->

        <section class="texto-bienvenida">


            <span class="etiqueta-bienvenida">

                <span class="punto"></span>

                Portal Institucional de Tutorías

            </span>


            <h1>

                ¡Bienvenido,

                <span>

                    <?php

                    echo htmlspecialchars(
                        $nombre_completo
                    );

                    ?>

                </span>!

            </h1>


            <h2>
                Jefatura de Carrera
            </h2>


            <p>

                Es un gusto tenerte aquí. Desde este espacio
                podrás gestionar y dar seguimiento a las
                actividades relacionadas con tu carrera,
                contribuyendo al acompañamiento y desarrollo
                académico de los estudiantes.

            </p>


            <div class="mensaje-institucional">


                <div class="mensaje-icono">

                    <i class="fas fa-graduation-cap"></i>

                </div>


                <div>

                    <strong>

                        Tu trabajo impulsa el crecimiento académico

                    </strong>


                    <span>

                        Coordina, acompaña y fortalece el desarrollo
                        de tu comunidad estudiantil.

                    </span>

                </div>


            </div>


        </section>


    </main>



    <!--=====================================================
        AVISOS
    ======================================================-->

    <section class="seccion-avisos-jefe">


        <!--=================================================
            ENCABEZADO
        ==================================================-->

        <div class="encabezado-avisos-jefe">


            <div class="titulo-avisos-jefe">


                <div class="icono-titulo-avisos">

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

                Comunicados publicados por el departamento de desarrollo academico a jefatura de carrera 

            </p>


        </div>



        <!--=================================================
            CONTENEDOR AVISOS
        ==================================================-->

        <div class="contenedor-avisos-jefe">


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
                    TARJETA AVISO
                ==========================================-->

                <article
                    class="aviso-card-jefe prioridad-<?php
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

                        <div class="aviso-imagen-jefe">

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
                                    $aviso["titulo"]
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

                    <div class="aviso-contenido-jefe">


                        <!-- PRIORIDAD -->

                        <div class="aviso-meta-jefe">


                            <span
                                class="aviso-prioridad-jefe"
                            >

                                <?php

                                if (
                                    $aviso["prioridad"]
                                    === "ALTA"
                                ) {

                                    echo '<i class="fas fa-circle-exclamation"></i>';

                                }
                                elseif (
                                    $aviso["prioridad"]
                                    === "MEDIA"
                                ) {

                                    echo '<i class="fas fa-circle-info"></i>';

                                }
                                else {

                                    echo '<i class="fas fa-circle-check"></i>';

                                }

                                ?>


                                <?php

                                echo htmlspecialchars(
                                    $aviso["prioridad"]
                                );

                                ?>

                            </span>


                            <span
                                class="aviso-fecha-jefe"
                            >

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
                                $aviso["titulo"]
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
                                        $aviso["descripcion"]
                                    )
                                );

                                ?>

                            </p>

                        <?php

                        endif;

                        ?>



                        <!--=================================
                            BOTONES
                        ==================================-->

                        <div
                            class="aviso-acciones-jefe"
                        >


                            <!-- VER DETALLE -->

                            <button
                                type="button"
                                class="btn-ver-aviso-jefe"
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
                                            $aviso["link"]
                                        );
                                    ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn-link-aviso-jefe"
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
                                    class="btn-pdf-aviso-jefe"
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

                <div class="sin-avisos-jefe">


                    <div class="sin-avisos-icono">

                        <i class="far fa-bell-slash"></i>

                    </div>


                    <h3>

                        No hay avisos por el momento

                    </h3>


                    <p>

                        Actualmente no existen comunicados
                        publicados para Jefatura de Carrera.

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

    <section class="frase-institucional">


        <div class="linea"></div>


        <p>

            "El liderazgo académico comienza con el compromiso
            de acompañar y orientar a nuestra comunidad."

        </p>


        <div class="linea"></div>


    </section>


</div>



<!--=========================================================
    MODAL AVISO
==========================================================-->

<div
    id="modalAvisoJefe"
    class="modal-aviso-jefe"
>


    <div class="modal-contenido-jefe">


        <button
            type="button"
            id="cerrarModalAvisoJefe"
            class="modal-cerrar-jefe"
        >

            <i class="fas fa-xmark"></i>

        </button>


        <div class="modal-icono-jefe">

            <i class="fas fa-bullhorn"></i>

        </div>


        <span
            id="modalPrioridadJefe"
            class="modal-prioridad-jefe"
        ></span>


        <h2
            id="modalTituloJefe"
        ></h2>


        <div
            id="modalFechaJefe"
            class="modal-fecha-jefe"
        ></div>


        <div
            id="modalDescripcionJefe"
            class="modal-descripcion-jefe"
        ></div>


    </div>


</div>



<!--=========================================================
    JAVASCRIPT SIDEBAR
==========================================================-->

<script
    src="<?php
        echo $base_url;
    ?>/includes_pit/sidebar_jefescarrera.js"
></script>


<!--=========================================================
    JAVASCRIPT INICIO
==========================================================-->

<script src="jefes.js"></script>


</body>

</html>
<?php include '../../includes/footer.php'; ?>


