<?php

/*=========================================================
    CITAS DEL PSICÓLOGO
    SIST V.4.0 - PIT V4.0
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

$id_usuario = isset($_SESSION["id_usuario"])
    ? (int) $_SESSION["id_usuario"]
    : 0;


if ($id_usuario <= 0) {

    header("Location: ../../indexloguin.php");
    exit();

}


/*=========================================================
    OBTENER PSICÓLOGO
=========================================================*/

$sql_psicologo = "

    SELECT

        id_registro,
        nombre,
        apellido_p,
        apellido_m,
        correo_institucional,
        no_empleado

    FROM administradores_psicologos

    WHERE id_usuario = ?

      AND activo = 1

    LIMIT 1

";


$stmt_psicologo =

    $conn->prepare(
        $sql_psicologo
    );


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


if (
    $resultado_psicologo->num_rows === 0
) {

    $stmt_psicologo->close();

    die("

        <div style='
            padding:50px;
            text-align:center;
            font-family:Arial,sans-serif;
        '>

            <h2>
                No se encontró el perfil del psicólogo.
            </h2>

            <p>
                El usuario actual no tiene un perfil de
                psicólogo activo asociado.
            </p>

        </div>

    ");

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

$nombre_psicologo = trim(

    $datos_psicologo["nombre"]
    . " "
    . $datos_psicologo["apellido_p"]
    . " "
    . $datos_psicologo["apellido_m"]

);


/*=========================================================
    OBTENER CITAS
=========================================================*/

$sqlCitas = "

    SELECT

        c.id_cita,
        c.id_disponibilidad,
        c.id_tutorado,
        c.motivo,
        c.hora_inicio_atencion,
        c.hora_fin_atencion,
        c.estado,
        c.fecha_registro,

        d.fecha,
        d.hora_inicio,
        d.hora_fin,
        d.estado AS estado_disponibilidad,

        t.matricula,
        t.nombre AS tutorado_nombre,
        t.apellido_p AS tutorado_apellido_p,
        t.apellido_m AS tutorado_apellido_m,
        t.carrera,
        t.grupo

    FROM citas_psicologia c

    INNER JOIN disponibilidad_psicologos d

        ON d.id_disponibilidad =
           c.id_disponibilidad

    INNER JOIN tutorados t

        ON t.id_tutorado =
           c.id_tutorado

    WHERE d.id_psicologo = ?

    ORDER BY

        d.fecha DESC,
        d.hora_inicio DESC,
        c.id_cita DESC

";


$stmtCitas =

    $conn->prepare(
        $sqlCitas
    );


if (!$stmtCitas) {

    die(
        "Error al preparar la consulta de citas: "
        . $conn->error
    );

}


$stmtCitas->bind_param(
    "i",
    $id_psicologo
);


$stmtCitas->execute();


$resultadoCitas =

    $stmtCitas->get_result();


$citas = [];


while (
    $fila =
    $resultadoCitas->fetch_assoc()
) {

    $citas[] = $fila;

}


$stmtCitas->close();


/*=========================================================
    CONTADORES
=========================================================*/

$totalPendientes = 0;
$totalConfirmadas = 0;
$totalAtendiendo = 0;
$totalAtendidas = 0;
$totalCanceladas = 0;
$totalNoAsistio = 0;


foreach ($citas as $registro) {

    switch ($registro["estado"]) {

        case "PENDIENTE":
            $totalPendientes++;
            break;

        case "CONFIRMADA":
            $totalConfirmadas++;
            break;

        case "ATENDIENDO":
            $totalAtendiendo++;
            break;

        case "ATENDIDA":
            $totalAtendidas++;
            break;

        case "CANCELADA":
            $totalCanceladas++;
            break;

        case "NO_ASISTIO":
            $totalNoAsistio++;
            break;

    }

}


/*=========================================================
    FECHA ACTUAL
=========================================================*/

$fechaHoy = date("Y-m-d");


/*=========================================================
    CITAS DE HOY
=========================================================*/

$totalHoy = 0;


foreach ($citas as $registro) {

    if ($registro["fecha"] === $fechaHoy) {

        $totalHoy++;

    }

}


/*=========================================================
    DÍAS
=========================================================*/

$dias = [

    "Sunday"    => "Domingo",
    "Monday"    => "Lunes",
    "Tuesday"   => "Martes",
    "Wednesday" => "Miércoles",
    "Thursday"  => "Jueves",
    "Friday"    => "Viernes",
    "Saturday"  => "Sábado"

];


/*=========================================================
    MESES
=========================================================*/

$meses = [

    "January"   => "enero",
    "February"  => "febrero",
    "March"     => "marzo",
    "April"     => "abril",
    "May"       => "mayo",
    "June"      => "junio",
    "July"      => "julio",
    "August"    => "agosto",
    "September" => "septiembre",
    "October"   => "octubre",
    "November"  => "noviembre",
    "December"  => "diciembre"

];


/*=========================================================
    FUNCIÓN FECHA BONITA
=========================================================*/

function fechaBonita(
    $fecha,
    $dias,
    $meses
) {

    $objetoFecha =
        new DateTime($fecha);

    $dia =
        $objetoFecha->format("l");

    $mes =
        $objetoFecha->format("F");

    return

        ($dias[$dia] ?? $dia)

        . " "

        . $objetoFecha->format("d")

        . " de "

        . ($meses[$mes] ?? $mes)

        . " de "

        . $objetoFecha->format("Y");

}


/*=========================================================
    TEXTOS DE ESTADO
=========================================================*/

$estadoTexto = [

    "PENDIENTE"   => "Pendiente",
    "CONFIRMADA"  => "Confirmada",
    "ATENDIENDO"  => "Atendiendo",
    "ATENDIDA"    => "Atendida",
    "CANCELADA"   => "Cancelada",
    "NO_ASISTIO"  => "No asistió"

];


/*=========================================================
    ICONOS DE ESTADO
=========================================================*/

$estadoIcono = [

    "PENDIENTE"   => "fa-clock",
    "CONFIRMADA"  => "fa-circle-check",
    "ATENDIENDO"  => "fa-user-doctor",
    "ATENDIDA"    => "fa-check-double",
    "CANCELADA"   => "fa-ban",
    "NO_ASISTIO"  => "fa-user-xmark"

];


/*=========================================================
    MENSAJE DE RESULTADO
=========================================================*/

$success =

    isset($_GET["success"])
        ? trim($_GET["success"])
        : "";


$accionResultado =

    isset($_GET["accion"])
        ? trim($_GET["accion"])
        : "";


$idCitaResultado =

    isset($_GET["cita"])
        ? (int) $_GET["cita"]
        : 0;


/*=========================================================
    DATOS DEL MENSAJE DE ÉXITO
=========================================================*/

$mostrarExito = false;

$tituloExito = "";
$mensajeExito = "";
$iconoExito = "fa-circle-check";
$claseExito = "modal-exito";


/*=========================================================
    BUSCAR NOMBRE DEL TUTORADO
=========================================================*/

$nombreTutoradoExito = "La cita";


if (
    $success === "estado_cita" &&
    $idCitaResultado > 0
) {

    foreach ($citas as $citaResultado) {

        if (
            (int)$citaResultado["id_cita"]
            === $idCitaResultado
        ) {

            $nombreTutoradoExito = trim(

                $citaResultado["tutorado_nombre"]
                . " "
                . $citaResultado["tutorado_apellido_p"]
                . " "
                . $citaResultado["tutorado_apellido_m"]

            );

            break;

        }

    }

}


/*=========================================================
    CONFIGURAR MENSAJE
=========================================================*/

if ($success === "estado_cita") {

    switch ($accionResultado) {

        case "confirmar":

            $mostrarExito = true;

            $tituloExito =
                "Cita confirmada";

            $mensajeExito =
                "La cita de "
                . $nombreTutoradoExito
                . " fue confirmada correctamente.";

            $iconoExito =
                "fa-circle-check";

            $claseExito =
                "modal-exito modal-confirmar";

            break;


        case "cancelar":

            $mostrarExito = true;

            $tituloExito =
                "Cita cancelada";

            $mensajeExito =
                "La cita de "
                . $nombreTutoradoExito
                . " fue cancelada correctamente.";

            $iconoExito =
                "fa-ban";

            $claseExito =
                "modal-exito modal-cancelar";

            break;


        case "atendiendo":

            $mostrarExito = true;

            $tituloExito =
                "Atención iniciada";

            $mensajeExito =
                "La cita de "
                . $nombreTutoradoExito
                . " ahora se encuentra en estado de atención.";

            $iconoExito =
                "fa-user-doctor";

            $claseExito =
                "modal-exito modal-atendiendo";

            break;


        case "atendida":

            $mostrarExito = true;

            $tituloExito =
                "Atención finalizada";

            $mensajeExito =
                "La cita de "
                . $nombreTutoradoExito
                . " fue marcada como atendida correctamente.";

            $iconoExito =
                "fa-check-double";

            $claseExito =
                "modal-exito modal-atendida";

            break;


        case "no_asistio":

            $mostrarExito = true;

            $tituloExito =
                "Inasistencia registrada";

            $mensajeExito =
                "La cita de "
                . $nombreTutoradoExito
                . " fue registrada como inasistencia.";

            $iconoExito =
                "fa-user-xmark";

            $claseExito =
                "modal-exito modal-no-asistio";

            break;

    }

}


/*=========================================================
    MENSAJES DE ERROR
=========================================================*/

$error =

    isset($_GET["error"])
        ? trim($_GET["error"])
        : "";


$mostrarError = false;

$tituloError = "No fue posible realizar la acción.";

$mensajeError =
    "Ocurrió un problema al procesar la solicitud.";


if (!empty($error)) {

    $mostrarError = true;


    switch ($error) {

        case "sesion":

            $tituloError =
                "Sesión no válida";

            $mensajeError =
                "No se pudo identificar correctamente tu sesión.";

            break;


        case "psicologo":

            $tituloError =
                "Perfil no encontrado";

            $mensajeError =
                "No se encontró un perfil de psicólogo activo asociado a tu usuario.";

            break;


        case "id_cita":

            $tituloError =
                "Cita no válida";

            $mensajeError =
                "No se recibió correctamente el identificador de la cita.";

            break;


        case "accion":

            $tituloError =
                "Acción no válida";

            $mensajeError =
                "La acción solicitada no está permitida.";

            break;


        case "consulta":

            $tituloError =
                "Error de consulta";

            $mensajeError =
                "No fue posible consultar la información de la cita.";

            break;


        case "cita_no_encontrada":

            $tituloError =
                "Cita no encontrada";

            $mensajeError =
                "La cita no existe o no pertenece a tu agenda.";

            break;


        case "estado_no_valido":

            $tituloError =
                "Cambio de estado no permitido";

            $mensajeError =
                "La cita no puede cambiar a ese estado desde su estado actual.";

            break;


        case "actualizar":

            $tituloError =
                "No se pudo actualizar";

            $mensajeError =
                "Ocurrió un error al actualizar la cita en el sistema.";

            break;


        case "sin_cambios":

            $tituloError =
                "Sin cambios";

            $mensajeError =
                "La cita no pudo actualizarse porque no se detectaron cambios.";

            break;


        default:

            $tituloError =
                "No fue posible realizar la acción";

            $mensajeError =
                "Ocurrió un problema inesperado. Intenta nuevamente.";

            break;

    }

}

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
        Citas | Psicología PIT V4.0
    </title>


    <!--=================================================
        FONT AWESOME
    ==================================================-->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!--=================================================
        CSS
    ==================================================-->

    <link
        rel="stylesheet"
        href="css/citas.css"
    >

</head>


<body>


<?php

/*=========================================================
    SIDEBAR
=========================================================*/

include(
    "../../includes_pit/sidebar_psicologos.php"
);

?>


<!--=========================================================
    CONTENEDOR PRINCIPAL
=========================================================-->

<main class="contenedor-citas">


    <!--=====================================================
        ENCABEZADO
    ======================================================-->

    <section class="encabezado-citas">

        <div class="encabezado-icono">

            <i class="fas fa-calendar-check"></i>

        </div>


        <div>

            <span>
                Departamento de Psicología
            </span>

            <h1>
                Citas de psicología
            </h1>

            <p>
                Gestiona las solicitudes de atención
                de los estudiantes asignados a ti.
            </p>

        </div>

    </section>



    <!--=====================================================
        BIENVENIDA
    ======================================================-->

    <section class="bienvenida-psicologo">

        <div>

            <span>
                PSICÓLOGO
            </span>

            <h2>

                Bienvenido,
                <?= htmlspecialchars(
                    $datos_psicologo["nombre"]
                ) ?>

            </h2>

            <p>
                Desde este espacio puedes consultar,
                confirmar y dar seguimiento a las citas
                de tus alumnos.
            </p>

        </div>


        <div class="bienvenida-icono">

            <i class="fas fa-user-doctor"></i>

        </div>

    </section>



    <!--=====================================================
        RESUMEN
    ======================================================-->

    <section class="resumen-citas">


        <div class="resumen-card pendiente">

            <div class="resumen-icono">

                <i class="fas fa-clock"></i>

            </div>

            <div>

                <span>
                    Pendientes
                </span>

                <strong>
                    <?= $totalPendientes ?>
                </strong>

            </div>

        </div>



        <div class="resumen-card confirmada">

            <div class="resumen-icono">

                <i class="fas fa-circle-check"></i>

            </div>

            <div>

                <span>
                    Confirmadas
                </span>

                <strong>
                    <?= $totalConfirmadas ?>
                </strong>

            </div>

        </div>



        <div class="resumen-card atendiendo">

            <div class="resumen-icono">

                <i class="fas fa-user-doctor"></i>

            </div>

            <div>

                <span>
                    En atención
                </span>

                <strong>
                    <?= $totalAtendiendo ?>
                </strong>

            </div>

        </div>



        <div class="resumen-card hoy">

            <div class="resumen-icono">

                <i class="fas fa-calendar-day"></i>

            </div>

            <div>

                <span>
                    Citas de hoy
                </span>

                <strong>
                    <?= $totalHoy ?>
                </strong>

            </div>

        </div>


    </section>



    <!--=====================================================
        ENCABEZADO LISTA
    ======================================================-->

    <section class="lista-encabezado">

        <div>

            <span>
                GESTIÓN
            </span>

            <h2>
                Solicitudes de atención
            </h2>

        </div>


        <div class="total-citas">

            <i class="fas fa-list-check"></i>

            <?= count($citas) ?>

            <?= count($citas) === 1
                ? "cita"
                : "citas"
            ?>

        </div>

    </section>



    <!--=====================================================
        LISTA DE CITAS
    ======================================================-->

    <?php if (empty($citas)): ?>


        <section class="sin-citas">

            <div class="sin-citas-icono">

                <i class="fas fa-calendar-xmark"></i>

            </div>

            <h2>
                No tienes citas registradas
            </h2>

            <p>
                Cuando un tutorado solicite un espacio
                de atención asociado a tu disponibilidad,
                aparecerá aquí.
            </p>

        </section>


    <?php else: ?>


        <section class="lista-citas">


            <?php foreach ($citas as $cita): ?>


                <?php

                $estado =
                    $cita["estado"];


                $textoEstado =
                    $estadoTexto[$estado]
                    ?? $estado;


                $iconoEstado =
                    $estadoIcono[$estado]
                    ?? "fa-circle-question";


                $nombreTutorado = trim(

                    $cita["tutorado_nombre"]
                    . " "
                    . $cita["tutorado_apellido_p"]
                    . " "
                    . $cita["tutorado_apellido_m"]

                );


                $fechaCita =
                    fechaBonita(
                        $cita["fecha"],
                        $dias,
                        $meses
                    );


                $horaInicio =
                    date(
                        "H:i",
                        strtotime(
                            $cita["hora_inicio"]
                        )
                    );


                $horaFin =
                    date(
                        "H:i",
                        strtotime(
                            $cita["hora_fin"]
                        )
                    );

                ?>


                <!--=================================================
                    TARJETA
                ==================================================-->

                <article
                    class="
                        cita-psicologo-card
                        estado-card-<?= strtolower(
                            $estado
                        ) ?>
                    "
                >


                    <!--=============================================
                        PARTE SUPERIOR
                    ==============================================-->

                    <div class="cita-card-superior">


                        <div>

                            <span class="numero-cita">

                                CITA #

                                <?= (int)
                                    $cita["id_cita"]
                                ?>

                            </span>


                            <h3>

                                <?= htmlspecialchars(
                                    $nombreTutorado
                                ) ?>

                            </h3>

                        </div>


                        <div
                            class="
                                estado-cita
                                estado-<?= strtolower(
                                    $estado
                                ) ?>
                            "
                        >

                            <i
                                class="
                                    fas
                                    <?= htmlspecialchars(
                                        $iconoEstado
                                    ) ?>
                                "
                            ></i>

                            <?= htmlspecialchars(
                                $textoEstado
                            ) ?>

                        </div>


                    </div>



                    <!--=============================================
                        DATOS TUTORADO
                    ==============================================-->

                    <div class="datos-tutorado">


                        <div class="dato">

                            <i class="fas fa-id-card"></i>

                            <div>

                                <span>
                                    Matrícula
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $cita["matricula"]
                                    ) ?>

                                </strong>

                            </div>

                        </div>



                        <div class="dato">

                            <i class="fas fa-graduation-cap"></i>

                            <div>

                                <span>
                                    Carrera
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $cita["carrera"]
                                    ) ?>

                                </strong>

                            </div>

                        </div>



                        <div class="dato">

                            <i class="fas fa-users"></i>

                            <div>

                                <span>
                                    Grupo
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $cita["grupo"]
                                    ) ?>

                                </strong>

                            </div>

                        </div>


                    </div>



                    <!--=============================================
                        FECHA Y HORARIO
                    ==============================================-->

                    <div class="horario-cita">


                        <div>

                            <i class="fas fa-calendar-day"></i>

                            <div>

                                <span>
                                    Fecha
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $fechaCita
                                    ) ?>

                                </strong>

                            </div>

                        </div>



                        <div>

                            <i class="fas fa-clock"></i>

                            <div>

                                <span>
                                    Horario
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $horaInicio
                                    ) ?>

                                    —

                                    <?= htmlspecialchars(
                                        $horaFin
                                    ) ?>

                                </strong>

                            </div>

                        </div>


                    </div>



                    <!--=============================================
                        MOTIVO
                    ==============================================-->

                    <?php if (
                        !empty(
                            $cita["motivo"]
                        )
                    ): ?>


                        <div class="motivo-cita">

                            <span>
                                Motivo de la solicitud
                            </span>

                            <p>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $cita["motivo"]
                                    )
                                ) ?>

                            </p>

                        </div>


                    <?php endif; ?>



                    <!--=============================================
                        ACCIONES
                    ==============================================-->

                    <div class="acciones-cita">


                        <?php if (
                            $estado === "PENDIENTE"
                        ): ?>


                            <button
                                type="button"
                                class="
                                    btn-accion
                                    btn-confirmar
                                    btn-cambiar-estado
                                "
                                data-id="<?= (int)
                                    $cita["id_cita"]
                                ?>"
                                data-accion="confirmar"
                            >

                                <i class="fas fa-circle-check"></i>

                                Confirmar cita

                            </button>


                            <button
                                type="button"
                                class="
                                    btn-accion
                                    btn-cancelar
                                    btn-cambiar-estado
                                "
                                data-id="<?= (int)
                                    $cita["id_cita"]
                                ?>"
                                data-accion="cancelar"
                            >

                                <i class="fas fa-ban"></i>

                                Cancelar

                            </button>


                        <?php elseif (
                            $estado === "CONFIRMADA"
                        ): ?>


                            <button
                                type="button"
                                class="
                                    btn-accion
                                    btn-atendiendo
                                    btn-cambiar-estado
                                "
                                data-id="<?= (int)
                                    $cita["id_cita"]
                                ?>"
                                data-accion="atendiendo"
                            >

                                <i class="fas fa-user-check"></i>

                                Alumno llegó

                            </button>


                            <button
                                type="button"
                                class="
                                    btn-accion
                                    btn-no-asistio
                                    btn-cambiar-estado
                                "
                                data-id="<?= (int)
                                    $cita["id_cita"]
                                ?>"
                                data-accion="no_asistio"
                            >

                                <i class="fas fa-user-xmark"></i>

                                No asistió

                            </button>


                            <button
                                type="button"
                                class="
                                    btn-accion
                                    btn-cancelar
                                    btn-cambiar-estado
                                "
                                data-id="<?= (int)
                                    $cita["id_cita"]
                                ?>"
                                data-accion="cancelar"
                            >

                                <i class="fas fa-ban"></i>

                                Cancelar

                            </button>


                        <?php elseif (
                            $estado === "ATENDIENDO"
                        ): ?>


                            <button
                                type="button"
                                class="
                                    btn-accion
                                    btn-finalizar
                                    btn-cambiar-estado
                                "
                                data-id="<?= (int)
                                    $cita["id_cita"]
                                ?>"
                                data-accion="atendida"
                            >

                                <i class="fas fa-check-double"></i>

                                Finalizar atención

                            </button>


                        <?php elseif (
                            $estado === "ATENDIDA"
                        ): ?>


                            <div class="cita-finalizada">

                                <i class="fas fa-circle-check"></i>

                                Atención finalizada

                            </div>


                        <?php elseif (
                            $estado === "CANCELADA"
                        ): ?>


                            <div class="cita-cerrada">

                                <i class="fas fa-ban"></i>

                                Cita cancelada

                            </div>


                        <?php elseif (
                            $estado === "NO_ASISTIO"
                        ): ?>


                            <div
                                class="
                                    cita-cerrada
                                    no-asistio
                                "
                            >

                                <i class="fas fa-user-xmark"></i>

                                Inasistencia registrada

                            </div>


                        <?php endif; ?>


                    </div>


                </article>


            <?php endforeach; ?>


        </section>


    <?php endif; ?>


