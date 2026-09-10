<?php
/*=========================================================
    MODULO COORDINADOR
    SIST V.4.0 - PIT V.4.0

    index.php

    Funciones:
    - Validar sesión
    - Validar rol COORDINADOR
    - Obtener carrera del coordinador
    - Mostrar estadísticas
    - Mostrar estados
    - Mostrar tutores con canalizaciones
    - Mostrar canalizaciones recientes
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


/*=========================================================
    SI NO ES COORDINADOR
=========================================================*/

if (!$es_coordinador) {

    header("Location: ../indexloguin.php");
    exit();

}


/*=========================================================
    CONEXIÓN A BASE DE DATOS
=========================================================*/

require_once("../base_pit/conect_pit.php");


/*=========================================================
    OBTENER INFORMACIÓN DEL COORDINADOR
=========================================================*/

$sql = "

    SELECT
        id_personal,
        id_usuario,
        no_empleado,
        nombre,
        apellido_p,
        apellido_m,
        carrera,
        fotografia

    FROM personal_academico

    WHERE id_usuario = ?
      AND activo = 1

    LIMIT 1

";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die(
        "Error al preparar la consulta del coordinador: " .
        $conn->error
    );

}

$stmt->bind_param("i", $id_usuario);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    $stmt->close();

    die(
        "No se encontraron los datos del coordinador."
    );

}

$coordinador = $resultado->fetch_assoc();

$stmt->close();


/*=========================================================
    DATOS DEL COORDINADOR
=========================================================*/

$nombre_coordinador = trim(

    $coordinador["nombre"] . " " .
    $coordinador["apellido_p"] . " " .
    $coordinador["apellido_m"]

);

$carrera_coordinador = trim(
    $coordinador["carrera"]
);


/*=========================================================
    VALIDAR CARRERA
=========================================================*/

if ($carrera_coordinador === "") {

    die(
        "El coordinador no tiene una carrera registrada."
    );

}


/*=========================================================
    DATOS SEGUROS PARA HTML
=========================================================*/

$nombre_html = htmlspecialchars(
    $nombre_coordinador,
    ENT_QUOTES,
    "UTF-8"
);

$carrera_html = htmlspecialchars(
    $carrera_coordinador,
    ENT_QUOTES,
    "UTF-8"
);


/*=========================================================
    TOTAL DE CANALIZACIONES
=========================================================*/

$sql = "

    SELECT
        COUNT(*) AS total

    FROM canalizaciones c

    INNER JOIN tutorados t
        ON t.id_tutorado = c.id_tutorado

    WHERE t.carrera = ?
      AND t.activo = 1

";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error al consultar canalizaciones.");
}

$stmt->bind_param(
    "s",
    $carrera_coordinador
);

$stmt->execute();

$resultado = $stmt->get_result();

$fila = $resultado->fetch_assoc();

$total_canalizaciones =
    (int)($fila["total"] ?? 0);

$stmt->close();


/*=========================================================
    TOTAL DE ALUMNOS CANALIZADOS
=========================================================*/

$sql = "

    SELECT
        COUNT(DISTINCT c.id_tutorado) AS total

    FROM canalizaciones c

    INNER JOIN tutorados t
        ON t.id_tutorado = c.id_tutorado

    WHERE t.carrera = ?
      AND t.activo = 1

";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error al consultar alumnos.");
}

$stmt->bind_param(
    "s",
    $carrera_coordinador
);

$stmt->execute();

$resultado = $stmt->get_result();

$fila = $resultado->fetch_assoc();

$total_alumnos =
    (int)($fila["total"] ?? 0);

$stmt->close();


/*=========================================================
    TOTAL DE TUTORES
=========================================================*/

$sql = "

    SELECT
        COUNT(DISTINCT c.id_tutor) AS total

    FROM canalizaciones c

    INNER JOIN tutorados t
        ON t.id_tutorado = c.id_tutorado

    INNER JOIN personal_academico pa
        ON pa.id_personal = c.id_tutor

    WHERE t.carrera = ?
      AND t.activo = 1
      AND pa.activo = 1

";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error al consultar tutores.");
}

$stmt->bind_param(
    "s",
    $carrera_coordinador
);

$stmt->execute();

$resultado = $stmt->get_result();

$fila = $resultado->fetch_assoc();

$total_tutores =
    (int)($fila["total"] ?? 0);

$stmt->close();


