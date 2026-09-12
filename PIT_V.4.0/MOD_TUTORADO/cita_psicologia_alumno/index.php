<?php

/*=========================================================
    VALIDACIÓN DE SESIÓN - TUTORADO
=========================================================*/

require_once("../../sesion.php");

if(
    !isset($_SESSION["roles"]) ||
    !in_array(
        "TUTORADO",
        $_SESSION["roles"]
    )
){

    header(
        "Location: ../../indexloguin.php"
    );

    exit();

}


/*=========================================================
    CONEXIÓN A BASE DE DATOS
=========================================================*/

require_once("../../base_pit/conect_pit.php");


/*=========================================================
    SIDEBAR DEL TUTORADO
=========================================================*/

include(
    "../../includes_pit/sidebar_tutorado.php"
);


/*=========================================================
    IDENTIFICAR USUARIO
=========================================================*/

$id_usuario =

    isset($_SESSION["id_usuario"])

        ? (int)$_SESSION["id_usuario"]

        : 0;


/*=========================================================
    BUSCAR INFORMACIÓN DEL TUTORADO
=========================================================*/

$sqlTutorado = "

    SELECT

        t.id_tutorado,
        t.nombre,
        t.apellido_p,
        t.apellido_m,
        t.matricula,
        t.carrera,
        t.grupo

    FROM tutorados t

    INNER JOIN usuarios u
        ON u.id_usuario = t.id_usuario

    WHERE t.id_usuario = ?

      AND t.activo = 1

    LIMIT 1

";


$stmtTutorado =
    $conn->prepare(
        $sqlTutorado
    );


$stmtTutorado->bind_param(
    "i",
    $id_usuario
);


$stmtTutorado->execute();


$resultadoTutorado =
    $stmtTutorado->get_result();


$tutorado =
    $resultadoTutorado->fetch_assoc();


$stmtTutorado->close();


/*=========================================================
    VALIDAR TUTORADO
=========================================================*/

if(!$tutorado){

    echo "

        <div style='
            padding:50px;
            text-align:center;
            font-family:Arial,sans-serif;
        '>

            <h2>
                No fue posible identificar tu cuenta.
            </h2>

            <p>
                Cierra sesión e ingresa nuevamente al PIT.
            </p>

        </div>

    ";

    exit();

}


$id_tutorado =
    (int)$tutorado["id_tutorado"];


/*=========================================================
    BUSCAR CITA ACTUAL
=========================================================*/

/*
    SOLAMENTE ESTOS ESTADOS SON CONSIDERADOS
    COMO UNA CITA ACTIVA:

        PENDIENTE
        CONFIRMADA
        ATENDIENDO

    Estos estados pasan al historial:

        ATENDIDA
        CANCELADA
        NO_ASISTIO
*/

$sqlCitaActual = "

    SELECT

        c.id_cita,
        c.id_disponibilidad,
        c.motivo,
        c.hora_inicio_atencion,
        c.hora_fin_atencion,
        c.estado,
        c.fecha_registro,

        d.fecha,
        d.hora_inicio,
        d.hora_fin,

        p.nombre AS psicologo_nombre,
        p.apellido_p AS psicologo_apellido_p,
        p.apellido_m AS psicologo_apellido_m

    FROM citas_psicologia c

    INNER JOIN disponibilidad_psicologos d
        ON d.id_disponibilidad = c.id_disponibilidad

    INNER JOIN administradores_psicologos p
        ON p.id_registro = d.id_psicologo

    WHERE c.id_tutorado = ?

      AND c.estado IN (
          'PENDIENTE',
          'CONFIRMADA',
          'ATENDIENDO'
      )

    ORDER BY c.id_cita DESC

    LIMIT 1

";


$stmtCitaActual =
    $conn->prepare(
        $sqlCitaActual
    );


$stmtCitaActual->bind_param(
    "i",
    $id_tutorado
);


$stmtCitaActual->execute();


$resultadoCitaActual =
    $stmtCitaActual->get_result();


$cita =
    $resultadoCitaActual->fetch_assoc();


$stmtCitaActual->close();


/*=========================================================
    BUSCAR HISTORIAL
=========================================================*/

