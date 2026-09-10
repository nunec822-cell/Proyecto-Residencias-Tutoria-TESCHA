<?php
/*=========================================================
    MODULO COORDINADOR
    SIST V.4.0 - PIT V.4.0

    detalle_canalizaciones.php

    Permite al coordinador consultar las canalizaciones
    correspondientes a un tutor de SU MISMA CARRERA.
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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

$id_usuario = (int)$_SESSION["id_usuario"];


/*=========================================================
    VALIDAR ROL COORDINADOR
=========================================================*/

$es_coordinador = false;

if (is_array($_SESSION["roles"])) {

    foreach ($_SESSION["roles"] as $rol) {

        if (
            (string)$rol === "4" ||
            strtoupper(trim((string)$rol)) === "COORDINADOR"
        ) {

            $es_coordinador = true;
            break;

        }

    }

} else {

    if (
        (string)$_SESSION["roles"] === "4" ||
        strtoupper(trim((string)$_SESSION["roles"])) === "COORDINADOR"
    ) {

        $es_coordinador = true;

    }

}


if (!$es_coordinador) {

    header("Location: ../indexloguin.php");
    exit();

}


/*=========================================================
    VALIDAR ID DEL TUTOR
=========================================================*/

if (
    !isset($_GET["id_tutor"]) ||
    !is_numeric($_GET["id_tutor"])
) {

    header("Location: index.php");
    exit();

}


$id_tutor = (int)$_GET["id_tutor"];


if ($id_tutor <= 0) {

    header("Location: index.php");
    exit();

}


/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../base_pit/conect_pit.php");


/*=========================================================
    OBTENER CARRERA DEL COORDINADOR
=========================================================*/

$sql = "

    SELECT

        id_personal,
        nombre,
        apellido_p,
        apellido_m,
        carrera

    FROM personal_academico

    WHERE id_usuario = ?
      AND activo = 1

    LIMIT 1

";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die(
        "Error al consultar los datos del coordinador: " .
        $conn->error
    );

}

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    $stmt->close();

    die(
        "No se encontraron los datos académicos del coordinador."
    );

}

$coordinador = $resultado->fetch_assoc();

$stmt->close();


$carrera_coordinador =
    trim($coordinador["carrera"]);


/*=========================================================
    OBTENER TUTOR
=========================================================*/

$sql = "

    SELECT

        pa.id_personal,
        pa.no_empleado,
        pa.nombre,
        pa.apellido_p,
        pa.apellido_m,
        pa.carrera

    FROM personal_academico pa

    WHERE pa.id_personal = ?
      AND pa.activo = 1

    LIMIT 1

";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die(
        "Error al consultar el tutor: " .
        $conn->error
    );

}

$stmt->bind_param(
    "i",
    $id_tutor
);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    $stmt->close();

    header("Location: index.php");
    exit();

}

$tutor = $resultado->fetch_assoc();

$stmt->close();


/*=========================================================
    SEGURIDAD

    El tutor debe pertenecer a la misma carrera
    que el coordinador.
=========================================================*/

if (
    trim($tutor["carrera"]) !==
    $carrera_coordinador
) {

    header("Location: index.php");
    exit();

}


/*=========================================================
    NOMBRE DEL TUTOR
=========================================================*/

$nombre_tutor = trim(

    $tutor["nombre"] . " " .
    $tutor["apellido_p"] . " " .
    $tutor["apellido_m"]

);


/*=========================================================
    OBTENER CANALIZACIONES
=========================================================*/

$canalizaciones = [];

$sql = "

    SELECT

        c.id_canalizacion,
        c.id_tutorado,
        c.observaciones,
        c.acciones,
        c.estado,
        c.fecha_canalizacion,
        c.fecha_cierre,
        c.nuevos_reportes,

        t.matricula,
        t.nombre AS alumno_nombre,
        t.apellido_p AS alumno_apellido_p,
        t.apellido_m AS alumno_apellido_m,
        t.carrera,
        t.grupo

    FROM canalizaciones c

    INNER JOIN tutorados t
        ON t.id_tutorado = c.id_tutorado

    WHERE c.id_tutor = ?
      AND t.carrera = ?
      AND t.activo = 1

    ORDER BY c.fecha_canalizacion DESC

";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die(
        "Error al consultar canalizaciones: " .
        $conn->error
    );

}

