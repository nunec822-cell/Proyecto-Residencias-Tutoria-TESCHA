<?php
/*=========================================================
    ALUMNOS CON CANALIZACIÓN ACTIVA
    JEFE DE CARRERA
    SIST V.4.0 - PIT V.4.0
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*=========================================================
    VALIDAR SESIÓN
=========================================================*/

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../index.php");
    exit;
}

$id_usuario = (int) $_SESSION['id_usuario'];

/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../../base_pit/conect_pit.php");

/*=========================================================
    VALIDAR ROL
=========================================================*/

$es_jefe_carrera = false;

if (isset($_SESSION['rol'])) {

    if (
        $_SESSION['rol'] === 'JEFE_CARRERA' ||
        $_SESSION['rol'] === 'JEFE DE CARRERA'
    ) {
        $es_jefe_carrera = true;
    }
}

if (isset($_SESSION['roles'])) {

    if (is_array($_SESSION['roles'])) {

        foreach ($_SESSION['roles'] as $rol) {

            if (
                $rol === 'JEFE_CARRERA' ||
                $rol === 'JEFE DE CARRERA'
            ) {
                $es_jefe_carrera = true;
                break;
            }
        }

    } else {

        if (
            $_SESSION['roles'] === 'JEFE_CARRERA' ||
            $_SESSION['roles'] === 'JEFE DE CARRERA'
        ) {
            $es_jefe_carrera = true;
        }
    }
}

if (!$es_jefe_carrera) {

    $sql_rol = "
        SELECT 1
        FROM usuario_tipo ut
        INNER JOIN tipos t
            ON t.id_tipo = ut.id_tipo
        WHERE ut.id_usuario = ?
          AND t.nombre = 'JEFE_CARRERA'
        LIMIT 1
    ";

    $stmt_rol = $conexion->prepare($sql_rol);

    if ($stmt_rol) {

        $stmt_rol->bind_param("i", $id_usuario);
        $stmt_rol->execute();

        $resultado_rol = $stmt_rol->get_result();

        if ($resultado_rol->num_rows > 0) {
            $es_jefe_carrera = true;
        }

        $stmt_rol->close();
    }
}

if (!$es_jefe_carrera) {
    http_response_code(403);
    die("Acceso no autorizado.");
}

/*=========================================================
    GRUPO SOLICITADO
=========================================================*/

if (!isset($_GET['grupo']) || trim($_GET['grupo']) === '') {

    header("Location: index.php");
    exit;
}

$grupo = trim($_GET['grupo']);

/*
    Limitar longitud para evitar valores absurdos.
*/
if (mb_strlen($grupo, 'UTF-8') > 20) {
    header("Location: index.php");
    exit;
}

/*=========================================================
    OBTENER CARRERA DEL JEFE
=========================================================*/

$sql_jefe = "
    SELECT
        id_personal,
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

$stmt_jefe = $conn->prepare($sql_jefe);

if (!$stmt_jefe) {
    die("Error al preparar la consulta del jefe.");
}

$stmt_jefe->bind_param("i", $id_usuario);
$stmt_jefe->execute();

$resultado_jefe = $stmt_jefe->get_result();

if ($resultado_jefe->num_rows === 0) {
    $stmt_jefe->close();
    die("No se encontró la información del jefe de carrera.");
}

$jefe = $resultado_jefe->fetch_assoc();

$stmt_jefe->close();

$carrera_jefe = trim($jefe['carrera']);

$nombre_jefe = trim(
    $jefe['nombre'] . ' ' .
    $jefe['apellido_p'] . ' ' .
    $jefe['apellido_m']
);

/*=========================================================
    OBTENER ALUMNOS
=========================================================*/

/*
    Se obtiene una fila por canalización activa.

    Si un alumno tiene varias canalizaciones activas,
    se conserva la más reciente mediante MAX(id_canalizacion)
    en una subconsulta.

    Esto evita duplicar alumnos en la lista.
*/

$sql_alumnos = "
    SELECT
        t.id_tutorado,
        t.matricula,
        t.nombre,
        t.apellido_p,
        t.apellido_m,
        t.carrera,
        t.grupo,

        c.id_canalizacion,
        c.estado,
        c.observaciones,
        c.acciones,
        c.fecha_canalizacion,
        c.fecha_cierre,
        c.nuevos_reportes

    FROM tutorados t

    INNER JOIN canalizaciones c
        ON c.id_canalizacion = (
            SELECT MAX(c2.id_canalizacion)
            FROM canalizaciones c2
            WHERE c2.id_tutorado = t.id_tutorado
              AND c2.estado IN (
                    'PENDIENTE',
                    'EN PROCESO',
                    'ATENDIDO'
              )
        )

    WHERE t.carrera = ?
      AND t.grupo = ?
      AND t.activo = 1

    ORDER BY
        t.apellido_p ASC,
        t.apellido_m ASC,
        t.nombre ASC