$sqlHistorial = "

    SELECT

        c.id_cita,
        c.id_disponibilidad,
        c.motivo,
        c.hora_inicio_atencion,
        c.hora_fin_atencion,
        c.estado,
        c.fecha_registro,

        d.fecha,
        d.hora_inicio,
        d.hora_fin,

        p.nombre AS psicologo_nombre,
        p.apellido_p AS psicologo_apellido_p,
        p.apellido_m AS psicologo_apellido_m

    FROM citas_psicologia c

    INNER JOIN disponibilidad_psicologos d
        ON d.id_disponibilidad = c.id_disponibilidad

    INNER JOIN administradores_psicologos p
        ON p.id_registro = d.id_psicologo

    WHERE c.id_tutorado = ?

      AND c.estado IN (
          'ATENDIDA',
          'CANCELADA',
          'NO_ASISTIO'
      )

    ORDER BY c.fecha_registro DESC

";


$stmtHistorial =
    $conn->prepare(
        $sqlHistorial
    );


$stmtHistorial->bind_param(
    "i",
    $id_tutorado
);


$stmtHistorial->execute();


$resultadoHistorial =
    $stmtHistorial->get_result();


$historial = [];


while(
    $fila =
    $resultadoHistorial->fetch_assoc()
){

    $historial[] = $fila;

}


$stmtHistorial->close();


/*=========================================================
    FUNCIONES PARA FECHAS
=========================================================*/

$dias = [

    "Sunday" =>
        "Domingo",

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
        "Sábado"

];


$meses = [

    "January" =>
        "enero",

    "February" =>
        "febrero",

    "March" =>
        "marzo",

    "April" =>
        "abril",

    "May" =>
        "mayo",

    "June" =>
        "junio",

    "July" =>
        "julio",

    "August" =>
        "agosto",

    "September" =>
        "septiembre",

    "October" =>
        "octubre",

    "November" =>
        "noviembre",

    "December" =>
        "diciembre"

];


/*=========================================================
    FUNCIÓN FORMATEAR FECHA
=========================================================*/

function formatearFecha(
    $fecha,
    $dias,
    $meses
){

    $objFecha =
        new DateTime($fecha);


    return

        $dias[
            $objFecha->format("l")
        ]

        . " "

        .

        $objFecha->format("d")

        .

        " de "

        .

        $meses[
            $objFecha->format("F")
        ]

        .

        " de "

        .

        $objFecha->format("Y");

}


/*=========================================================
    ESTADOS
=========================================================*/

$estadoTexto = [

    "PENDIENTE" =>
        "Pendiente",

    "CONFIRMADA" =>
        "Confirmada",

    "ATENDIENDO" =>
        "En atención",

    "ATENDIDA" =>
        "Atendida",

    "CANCELADA" =>
        "Cancelada",

    "NO_ASISTIO" =>
        "No asististe"

];


$estadoIcono = [

    "PENDIENTE" =>
        "fa-clock",

    "CONFIRMADA" =>
        "fa-circle-check",

    "ATENDIENDO" =>
        "fa-user-doctor",

    "ATENDIDA" =>
        "fa-check-double",

    "CANCELADA" =>
        "fa-ban",

    "NO_ASISTIO" =>
        "fa-user-xmark"

];

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
        Citas de Psicología | PIT V4.0
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
        href="css/cita_psicologia.css"
    >

</head>


<body>