/*=========================================================
    ESTADOS DE CANALIZACIONES
=========================================================*/

$estados = [

    "PENDIENTE" => 0,
    "EN PROCESO" => 0,
    "ATENDIDO" => 0,
    "CERRADO" => 0

];


$sql = "

    SELECT
        c.estado,
        COUNT(*) AS total

    FROM canalizaciones c

    INNER JOIN tutorados t
        ON t.id_tutorado = c.id_tutorado

    WHERE t.carrera = ?
      AND t.activo = 1

    GROUP BY c.estado

";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error al consultar estados.");
}

$stmt->bind_param(
    "s",
    $carrera_coordinador
);

$stmt->execute();

$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {

    $estado = $fila["estado"];

    if (isset($estados[$estado])) {

        $estados[$estado] =
            (int)$fila["total"];

    }

}

$stmt->close();


/*=========================================================
    TUTORES CON CANALIZACIONES
=========================================================*/

$tutores = [];

$sql = "

    SELECT

        pa.id_personal,

        pa.nombre,
        pa.apellido_p,
        pa.apellido_m,
        pa.no_empleado,

        COUNT(DISTINCT c.id_tutorado)
            AS alumnos,

        COUNT(c.id_canalizacion)
            AS canalizaciones

    FROM canalizaciones c

    INNER JOIN tutorados t
        ON t.id_tutorado = c.id_tutorado

    INNER JOIN personal_academico pa
        ON pa.id_personal = c.id_tutor

    WHERE t.carrera = ?
      AND t.activo = 1
      AND pa.activo = 1

    GROUP BY

        pa.id_personal,
        pa.nombre,
        pa.apellido_p,
        pa.apellido_m,
        pa.no_empleado

    ORDER BY canalizaciones DESC

";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die(
        "Error al consultar tutores: " .
        $conn->error
    );

}

$stmt->bind_param(
    "s",
    $carrera_coordinador
);

$stmt->execute();

$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {

    $tutores[] = $fila;

}

$stmt->close();


/*=========================================================
    CANALIZACIONES RECIENTES
=========================================================*/

$recientes = [];

$sql = "

    SELECT

        c.id_canalizacion,
        c.estado,
        c.fecha_canalizacion,

        t.id_tutorado,
        t.matricula,
        t.nombre AS alumno_nombre,
        t.apellido_p AS alumno_apellido_p,
        t.apellido_m AS alumno_apellido_m,
        t.grupo,

        pa.nombre AS tutor_nombre,
        pa.apellido_p AS tutor_apellido_p,
        pa.apellido_m AS tutor_apellido_m

    FROM canalizaciones c

    INNER JOIN tutorados t
        ON t.id_tutorado = c.id_tutorado

    INNER JOIN personal_academico pa
        ON pa.id_personal = c.id_tutor

    WHERE t.carrera = ?
      AND t.activo = 1
      AND pa.activo = 1

    ORDER BY c.fecha_canalizacion DESC

    LIMIT 10

";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die(
        "Error al consultar canalizaciones recientes: " .
        $conn->error
    );

}

$stmt->bind_param(
    "s",
    $carrera_coordinador
);

$stmt->execute();

$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {

    $recientes[] = $fila;

}

$stmt->close();


/*=========================================================
    FUNCIONES
=========================================================*/

function estadoTexto($estado)
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


function estadoClase($estado)
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