$stmt->bind_param(
    "is",
    $id_tutor,
    $carrera_coordinador
);

$stmt->execute();

$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {

    $canalizaciones[] = $fila;

}

$stmt->close();


/*=========================================================
    ESTADÍSTICAS
=========================================================*/

$total_canalizaciones =
    count($canalizaciones);


$alumnos = [];

foreach ($canalizaciones as $canalizacion) {

    $alumnos[
        $canalizacion["id_tutorado"]
    ] = true;

}

$total_alumnos =
    count($alumnos);


/*=========================================================
    CONTADORES POR ESTADO
=========================================================*/

$estados = [

    "PENDIENTE" => 0,
    "EN PROCESO" => 0,
    "ATENDIDO" => 0,
    "CERRADO" => 0

];


foreach ($canalizaciones as $canalizacion) {

    $estado =
        $canalizacion["estado"];

    if (isset($estados[$estado])) {

        $estados[$estado]++;

    }

}


/*=========================================================
    FUNCIONES
=========================================================*/

function textoEstadoDetalle($estado)
{

    switch ($estado) {

        case "PENDIENTE":
            return "Pendiente";

        case "EN PROCESO":
            return "En proceso";

        case "ATENDIDO":
            return "Atendido";

        case "CERRADO":
            return "Cerrado";

        default:
            return $estado;

    }

}


function claseEstadoDetalle($estado)
{

    switch ($estado) {

        case "PENDIENTE":
            return "estado-pendiente";

        case "EN PROCESO":
            return "estado-proceso";

        case "ATENDIDO":
            return "estado-atendido";

        case "CERRADO":
            return "estado-cerrado";

        default:
            return "";

    }

}


function fechaDetalle($fecha)
{

    if (empty($fecha)) {
        return "-";
    }

    $timestamp = strtotime($fecha);

    if (!$timestamp) {
        return $fecha;
    }

    return date(
        "d/m/Y H:i",
        $timestamp
    );

}


$nombre_tutor_html =
    htmlspecialchars(
        $nombre_tutor,
        ENT_QUOTES,
        "UTF-8"
    );

