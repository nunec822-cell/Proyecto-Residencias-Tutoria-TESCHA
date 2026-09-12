<?php
/*=========================================================
    AGENDA DEL PSICÓLOGO
    SIST V.4.0 - PIT V.4.0
=========================================================*/


/*=========================================================
    SESIÓN
=========================================================*/

require_once("../../sesion.php");


/*=========================================================
    VALIDAR ROL
=========================================================*/

if (
    !isset($_SESSION["roles"]) ||
    !in_array("PSICOLOGO", $_SESSION["roles"])
) {

    header("Location: ../../indexloguin.php");
    exit();

}


/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../../base_pit/conect_pit.php");


/*=========================================================
    OBTENER ID DEL USUARIO
=========================================================*/

$id_usuario = (int) $_SESSION["id_usuario"];


/*=========================================================
    OBTENER ID DEL PSICÓLOGO
=========================================================

    La sesión contiene:

        id_usuario

    La tabla administradores_psicologos contiene:

        id_registro

    Y disponibilidad_psicologos utiliza:

        id_psicologo

    Por lo tanto:

        usuarios.id_usuario
                ↓
        administradores_psicologos.id_usuario
                ↓
        administradores_psicologos.id_registro
                ↓
        disponibilidad_psicologos.id_psicologo

=========================================================*/

$sql_psicologo = "

    SELECT
        id_registro,
        nombre,
        apellido_p,
        apellido_m

    FROM administradores_psicologos

    WHERE id_usuario = ?
    AND activo = 1

    LIMIT 1

";


$stmt_psicologo =
    $conn->prepare($sql_psicologo);


if (!$stmt_psicologo) {

    die(
        "Error al preparar la consulta del psicólogo: "
        . $conn->error
    );

}


$stmt_psicologo->bind_param(
    "i",
    $id_usuario
);


$stmt_psicologo->execute();


$resultado_psicologo =
    $stmt_psicologo->get_result();


/*=========================================================
    VERIFICAR PERFIL DE PSICÓLOGO
=========================================================*/

if (
    $resultado_psicologo->num_rows === 0
) {

    $stmt_psicologo->close();

    die(
        "No se encontró un perfil de psicólogo asociado al usuario actual."
    );

}


$datos_psicologo =
    $resultado_psicologo->fetch_assoc();


$stmt_psicologo->close();


/*=========================================================
    ID REAL DEL PSICÓLOGO
=========================================================*/

$id_psicologo =
    (int) $datos_psicologo["id_registro"];


/*=========================================================
    NOMBRE DEL PSICÓLOGO
=========================================================*/

$nombre_psicologo =
    trim(
        $datos_psicologo["nombre"]
        . " "
        . $datos_psicologo["apellido_p"]
        . " "
        . $datos_psicologo["apellido_m"]
    );


/*=========================================================
    CONSULTAR DISPONIBILIDADES
=========================================================*/

$sql = "

    SELECT
        id_disponibilidad,
        fecha,
        hora_inicio,
        hora_fin,
        estado

    FROM disponibilidad_psicologos

    WHERE id_psicologo = ?

    ORDER BY
        fecha ASC,
        hora_inicio ASC

";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    die(
        "Error al preparar la consulta: "
        . $conn->error
    );

}


$stmt->bind_param(
    "i",
    $id_psicologo
);


$stmt->execute();


$resultado =
    $stmt->get_result();


/*=========================================================
    CONTADORES
=========================================================*/

$total_disponibilidades = 0;

$total_disponibles = 0;

$total_cerrados = 0;


$disponibilidades = [];


while (
    $fila = $resultado->fetch_assoc()
) {

    $disponibilidades[] = $fila;

    $total_disponibilidades++;


    if (
        $fila["estado"] === "DISPONIBLE"
    ) {

        $total_disponibles++;

    } else {

        $total_cerrados++;

    }

}


$stmt->close();


/*=========================================================
    FECHA ACTUAL
=========================================================*/

$fecha_actual = date("Y-m-d");