";

$stmt_alumnos = $conn->prepare($sql_alumnos);

if (!$stmt_alumnos) {
    die("Error al preparar la consulta de alumnos.");
}

$stmt_alumnos->bind_param(
    "ss",
    $carrera_jefe,
    $grupo
);

$stmt_alumnos->execute();

$resultado_alumnos = $stmt_alumnos->get_result();

$alumnos = [];

while ($fila = $resultado_alumnos->fetch_assoc()) {
    $alumnos[] = $fila;
}

$stmt_alumnos->close();

/*=========================================================
    CONTADORES
=========================================================*/

$total_alumnos_canalizados = count($alumnos);

/*=========================================================
    ESTADÍSTICAS POR ESTADO
=========================================================*/

$pendientes = 0;
$en_proceso = 0;
$atendidos = 0;

foreach ($alumnos as $alumno) {

    switch ($alumno['estado']) {

        case 'PENDIENTE':
            $pendientes++;
            break;

        case 'EN PROCESO':
            $en_proceso++;
            break;

        case 'ATENDIDO':
            $atendidos++;
            break;
    }
}

/*=========================================================
    FORMATO DE FECHA
=========================================================*/

function fecha_espanol($fecha)
{
    if (!$fecha) {
        return 'Sin fecha';
    }

    $timestamp = strtotime($fecha);

    if (!$timestamp) {
        return 'Sin fecha';
    }

    $meses = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    ];

    $dia = date('d', $timestamp);
    $mes = $meses[(int) date('m', $timestamp)];
    $anio = date('Y', $timestamp);

    return $dia . ' de ' . $mes . ' de ' . $anio;
}

/*=========================================================
    SIDEBAR
=========================================================*/

$pagina_actual = $_SERVER['PHP_SELF'];

$base_url = "/SIST V.4.0/PIT_V.4.0/";

$sidebar_path = "../../includes_pit/sidebar_jefe_carrera.php";

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
        Alumnos canalizados | PIT V.4.0
    </title>

    <link
        rel="stylesheet"
        href="canalizacionesporcarrera.css"
    >

</head>

<body>