</main>



<!--=========================================================
    MODAL DE ÉXITO
=========================================================-->

<div
    id="modalExitoCita"
    class="
        modal-cita-overlay
        <?= $mostrarExito
            ? $claseExito
            : ""
        ?>
    "
    <?= $mostrarExito
        ? 'data-mostrar="true"'
        : ''
    ?>
>

    <div class="modal-cita modal-cita-exito">


        <button
            type="button"
            class="modal-cita-cerrar"
            id="cerrarModalExito"
        >
            ×
        </button>


        <div class="modal-cita-icono">

            <i
                class="fas <?= htmlspecialchars(
                    $iconoExito
                ) ?>"
            ></i>

        </div>


        <div class="modal-cita-contenido">

            <span class="modal-cita-etiqueta">
                GESTIÓN DE CITA
            </span>


            <h2>

                <?= htmlspecialchars(
                    $tituloExito
                ) ?>

            </h2>


            <p>

                <?= htmlspecialchars(
                    $mensajeExito
                ) ?>

            </p>

        </div>


        <div class="modal-cita-acciones">

            <button
                type="button"
                class="modal-btn modal-btn-confirmar"
                id="aceptarModalExito"
            >

                <i class="fas fa-check"></i>

                Entendido

            </button>

        </div>


    </div>