<main class="contenedor-cita">


    <!--=================================================
        ENCABEZADO
    ==================================================-->

    <section class="encabezado-cita">

        <div class="encabezado-icono">

            <i class="fas fa-heart-pulse"></i>

        </div>


        <div>

            <span>
                Departamento de Psicología
            </span>

            <h1>
                Mis citas de psicología
            </h1>

            <p>
                Consulta el estado y la información de tus
                solicitudes de atención psicológica.
            </p>

        </div>

    </section>



    <!--=================================================
        CITA ACTUAL
    ==================================================-->

    <?php if($cita): ?>


        <?php

        $estado =
            $cita["estado"];


        $fechaTexto =
            formatearFecha(
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


        $nombrePsicologo =

            $cita["psicologo_nombre"]

            . " "

            .

            $cita["psicologo_apellido_p"]

            . " "

            .

            $cita["psicologo_apellido_m"];

        ?>


        <section class="cita-card">


            <!--=========================================
                ENCABEZADO ESTADO
            ==========================================-->

            <div class="cita-card-header">

                <div>

                    <span class="mini-titulo">
                        CITA ACTUAL
                    </span>

                    <h2>
                        Tu cita de psicología
                    </h2>

                </div>


                <div
                    class="cita-estado estado-<?= strtolower($estado) ?>"
                >

                    <span class="estado-indicador"></span>

                    <i
                        class="fas <?= $estadoIcono[$estado] ?>"
                    ></i>

                    <span>

                        <?= htmlspecialchars(
                            $estadoTexto[$estado]
                        ) ?>

                    </span>

                </div>

            </div>



            <!--=========================================
                MENSAJE DEL ESTADO
            ==========================================-->

            <div
                class="estado-mensaje estado-<?= strtolower($estado) ?>"
            >


                <?php if($estado === "PENDIENTE"): ?>

                    <strong>
                        Tu solicitud está registrada.
                    </strong>

                    <p>
                        El Departamento de Psicología dará
                        seguimiento a tu solicitud de atención.
                    </p>


                <?php elseif($estado === "CONFIRMADA"): ?>

                    <strong>
                        Tu cita está confirmada.
                    </strong>

                    <p>
                        Recuerda asistir en la fecha y horario
                        indicados.
                    </p>


                <?php elseif($estado === "ATENDIENDO"): ?>

                    <strong>
                        Tu atención está en curso.
                    </strong>

                    <p>
                        El psicólogo está atendiendo tu solicitud.
                    </p>


                <?php endif; ?>


            </div>



            <!--=========================================
                INFORMACIÓN DE LA CITA
            ==========================================-->

            <div class="informacion-cita">


                <!-- FECHA -->

                <div class="dato-cita">

                    <div class="dato-icono">

                        <i class="fas fa-calendar-day"></i>

                    </div>

                    <div>

                        <span>
                            Fecha
                        </span>

                        <strong>
                            <?= htmlspecialchars(
                                $fechaTexto
                            ) ?>
                        </strong>

                    </div>

                </div>



                <!-- HORARIO -->

                <div class="dato-cita">

                    <div class="dato-icono">

                        <i class="fas fa-clock"></i>

                    </div>

                    <div>

                        <span>
                            Horario
                        </span>

                        <strong>

                            <?= htmlspecialchars(
                                $horaInicio
                            ) ?>

                            <small>—</small>

                            <?= htmlspecialchars(
                                $horaFin
                            ) ?>

                        </strong>

                    </div>

                </div>



                <!-- PSICÓLOGO -->

                <div class="dato-cita">

                    <div class="dato-icono">

                        <i class="fas fa-user-doctor"></i>

                    </div>

                    <div>

                        <span>
                            Psicólogo asignado
                        </span>

                        <strong>

                            <?= htmlspecialchars(
                                $nombrePsicologo
                            ) ?>

                        </strong>

                    </div>

                </div>


            </div>



            <!--=========================================
                MOTIVO
            ==========================================-->

            <div class="motivo-cita">

                <span>
                    Motivo de tu solicitud
                </span>

                <p>

                    <?= nl2br(
                        htmlspecialchars(
                            $cita["motivo"]
                        )
                    ) ?>

                </p>

            </div>



            <!--=========================================
                RECORDATORIO
            ==========================================-->

            <?php if(
                $estado === "PENDIENTE" ||
                $estado === "CONFIRMADA"
            ): ?>


                <div class="recordatorio-cita">

                    <div class="recordatorio-icono">

                        <i class="fas fa-circle-exclamation"></i>

                    </div>


                    <div>

                        <strong>
                            Recuerda asistir a tu cita
                        </strong>

                        <p>

                            Este espacio fue reservado
                            especialmente para ti y podría ser
                            utilizado por otro estudiante.

                        </p>

                        <p>

                            Si necesitas cancelar tu cita,
                            deberás acudir al área de
                            Desarrollo Académico – Psicología.

                        </p>

                    </div>

                </div>


            <?php endif; ?>


        </section>


    <?php else: ?>


        <!--=================================================
            SIN CITA ACTIVA
        ==================================================-->

        <section class="sin-cita">

            <div class="sin-cita-icono">

                <i class="fas fa-calendar-plus"></i>

            </div>


            <h2>
                No tienes una cita activa
            </h2>


            <p>

                Actualmente no tienes una solicitud de
                atención psicológica pendiente, confirmada
                o en atención.

            </p>


            <a
                href="../../MOD_PSICOLOGOS/solicitud_cita/index.php"
                class="btn-nueva-cita"
            >

                <i class="fas fa-calendar-plus"></i>

                Solicitar nueva cita

            </a>

        </section>


    <?php endif; ?>



    <!--=================================================
        HISTORIAL
    ==================================================-->

    <?php if(!empty($historial)): ?>


        <section class="historial-seccion">


            <!--=========================================
                ENCABEZADO
            ==========================================-->

            <div class="historial-titulo">

                <div>

                    <span>
                        REGISTRO
                    </span>

                    <h2>
                        Historial de citas
                    </h2>

                    <p>
                        Aquí puedes consultar tus citas anteriores.
                    </p>

                </div>


                <div class="historial-icono">

                    <i class="fas fa-clock-rotate-left"></i>

                </div>

            </div>



            <!--=========================================
                LISTA
            ==========================================-->

            <div class="historial-lista">


                <?php foreach(
                    $historial
                    as $registro
                ): ?>


                    <?php

                    $estadoHistorial =
                        $registro["estado"];


                    $fechaHistorialTexto =
                        formatearFecha(
                            $registro["fecha"],
                            $dias,
                            $meses
                        );


                    $horaInicioHistorial =
                        date(
                            "H:i",
                            strtotime(
                                $registro["hora_inicio"]
                            )
                        );


                    $horaFinHistorial =
                        date(
                            "H:i",
                            strtotime(
                                $registro["hora_fin"]
                            )
                        );


                    $nombrePsicologoHistorial =

                        $registro["psicologo_nombre"]

                        . " "

                        .

                        $registro["psicologo_apellido_p"]

                        . " "

                        .

                        $registro["psicologo_apellido_m"];

                    ?>


                    <article
                        class="
                            historial-card
                            estado-historial-<?= strtolower(
                                $estadoHistorial
                            ) ?>
                        "
                    >


                        <!-- ICONO -->

                        <div class="historial-card-icono">

                            <i
                                class="
                                    fas
                                    <?= $estadoIcono[
                                        $estadoHistorial
                                    ] ?>
                                "
                            ></i>

                        </div>



                        <!-- CONTENIDO -->

                        <div class="historial-card-contenido">


                            <div class="historial-card-superior">

                                <strong>

                                    <?= htmlspecialchars(
                                        $estadoTexto[
                                            $estadoHistorial
                                        ]
                                    ) ?>

                                </strong>


                                <span>

                                    <?= htmlspecialchars(
                                        $fechaHistorialTexto
                                    ) ?>

                                </span>

                            </div>



                            <div class="historial-card-datos">


                                <span>

                                    <i
                                        class="fas fa-clock"
                                    ></i>

                                    <?= htmlspecialchars(
                                        $horaInicioHistorial
                                    ) ?>

                                    —

                                    <?= htmlspecialchars(
                                        $horaFinHistorial
                                    ) ?>

                                </span>


                                <span>

                                    <i
                                        class="fas fa-user-doctor"
                                    ></i>

                                    <?= htmlspecialchars(
                                        $nombrePsicologoHistorial
                                    ) ?>

                                </span>


                            </div>



                            <!--=================================
                                MENSAJE DEL HISTORIAL
                            ==================================-->

                            <?php if(
                                $estadoHistorial === "CANCELADA"
                            ): ?>

                                <p class="historial-nota">

                                    Esta cita fue cancelada.
                                    Puedes solicitar un nuevo
                                    espacio disponible.

                                </p>


                            <?php elseif(
                                $estadoHistorial === "NO_ASISTIO"
                            ): ?>

                                <p class="historial-nota">

                                    Se registró que no asististe
                                    a esta cita.

                                </p>


                            <?php elseif(
                                $estadoHistorial === "ATENDIDA"
                            ): ?>

                                <p class="historial-nota">

                                    Esta atención psicológica
                                    fue concluida.

                                </p>

                            <?php endif; ?>


                        </div>

                    </article>


                <?php endforeach; ?>


            </div>


        </section>


    <?php endif; ?>


</main>


<!--=========================================================
    JAVASCRIPT
=========================================================-->

<script
    src="js/cita_psicologia.js"
></script>


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