<div class="layout">

    <!--==================================================
        SIDEBAR
    ===================================================-->

    <?php

    if (file_exists($sidebar_path)) {
        require_once($sidebar_path);
    }

    ?>


    <!--==================================================
        CONTENIDO
    ===================================================-->

    <main class="contenido-principal">

        <!--================================================
            BOTÓN REGRESAR
        =================================================-->

        <a
            href="index.php"
            class="btn-regresar"
        >
            <span>←</span>
            Volver a grupos
        </a>


        <!--================================================
            ENCABEZADO
        =================================================-->

        <section class="encabezado-pagina encabezado-alumnos">

            <div class="encabezado-texto">

                <span class="etiqueta-seccion">
                    CANALIZACIONES ACTIVAS
                </span>

                <h1>
                    Grupo <?= htmlspecialchars($grupo, ENT_QUOTES, 'UTF-8'); ?>
                </h1>

                <p>
                    Alumnos de
                    <strong>
                        <?= htmlspecialchars($carrera_jefe, ENT_QUOTES, 'UTF-8'); ?>
                    </strong>
                    que cuentan actualmente con una canalización activa.
                </p>

            </div>

            <div class="contador-principal">

                <strong>
                    <?= $total_alumnos_canalizados; ?>
                </strong>

                <span>
                    <?= $total_alumnos_canalizados === 1
                        ? 'alumno canalizado'
                        : 'alumnos canalizados'; ?>
                </span>

            </div>

        </section>


        <!--================================================
            ESTADÍSTICAS
        =================================================-->

        <section class="resumen-grid resumen-alumnos">

            <article class="resumen-card">

                <div class="resumen-icono icono-pendiente">
                    ⏳
                </div>

                <div class="resumen-info">

                    <span>
                        Pendientes
                    </span>

                    <strong>
                        <?= $pendientes; ?>
                    </strong>

                </div>

            </article>


            <article class="resumen-card">

                <div class="resumen-icono icono-proceso">
                    🔄
                </div>

                <div class="resumen-info">

                    <span>
                        En proceso
                    </span>

                    <strong>
                        <?= $en_proceso; ?>
                    </strong>

                </div>

            </article>


            <article class="resumen-card">

                <div class="resumen-icono icono-atendido">
                    ✓
                </div>

                <div class="resumen-info">

                    <span>
                        Atendidos
                    </span>

                    <strong>
                        <?= $atendidos; ?>
                    </strong>

                </div>

            </article>

        </section>


        <!--================================================
            LISTA DE ALUMNOS
        =================================================-->

        <section class="lista-alumnos-seccion">

            <div class="lista-encabezado">

                <div>

                    <span class="mini-etiqueta">
                        ALUMNOS
                    </span>

                    <h2>
                        Canalizaciones activas
                    </h2>

                    <p>
                        Consulta la información principal de cada alumno.
                    </p>

                </div>

                <?php if ($total_alumnos_canalizados > 0): ?>

                    <div class="buscador-contenedor">

                        <span class="buscador-icono">
                            🔎
                        </span>

                        <input
                            type="text"
                            id="buscarAlumno"
                            placeholder="Buscar alumno o matrícula..."
                            autocomplete="off"
                        >

                    </div>

                <?php endif; ?>

            </div>


            <?php if ($total_alumnos_canalizados > 0): ?>

                <div
                    class="tabla-contenedor"
                    id="tablaAlumnos"
                >

                    <table class="tabla-alumnos">

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
                                    Fecha de canalización
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($alumnos as $alumno): ?>

                                <?php

                                $nombre_alumno = trim(
                                    $alumno['nombre'] . ' ' .
                                    $alumno['apellido_p'] . ' ' .
                                    $alumno['apellido_m']
                                );

                                $estado = $alumno['estado'];

                                $clase_estado = '';

                                switch ($estado) {

                                    case 'PENDIENTE':
                                        $clase_estado = 'estado-pendiente';
                                        break;

                                    case 'EN PROCESO':
                                        $clase_estado = 'estado-proceso';
                                        break;

                                    case 'ATENDIDO':
                                        $clase_estado = 'estado-atendido';
                                        break;
                                }

                                ?>

                                <tr
                                    class="fila-alumno"
                                    data-busqueda="<?= htmlspecialchars(
                                        mb_strtolower(
                                            $nombre_alumno . ' ' .
                                            $alumno['matricula'],
                                            'UTF-8'
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>"
                                >

                                    <td>

                                        <div class="alumno-info">

                                            <div class="avatar-alumno">
                                                <?= htmlspecialchars(
                                                    mb_strtoupper(
                                                        mb_substr(
                                                            $alumno['nombre'],
                                                            0,
                                                            1,
                                                            'UTF-8'
                                                        ),
                                                        'UTF-8'
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </div>

                                            <div>

                                                <strong>
                                                    <?= htmlspecialchars(
                                                        $nombre_alumno,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ); ?>
                                                </strong>

                                                <span>
                                                    Alumno canalizado
                                                </span>

                                            </div>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="matricula">
                                            <?= htmlspecialchars(
                                                $alumno['matricula'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </span>

                                    </td>


                                    <td>

                                        <span class="grupo-mini">
                                            <?= htmlspecialchars(
                                                $alumno['grupo'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </span>

                                    </td>


                                    <td>

                                        <span
                                            class="estado-pill <?= $clase_estado; ?>"
                                        >

                                            <span class="estado-punto"></span>

                                            <?= htmlspecialchars(
                                                $estado,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>

                                        </span>

                                    </td>


                                    <td>

                                        <span class="fecha-canalizacion">
                                            <?= fecha_espanol(
                                                $alumno['fecha_canalizacion']
                                            ); ?>
                                        </span>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <div
                    id="sinResultadosAlumnos"
                    class="sin-resultados"
                >

                    <div>
                        🔎
                    </div>

                    <h3>
                        No encontramos al alumno
                    </h3>

                    <p>
                        Intenta buscar utilizando su nombre o matrícula.
                    </p>

                </div>

            <?php else: ?>

                <div class="estado-vacio-general estado-vacio-alumnos">

                    <div class="estado-vacio-icono">
                        ✓
                    </div>

                    <h3>
                        No hay canalizaciones activas
                    </h3>

                    <p>
                        Actualmente no existen alumnos con una
                        canalización activa en el grupo
                        <strong>
                            <?= htmlspecialchars(
                                $grupo,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </strong>.
                    </p>

                    <a
                        href="index.php"
                        class="btn-principal"
                    >
                        ← Regresar a grupos
                    </a>

                </div>

            <?php endif; ?>

        </section>


        <!--================================================
            INFORMACIÓN
        =================================================-->

        <section class="informacion-panel">

            <div class="informacion-icono">
                🔐
            </div>

            <div>

                <h3>
                    Información protegida
                </h3>

                <p>
                    Esta consulta está limitada a los alumnos
                    pertenecientes a la carrera asignada al jefe
                    de carrera que inició sesión.
                </p>

            </div>

        </section>

    </main>

</div>


<script src="canalizacionesporcarrera.js"></script>

</body>

</html>