</div>



<!--=========================================================
    MODAL DE ERROR
=========================================================-->

<div
    id="modalErrorCita"
    class="
        modal-cita-overlay
        modal-error
        <?= $mostrarError
            ? "mostrar"
            : ""
        ?>
    "
>

    <div class="modal-cita modal-cita-error">


        <button
            type="button"
            class="modal-cita-cerrar"
            id="cerrarModalError"
        >
            ×
        </button>


        <div class="modal-cita-icono">

            <i class="fas fa-circle-exclamation"></i>

        </div>


        <div class="modal-cita-contenido">

            <span class="modal-cita-etiqueta">
                AVISO
            </span>


            <h2>

                <?= htmlspecialchars(
                    $tituloError
                ) ?>

            </h2>


            <p>

                <?= htmlspecialchars(
                    $mensajeError
                ) ?>

            </p>

        </div>


        <div class="modal-cita-acciones">

            <button
                type="button"
                class="modal-btn modal-btn-confirmar"
                id="aceptarModalError"
            >

                <i class="fas fa-check"></i>

                Entendido

            </button>

        </div>


    </div>

</div>



<!--=========================================================
    JAVASCRIPT
=========================================================-->

<script src="js/citas.js"></script>


</body>

</html>


<?php

/*=========================================================
    FOOTER
=========================================================*/

include(
    "../../../includes/footer.php"
);

?>