$carrera_html =
    htmlspecialchars(
        $carrera_coordinador,
        ENT_QUOTES,
        "UTF-8"
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
        Canalizaciones del tutor | PIT V.4.0
    </title>

    <link
        rel="stylesheet"
        href="coordinador.css"
    >
            <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<?php include("../includes_pit/sidebar_docente.php"); ?>


<div class="coordinador-contenedor">


    <!-- =====================================================
         ENCABEZADO
    ====================================================== -->

    <header class="coordinador-header">

        <div class="header-principal">

            <span class="etiqueta">
                DETALLE DE CANALIZACIONES
            </span>

            <h1>

                <?php
                echo $nombre_tutor_html;
                ?>

            </h1>

            <p>

                Carrera:

                <strong>
                    <?php
                    echo $carrera_html;
                    ?>
                </strong>

            </p>

        </div>


        <div class="header-acciones">

            <a
                href="index.php"
                class="btn-regresar"
            >
                ← Regresar
            </a>

        </div>

    </header>


    <!-- =====================================================
         INFORMACIÓN DEL TUTOR
    ====================================================== -->

    <section class="informacion-tutor">

        <div>

            <span>
                Tutor
            </span>

            <strong>
                <?php
                echo $nombre_tutor_html;
                ?>
            </strong>

        </div>


        <div>

            <span>
                No. empleado
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $tutor["no_empleado"],
                    ENT_QUOTES,
                    "UTF-8"
                );

                ?>

            </strong>

        </div>


        <div>

            <span>
                Carrera
            </span>

            <strong>
                <?php
                echo $carrera_html;
                ?>
            </strong>

        </div>

    </section>


    <!-- =====================================================
         ESTADÍSTICAS
    ====================================================== -->

    <section class="estadisticas-grid">


        <article class="estadistica-card">

            <div class="estadistica-icono">
                👨‍🎓
            </div>

            <div>

                <span>
                    Alumnos
                </span>

                <strong>
                    <?php
                    echo $total_alumnos;
                    ?>
                </strong>

            </div>

        </article>


        <article class="estadistica-card">

            <div class="estadistica-icono">
                📋
            </div>

            <div>

                <span>
                    Canalizaciones
                </span>

                <strong>
                    <?php
                    echo $total_canalizaciones;
                    ?>
                </strong>

            </div>

        </article>


        <article class="estadistica-card">

            <div class="estadistica-icono">
                ⏳
            </div>

            <div>

                <span>
                    Pendientes
                </span>

                <strong>
                    <?php
                    echo $estados["PENDIENTE"];
                    ?>
                </strong>

            </div>

        </article>


        <article class="estadistica-card">

            <div class="estadistica-icono">
                ✓
            </div>

            <div>

                <span>
                    Atendidas
                </span>

                <strong>
                    <?php
                    echo $estados["ATENDIDO"];
                    ?>
                </strong>

            </div>

        </article>


    </section>


    <!-- =====================================================
         CANALIZACIONES
    ====================================================== -->

    <section class="panel">

        <div class="panel-titulo">

            <span class="etiqueta">
                REGISTROS
            </span>

            <h2>
                Alumnos canalizados
            </h2>

            <p>
                Canalizaciones realizadas por este tutor.
            </p>

        </div>


        <?php if (!empty($canalizaciones)): ?>


            <div class="tabla-contenedor">

                <table class="tabla">

                    <thead>

                        <tr>

                            <th>
                                Alumno
                            </th>

                            <th>
                                Matrícula
                            </th>

                            <th>
                                Grupo
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Fecha
                            </th>

                            <th>
                                Reportes
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach (
                        $canalizaciones
                        as $canalizacion
                    ): ?>


                        <?php

                        $nombre_alumno = trim(

                            $canalizacion["alumno_nombre"] . " " .
                            $canalizacion["alumno_apellido_p"] . " " .
                            $canalizacion["alumno_apellido_m"]

                        );

                        ?>


                        <tr>


                            <td>

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $nombre_alumno,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </strong>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $canalizacion["matricula"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $canalizacion["grupo"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                                ?>

                            </td>


                            <td>

                                <span
                                    class="estado <?php echo claseEstadoDetalle($canalizacion["estado"]); ?>"
                                >

                                    <?php

                                    echo textoEstadoDetalle(
                                        $canalizacion["estado"]
                                    );

                                    ?>

                                </span>

                            </td>


                            <td>

                                <?php

                                echo fechaDetalle(
                                    $canalizacion[
                                        "fecha_canalizacion"
                                    ]
                                );

                                ?>

                            </td>


                            <td>

                                <span class="numero">

                                    <?php

                                    echo (int)(
                                        $canalizacion[
                                            "nuevos_reportes"
                                        ] ?? 0
                                    );

                                    ?>

                                </span>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="vacio">

                <div class="vacio-icono">
                    📭
                </div>

                <h3>
                    Sin canalizaciones
                </h3>

                <p>
                    Este tutor no tiene canalizaciones
                    registradas para su carrera.
                </p>

            </div>


        <?php endif; ?>


    </section>


    <!-- =====================================================
         DISTRIBUCIÓN DE ESTADOS
    ====================================================== -->

    <section class="panel">

        <div class="panel-titulo">

            <span class="etiqueta">
                ESTADOS
            </span>

            <h2>
                Situación de las canalizaciones
            </h2>

        </div>


        <div class="estados-grid">


            <div class="estado-resumen">

                <span class="punto pendiente"></span>

                <div>

                    <span>
                        Pendientes
                    </span>

                    <strong>
                        <?php
                        echo $estados["PENDIENTE"];
                        ?>
                    </strong>

                </div>

            </div>


            <div class="estado-resumen">

                <span class="punto proceso"></span>

                <div>

                    <span>
                        En proceso
                    </span>

                    <strong>
                        <?php
                        echo $estados["EN PROCESO"];
                        ?>
                    </strong>

                </div>

            </div>


            <div class="estado-resumen">

                <span class="punto atendido"></span>

                <div>

                    <span>
                        Atendidas
                    </span>

                    <strong>
                        <?php
                        echo $estados["ATENDIDO"];
                        ?>
                    </strong>

                </div>

            </div>


            <div class="estado-resumen">

                <span class="punto cerrado"></span>

                <div>

                    <span>
                        Cerradas
                    </span>

                    <strong>
                        <?php
                        echo $estados["CERRADO"];
                        ?>
                    </strong>

                </div>

            </div>


        </div>

    </section>


</div>


<script
    src="js/coordinador.js"
></script>

</body>

</html>
<?php include '../../includes/footer.php'; ?>