?>
<?php include("../../includes_pit/sidebar_psicologos.php"); ?>
<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- CONTENEDOR PRINCIPAL -->
<div class="separador"></div>
<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Mi agenda | Psicología
    </title>


    <!--=====================================================
        CSS
    ======================================================-->

    <link
        rel="stylesheet"
        href="css/agenda.css"
    >

</head>


<body>


<!--=========================================================
    CONTENEDOR PRINCIPAL
=========================================================-->

<main class="agenda-container">


    <!--=====================================================
        ENCABEZADO
    ======================================================-->

    <header class="agenda-header">


        <div class="agenda-header-text">


            <div class="agenda-icon">

                📅

            </div>


            <div>

                <h1>
                    Mi agenda
                </h1>


                <p>

                    Hola,
                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $nombre_psicologo
                        );
                        ?>
                    </strong>

                    . Administra tus espacios disponibles.

                </p>

            </div>


        </div>



        <!--=================================================
            NUEVA DISPONIBILIDAD
        ==================================================-->

        <a
            href="registrar.php"
            class="btn-nueva"
        >

            <span>
                +
            </span>

            Nueva disponibilidad

        </a>


    </header>



    <!--=====================================================
        RESUMEN
    ======================================================-->

    <section class="agenda-resumen">


        <!--=================================================
            TOTAL
        ==================================================-->

        <article class="resumen-card">


            <div class="resumen-icon">

                📅

            </div>


            <div>

                <span>
                    Total de horarios
                </span>


                <strong>

                    <?php
                    echo $total_disponibilidades;
                    ?>

                </strong>

            </div>


        </article>



        <!--=================================================
            DISPONIBLES
        ==================================================-->

        <article class="resumen-card disponible">


            <div class="resumen-icon">

                🟢

            </div>


            <div>

                <span>
                    Disponibles
                </span>


                <strong>

                    <?php
                    echo $total_disponibles;
                    ?>

                </strong>

            </div>


        </article>



        <!--=================================================
            CERRADOS
        ==================================================-->

        <article class="resumen-card cerrado">


            <div class="resumen-icon">

                🔴

            </div>


            <div>

                <span>
                    Cerrados
                </span>


                <strong>

                    <?php
                    echo $total_cerrados;
                    ?>

                </strong>

            </div>


        </article>


    </section>



    <!--=====================================================
        LISTADO
    ======================================================-->

    <section class="agenda-listado">


        <div class="listado-header">


            <h2>
                Mis disponibilidades
            </h2>


            <p>

                Aquí puedes administrar los días y horarios
                en los que estarás disponible para atención.

            </p>


        </div>



        <?php if ($total_disponibilidades > 0): ?>


            <!--=================================================
                GRID
            ==================================================-->

            <div class="disponibilidades-grid">


                <?php foreach (
                    $disponibilidades
                    as $disponibilidad
                ): ?>


                    <?php

                    /*=============================================
                        DATOS
                    ==============================================*/

                    $id =
                        (int)
                        $disponibilidad[
                            "id_disponibilidad"
                        ];


                    $fecha =
                        $disponibilidad["fecha"];


                    $hora_inicio =
                        date(
                            "H:i",
                            strtotime(
                                $disponibilidad[
                                    "hora_inicio"
                                ]
                            )
                        );


                    $hora_fin =
                        date(
                            "H:i",
                            strtotime(
                                $disponibilidad[
                                    "hora_fin"
                                ]
                            )
                        );


                    $estado =
                        $disponibilidad["estado"];


                    /*=============================================
                        FECHA
                    ==============================================*/

                    $fecha_obj =
                        new DateTime($fecha);


                    $fecha_formateada =
                        $fecha_obj->format(
                            "d/m/Y"
                        );


                    $dia_semana =
                        $fecha_obj->format(
                            "l"
                        );


                    /*=============================================
                        TRADUCIR DÍA
                    ==============================================*/

                    $dias = [

                        "Monday" =>
                            "Lunes",

                        "Tuesday" =>
                            "Martes",

                        "Wednesday" =>
                            "Miércoles",

                        "Thursday" =>
                            "Jueves",

                        "Friday" =>
                            "Viernes",

                        "Saturday" =>
                            "Sábado",

                        "Sunday" =>
                            "Domingo"

                    ];


                    $dia_semana =
                        $dias[$dia_semana]
                        ?? $dia_semana;


                    /*=============================================
                        ESTADO
                    ==============================================*/

                    if (
                        $estado === "DISPONIBLE"
                    ) {

                        $clase_estado =
                            "estado-disponible";

                        $icono_estado =
                            "🟢";

                        $texto_estado =
                            "Disponible";

                        $texto_accion =
                            "Cerrar";

                        $icono_accion =
                            "🔒";

                    } else {

                        $clase_estado =
                            "estado-cerrado";

                        $icono_estado =
                            "🔴";

                        $texto_estado =
                            "Cerrado";

                        $texto_accion =
                            "Abrir";

                        $icono_accion =
                            "🔓";

                    }

                    ?>


                    <!--=================================================
                        TARJETA
                    ==================================================-->

                    <article
                        class="disponibilidad-card"
                    >


                        <!--=============================================
                            FECHA
                        ==============================================-->

                        <div class="card-fecha">


                            <div class="fecha-icon">

                                📅

                            </div>


                            <div>

                                <span>
                                    Fecha
                                </span>


                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $dia_semana
                                    );
                                    ?>

                                    <br>

                                    <?php
                                    echo htmlspecialchars(
                                        $fecha_formateada
                                    );
                                    ?>

                                </strong>

                            </div>


                        </div>



                        <!--=============================================
                            HORARIO
                        ==============================================-->

                        <div class="card-horario">


                            <span>
                                Horario disponible
                            </span>


                            <strong>

                                🕐

                                <?php
                                echo htmlspecialchars(
                                    $hora_inicio
                                );
                                ?>


                                <span class="separador">
                                    —
                                </span>


                                <?php
                                echo htmlspecialchars(
                                    $hora_fin
                                );
                                ?>

                            </strong>


                        </div>



                        <!--=============================================
                            ESTADO
                        ==============================================-->

                        <div class="card-estado">


                            <span>
                                Estado
                            </span>


                            <span
                                class="estado <?php echo $clase_estado; ?>"
                            >

                                <?php
                                echo $icono_estado;
                                ?>

                                <?php
                                echo $texto_estado;
                                ?>

                            </span>


                        </div>



                        <!--=============================================
                            ACCIONES
                        ==============================================-->

                        <div class="card-acciones">


                            <!--=========================================
                                EDITAR
                            ==========================================-->

                            <a
                                href="editar.php?id=<?php echo $id; ?>"
                                class="btn-editar"
                            >

                                ✏️
                                Editar

                            </a>



                            <!--=========================================
                                ESTADO
                            ==========================================-->

                            <a
                                href="cambiar_estado.php?id=<?php echo $id; ?>"
                                class="btn-estado"
                            >

                                <?php
                                echo $icono_accion;
                                ?>

                                <?php
                                echo $texto_accion;
                                ?>

                            </a>



                            <!--=========================================
                                ELIMINAR
                            ==========================================-->

                            <a
                                href="eliminar.php?id=<?php echo $id; ?>"
                                class="btn-eliminar"
                            >

                                🗑️
                                Eliminar

                            </a>


                        </div>


                    </article>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <!--=================================================
                SIN DISPONIBILIDADES
            ==================================================-->

            <div class="sin-disponibilidades">


                <div class="sin-disponibilidades-icon">

                    📅

                </div>


                <h3>

                    Aún no tienes disponibilidades

                </h3>


                <p>

                    Registra los días y horarios en los que
                    estarás disponible para atención
                    psicológica.

                </p>


                <a
                    href="registrar.php"
                    class="btn-nueva"
                >

                    <span>
                        +
                    </span>

                    Registrar mi primer horario

                </a>


            </div>


        <?php endif; ?>


    </section>