function formatoFecha($fecha)
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
        Coordinación | PIT V.4.0
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
                COORDINACIÓN ACADÉMICA
            </span>

            <h1>
                Panel de seguimiento
            </h1>

            <p>

                Bienvenido,

                <strong>
                    <?php echo $nombre_html; ?>
                </strong>

            </p>

        </div>


        <div class="carrera-box">

            <span>
                Carrera
            </span>

            <strong>
                <?php echo $carrera_html; ?>
            </strong>

        </div>

    </header>


    <!-- =====================================================
         ESTADÍSTICAS
    ====================================================== -->

    <section class="estadisticas-grid">


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
                👨‍🎓
            </div>

            <div>

                <span>
                    Alumnos canalizados
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
                👨‍🏫
            </div>

            <div>

                <span>
                    Tutores involucrados
                </span>

                <strong>
                    <?php
                    echo $total_tutores;
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


    </section>


    <!-- =====================================================
         ESTADOS
    ====================================================== -->

    <section class="panel">

        <div class="panel-titulo">

            <span class="etiqueta">
                SEGUIMIENTO
            </span>

            <h2>
                Estado de las canalizaciones
            </h2>

            <p>
                Resumen de atención de los alumnos de tu carrera.
            </p>

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


    <!-- =====================================================
         TUTORES
    ====================================================== -->

    <section class="panel">

        <div class="panel-titulo">

            <span class="etiqueta">
                TUTORES
            </span>

            <h2>
                Canalizaciones por tutor
            </h2>

            <p>
                Consulta cuántos alumnos canalizados tiene cada tutor.
            </p>

        </div>


        <?php if (!empty($tutores)): ?>


            <div class="tabla-contenedor">

                <table class="tabla">

                    <thead>

                        <tr>

                            <th>
                                Tutor
                            </th>

                            <th>
                                No. empleado
                            </th>

                            <th>
                                Alumnos
                            </th>

                            <th>
                                Canalizaciones
                            </th>

                            <th>
                                Detalle
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach ($tutores as $tutor): ?>


                        <?php

                        $nombre_tutor = trim(

                            $tutor["nombre"] . " " .
                            $tutor["apellido_p"] . " " .
                            $tutor["apellido_m"]

                        );

                        $nombre_tutor_html =
                            htmlspecialchars(
                                $nombre_tutor,
                                ENT_QUOTES,
                                "UTF-8"
                            );

                        ?>


                        <tr>

                            <td>

                                <div class="persona">

                                    <div class="avatar">

                                        <?php
                                        echo strtoupper(
                                            substr(
                                                $tutor["nombre"],
                                                0,
                                                1
                                            )
                                        );
                                        ?>

                                    </div>

                                    <strong>
                                        <?php
                                        echo $nombre_tutor_html;
                                        ?>
                                    </strong>

                                </div>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $tutor["no_empleado"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                                ?>

                            </td>


                            <td>

                                <span class="numero">
                                    <?php
                                    echo (int)$tutor["alumnos"];
                                    ?>
                                </span>

                            </td>


                            <td>

                                <span class="numero">
                                    <?php
                                    echo (int)$tutor["canalizaciones"];
                                    ?>
                                </span>

                            </td>


                            <td>

                                <a
                                    class="btn"
                                    href="detalle_canalizaciones.php?id_tutor=<?php echo (int)$tutor["id_personal"]; ?>"
                                >
                                    Ver canalizaciones
                                </a>

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
                    No hay canalizaciones
                </h3>

                <p>
                    Actualmente no existen canalizaciones
                    registradas para esta carrera.
                </p>

            </div>


        <?php endif; ?>


    </section>


    <!-- =====================================================
         CANALIZACIONES RECIENTES
    ====================================================== -->

    <section class="panel">

        <div class="panel-titulo">

            <span class="etiqueta">
                ACTIVIDAD RECIENTE
            </span>

            <h2>
                Últimas canalizaciones
            </h2>

        </div>


        <?php if (!empty($recientes)): ?>


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
                                Tutor
                            </th>

                            <th>
                                Estado
                            </th>

                            <th>
                                Fecha
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php foreach ($recientes as $fila): ?>


                        <?php

                        $alumno = trim(

                            $fila["alumno_nombre"] . " " .
                            $fila["alumno_apellido_p"] . " " .
                            $fila["alumno_apellido_m"]

                        );

                        $tutor = trim(

                            $fila["tutor_nombre"] . " " .
                            $fila["tutor_apellido_p"] . " " .
                            $fila["tutor_apellido_m"]

                        );

                        ?>


                        <tr>

                            <td>

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $alumno,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </strong>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $fila["matricula"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $fila["grupo"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $tutor,
                                    ENT_QUOTES,
                                    "UTF-8"
                                );

                                ?>

                            </td>


                            <td>

                                <span
                                    class="estado <?php echo estadoClase($fila["estado"]); ?>"
                                >

                                    <?php

                                    echo estadoTexto(
                                        $fila["estado"]
                                    );

                                    ?>

                                </span>

                            </td>


                            <td>

                                <?php

                                echo formatoFecha(
                                    $fila["fecha_canalizacion"]
                                );

                                ?>

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
                    Sin actividad reciente
                </h3>

                <p>
                    No existen canalizaciones registradas.
                </p>

            </div>


        <?php endif; ?>


    </section>


</div>


<script
    src="js/coordinador.js"
></script>

</body>

</html>
<?php include '../../includes/footer.php'; ?>