</main>



<!--=========================================================
    JAVASCRIPT
=========================================================-->
<?php
/*=========================================================
    MENSAJES DE OPERACIÓN
=========================================================*/

$mensaje = "";
$tipo_mensaje = "";

if (isset($_GET["success"])) {

    switch ($_GET["success"]) {

        case "registrado":

            $mensaje =
                "La disponibilidad se registró correctamente.";

            $tipo_mensaje = "exito";

            break;


        case "actualizado":

            $mensaje =
                "La disponibilidad se actualizó correctamente.";

            $tipo_mensaje = "exito";

            break;


        case "estado":

            $mensaje =
                "El estado de la disponibilidad se actualizó correctamente.";

            $tipo_mensaje = "exito";

            break;


        case "eliminado":

            $mensaje =
                "La disponibilidad se eliminó correctamente.";

            $tipo_mensaje = "exito";

            break;

    }

}


if (isset($_GET["error"])) {

    switch ($_GET["error"]) {

        case "no_psicologo":

            $mensaje =
                "No se encontró el perfil del psicólogo.";

            break;


        case "campos":

            $mensaje =
                "Debes completar todos los campos.";

            break;


        case "fecha":

            $mensaje =
                "La fecha seleccionada no es válida.";

            break;


        case "fecha_pasada":

            $mensaje =
                "No puedes registrar una fecha anterior a la fecha actual.";

            break;


        case "horario":

            $mensaje =
                "La hora de inicio debe ser menor que la hora de finalización.";

            break;


        case "hora":

            $mensaje =
                "El horario seleccionado no es válido.";

            break;


        case "superpuesto":

            $mensaje =
                "Ya existe una disponibilidad que se cruza con ese horario.";

            break;


        case "no_encontrado":

            $mensaje =
                "No se encontró la disponibilidad solicitada.";

            break;


        case "tiene_citas":

            $mensaje =
                "Esta disponibilidad no puede eliminarse porque tiene citas asociadas.";

            break;


        case "eliminar":

            $mensaje =
                "No fue posible eliminar la disponibilidad.";

            break;


        case "actualizar":

            $mensaje =
                "No fue posible actualizar la disponibilidad.";

            break;


        case "estado":

            $mensaje =
                "No fue posible actualizar el estado.";

            break;


        case "id":

            $mensaje =
                "El identificador de la disponibilidad no es válido.";

            break;


        default:

            $mensaje =
                "Ocurrió un error al realizar la operación.";

            break;

    }


    $tipo_mensaje = "error";

}
?>


<?php if (!empty($mensaje)): ?>

<!--=========================================================
    MODAL DE MENSAJE
=========================================================-->

<div
    class="modal-mensaje-overlay"
    id="modalMensaje"
>


    <div
        class="modal-mensaje <?php echo $tipo_mensaje; ?>"
        id="modalMensajeContenido"
    >


        <!--=================================================
            ICONO
        ==================================================-->

        <div class="modal-icono">


            <?php if ($tipo_mensaje === "exito"): ?>

                ✓

            <?php else: ?>

                !

            <?php endif; ?>


        </div>


        <!--=================================================
            TEXTO
        ==================================================-->

        <div class="modal-texto">


            <h3>

                <?php

                echo (
                    $tipo_mensaje === "exito"
                    ? "¡Listo!"
                    : "Atención"
                );

                ?>

            </h3>


            <p>

                <?php
                echo htmlspecialchars(
                    $mensaje
                );
                ?>

            </p>


        </div>


        <!--=================================================
            CERRAR
        ==================================================-->

        <button
            type="button"
            class="modal-cerrar"
            onclick="cerrarModalMensaje()"
        >

            ×

        </button>


        <!--=================================================
            BARRA DE TIEMPO
        ==================================================-->

        <div class="modal-progreso"></div>


    </div>


</div>

<?php endif; ?>


<script src="js/agenda.js"></script>
<script
    src="js/agenda.js"
></script>


</body>

</html>