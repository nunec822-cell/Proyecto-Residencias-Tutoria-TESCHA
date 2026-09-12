<?php
/*=========================================================
    CANALIZACIONES GENERALES
    DIRECTIVOS
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
    VALIDAR QUE SEA DIRECTIVO
=========================================================*/

$es_directivo = false;

$sql_rol = "
    SELECT 1
    FROM usuario_tipo ut
    INNER JOIN tipos t
        ON t.id_tipo = ut.id_tipo
    WHERE ut.id_usuario = ?
      AND (
            t.nombre = 'DIRECTIVO'
            OR t.nombre = 'DIRECTIVOS'
          )
    LIMIT 1
";

$stmt_rol = $conn->prepare($sql_rol);

if (!$stmt_rol) {
    die("Error al validar el rol del usuario.");
}

$stmt_rol->bind_param("i", $id_usuario);
$stmt_rol->execute();

$resultado_rol = $stmt_rol->get_result();

if ($resultado_rol->num_rows > 0) {
    $es_directivo = true;
}

$stmt_rol->close();


if (!$es_directivo) {
    http_response_code(403);
    die("Acceso no autorizado.");
}


/*=========================================================
    OBTENER INFORMACIÓN DEL DIRECTIVO
=========================================================*/

$sql_directivo = "
    SELECT
        id_directivo,
        no_empleado,
        nombre,
        apellido_p,
        apellido_m
    FROM directivos
    WHERE id_usuario = ?
      AND activo = 1
    LIMIT 1
";

$stmt_directivo = $conn->prepare($sql_directivo);

if (!$stmt_directivo) {
    die("Error al preparar la consulta del directivo.");
}

$stmt_directivo->bind_param("i", $id_usuario);
$stmt_directivo->execute();

$resultado_directivo = $stmt_directivo->get_result();

if ($resultado_directivo->num_rows === 0) {
    $stmt_directivo->close();
    die("No se encontró información del directivo.");
}

$directivo = $resultado_directivo->fetch_assoc();

$stmt_directivo->close();


/*=========================================================
    NOMBRE DEL DIRECTIVO
=========================================================*/

$nombre_directivo = trim(
    $directivo['nombre'] . ' ' .
    $directivo['apellido_p'] . ' ' .
    $directivo['apellido_m']
);


/*=========================================================
    CARRERA SELECCIONADA
=========================================================*/

$carrera_seleccionada = isset($_GET['carrera'])
    ? trim($_GET['carrera'])
    : '';


/*=========================================================
    OBTENER CARRERAS
=========================================================*/

$sql_carreras = "
    SELECT DISTINCT carrera
    FROM tutorados
    WHERE activo = 1
      AND carrera IS NOT NULL
      AND TRIM(carrera) <> ''
    ORDER BY carrera ASC
";

$resultado_carreras = $conn->query($sql_carreras);

if (!$resultado_carreras) {
    die("Error al obtener las carreras.");
}

$carreras = [];

while ($fila = $resultado_carreras->fetch_assoc()) {
    $carreras[] = trim($fila['carrera']);
}


/*=========================================================
    VARIABLES
=========================================================*/

$grupos = [];

$total_grupos = 0;
$total_alumnos = 0;
$total_canalizados = 0;

$porcentaje_general = 0;


/*=========================================================
    SI EXISTE CARRERA SELECCIONADA
=========================================================*/

if ($carrera_seleccionada !== '') {

    /*=====================================================
        OBTENER GRUPOS
    =====================================================*/

    $sql_grupos = "
        SELECT
            t.grupo,

            COUNT(DISTINCT t.id_tutorado) AS total_alumnos,

            COUNT(
                DISTINCT CASE
                    WHEN c.estado IN (
                        'PENDIENTE',
                        'EN PROCESO',
                        'ATENDIDO'
                    )
                    THEN t.id_tutorado
                END
            ) AS alumnos_canalizados

        FROM tutorados t

        LEFT JOIN canalizaciones c
            ON c.id_tutorado = t.id_tutorado

        WHERE t.carrera = ?
          AND t.activo = 1

        GROUP BY t.grupo

        ORDER BY t.grupo ASC
    ";

    $stmt_grupos = $conn->prepare($sql_grupos);

    if (!$stmt_grupos) {
        die("Error al preparar la consulta de grupos.");
    }

    $stmt_grupos->bind_param(
        "s",
        $carrera_seleccionada
    );

    $stmt_grupos->execute();

    $resultado_grupos = $stmt_grupos->get_result();


    while ($fila = $resultado_grupos->fetch_assoc()) {

        $fila['grupo'] = trim($fila['grupo']);

        $fila['total_alumnos'] =
            (int) $fila['total_alumnos'];

        $fila['alumnos_canalizados'] =
            (int) $fila['alumnos_canalizados'];

        $grupos[] = $fila;

        $total_grupos++;

        $total_alumnos +=
            $fila['total_alumnos'];

        $total_canalizados +=
            $fila['alumnos_canalizados'];
    }

    $stmt_grupos->close();


    /*=====================================================
        PORCENTAJE
    =====================================================*/

    if ($total_alumnos > 0) {

        $porcentaje_general = round(
            (
                $total_canalizados /
                $total_alumnos
            ) * 100
        );

    }

}


/*=========================================================
    SIDEBAR
=========================================================*/

$base_url = "/SIST V.4.0/PIT_V.4.0";

$pagina_actual = $_SERVER['PHP_SELF'];

$sidebar_path =
    "../../includes_pit/sidebar_directivos.php";

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
        Canalizaciones generales | PIT V.4.0
    </title>


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="canalizacionesgenerales.css"
    >

</head>


<body>


<?php

if (file_exists($sidebar_path)) {
    require_once($sidebar_path);
}

?>


<main class="contenido-directivo">


    <!--==================================================
        ENCABEZADO
    ===================================================-->

    <section class="encabezado-pagina">

        <div class="encabezado-texto">

            <span class="etiqueta-seccion">
                MONITOREO INSTITUCIONAL
            </span>

            <h1>
                Canalizaciones generales
            </h1>

            <p>
                Consulta y monitorea los alumnos que cuentan
                actualmente con una canalización activa,
                seleccionando la carrera que deseas supervisar.
            </p>

        </div>


        <div class="directivo-badge">

            <div class="directivo-icono">

                <i class="fas fa-user-tie"></i>

            </div>

            <div>

                <span>
                    Directivo
                </span>

                <strong>
                    <?= htmlspecialchars(
                        $nombre_directivo,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </strong>

            </div>

        </div>

    </section>



    <!--==================================================
        SELECTOR DE CARRERA
    ===================================================-->

    <section class="selector-carrera">

        <div class="selector-icono">

            <i class="fas fa-building-columns"></i>

        </div>


        <div class="selector-contenido">

            <span class="mini-etiqueta">
                ÁREA DE MONITOREO
            </span>

            <h2>
                Selecciona una carrera
            </h2>

            <p>
                Elige la carrera que deseas consultar.
                La información mostrada corresponderá
                únicamente a la carrera seleccionada.
            </p>


            <form
                method="GET"
                action="index.php"
                class="form-carrera"
            >

                <select
                    name="carrera"
                    id="selectCarrera"
                    required
                >

                    <option value="">
                        Selecciona una carrera...
                    </option>

                    <?php foreach ($carreras as $carrera): ?>

                        <option
                            value="<?= htmlspecialchars(
                                $carrera,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            <?= (
                                $carrera ===
                                $carrera_seleccionada
                            )
                            ? 'selected'
                            : ''; ?>
                        >

                            <?= htmlspecialchars(
                                $carrera,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>

                        </option>

                    <?php endforeach; ?>

                </select>


                <button
                    type="submit"
                    class="btn-consultar"
                >

                    <i class="fas fa-magnifying-glass"></i>

                    Consultar carrera

                </button>

            </form>

        </div>

    </section>



    <?php if ($carrera_seleccionada !== ''): ?>


        <!--==================================================
            CARRERA ACTUAL
        ===================================================-->

        <section class="carrera-seleccionada">

            <div class="carrera-seleccionada-icono">

                <i class="fas fa-graduation-cap"></i>

            </div>


            <div>

                <span>
                    CARRERA SELECCIONADA
                </span>

                <h2>
                    <?= htmlspecialchars(
                        $carrera_seleccionada,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>
                </h2>

            </div>

        </section>



        <!--==================================================
            RESUMEN
        ===================================================-->

        <section class="resumen-grid">


            <article class="resumen-card">

                <div class="resumen-icono">

                    <i class="fas fa-layer-group"></i>

                </div>

                <div>

                    <span>
                        Grupos
                    </span>

                    <strong>
                        <?= $total_grupos; ?>
                    </strong>

                </div>

            </article>



            <article class="resumen-card">

                <div class="resumen-icono">

                    <i class="fas fa-users"></i>

                </div>

                <div>

                    <span>
                        Alumnos
                    </span>

                    <strong>
                        <?= $total_alumnos; ?>
                    </strong>

                </div>

            </article>



            <article class="resumen-card">

                <div class="resumen-icono">

                    <i class="fas fa-file-circle-exclamation"></i>

                </div>

                <div>

                    <span>
                        Canalizaciones activas
                    </span>

                    <strong>
                        <?= $total_canalizados; ?>
                    </strong>

                </div>

            </article>



            <article class="resumen-card">

                <div class="resumen-icono">

                    <i class="fas fa-chart-pie"></i>

                </div>

                <div>

                    <span>
                        Porcentaje
                    </span>

                    <strong>
                        <?= $porcentaje_general; ?>%
                    </strong>

                </div>

            </article>


        </section>



        <!--==================================================
            GRUPOS
        ===================================================-->

        <section class="seccion-grupos">


            <div class="seccion-titulo">

                <div>

                    <span class="mini-etiqueta">
                        DISTRIBUCIÓN
                    </span>

                    <h2>
                        Grupos de la carrera
                    </h2>

                    <p>
                        Selecciona un grupo para consultar
                        sus alumnos con canalizaciones activas.
                    </p>

                </div>


                <div class="buscador-contenedor">

                    <i class="fas fa-search"></i>

                    <input
                        type="text"
                        id="buscarGrupo"
                        placeholder="Buscar grupo..."
                        autocomplete="off"
                    >

                </div>

            </div>



            <div
                class="grupos-grid"
                id="gruposGrid"
            >

                <?php if (count($grupos) > 0): ?>


                    <?php foreach ($grupos as $grupo): ?>

                        <?php

                        $nombre_grupo =
                            $grupo['grupo'];

                        $cantidad =
                            $grupo['alumnos_canalizados'];

                        $total_grupo =
                            $grupo['total_alumnos'];

                        $tiene =
                            $cantidad > 0;

                        $porcentaje =
                            0;

                        if ($total_grupo > 0) {

                            $porcentaje = round(
                                (
                                    $cantidad /
                                    $total_grupo
                                ) * 100
                            );

                        }

                        ?>


                        <a
                            href="alumnos.php?carrera=<?= urlencode(
                                $carrera_seleccionada
                            ); ?>&grupo=<?= urlencode(
                                $nombre_grupo
                            ); ?>"
                            class="grupo-card <?= $tiene
                                ? 'tiene-canalizaciones'
                                : 'sin-canalizaciones'; ?>"
                            data-grupo="<?= htmlspecialchars(
                                mb_strtolower(
                                    $nombre_grupo,
                                    'UTF-8'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                        >


                            <div class="grupo-card-top">

                                <div class="grupo-icono">

                                    <i class="fas fa-users-rectangle"></i>

                                </div>


                                <span
                                    class="estado-grupo <?= $tiene
                                        ? 'estado-activo'
                                        : 'estado-vacio'; ?>"
                                >

                                    <?= $tiene
                                        ? 'CON CANALIZACIONES'
                                        : 'SIN CANALIZACIONES'; ?>

                                </span>

                            </div>



                            <div class="grupo-nombre">

                                <span>
                                    Grupo
                                </span>

                                <h3>
                                    <?= htmlspecialchars(
                                        $nombre_grupo,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>
                                </h3>

                            </div>



                            <div class="grupo-cantidad">

                                <strong>
                                    <?= $cantidad; ?>
                                </strong>

                                <span>

                                    <?= $cantidad === 1
                                        ? 'alumno con canalización activa'
                                        : 'alumnos con canalización activa'; ?>

                                </span>

                            </div>



                            <div class="grupo-barra">

                                <div class="barra-fondo">

                                    <div
                                        class="barra-progreso"
                                        style="width: <?= $porcentaje; ?>%;"
                                    ></div>

                                </div>

                                <span>
                                    <?= $cantidad; ?>
                                    de
                                    <?= $total_grupo; ?>
                                </span>

                            </div>



                            <div class="grupo-footer">

                                <span>
                                    Ver alumnos
                                </span>

                                <i class="fas fa-arrow-right"></i>

                            </div>


                        </a>


                    <?php endforeach; ?>


                <?php else: ?>


                    <div class="estado-vacio-general">

                        <div class="estado-vacio-icono">

                            <i class="fas fa-users-slash"></i>

                        </div>

                        <h3>
                            No hay grupos registrados
                        </h3>

                        <p>
                            No existen alumnos registrados
                            para la carrera seleccionada.
                        </p>

                    </div>


                <?php endif; ?>


            </div>



            <div
                id="sinResultados"
                class="sin-resultados"
            >

                <i class="fas fa-search"></i>

                <h3>
                    No encontramos ese grupo
                </h3>

                <p>
                    Intenta realizar la búsqueda con otro nombre.
                </p>

            </div>


        </section>



        <!--==================================================
            INFORMACIÓN
        ===================================================-->

        <section class="informacion-panel">

            <div class="informacion-icono">

                <i class="fas fa-circle-info"></i>

            </div>


            <div>

                <h3>
                    ¿Qué se considera una canalización activa?
                </h3>

                <p>
                    El sistema considera activas las
                    canalizaciones con estado
                    <strong>PENDIENTE</strong>,
                    <strong>EN PROCESO</strong> o
                    <strong>ATENDIDO</strong>.
                    Las canalizaciones con estado
                    <strong>CERRADO</strong>
                    no se contabilizan.
                </p>

            </div>

        </section>


    <?php else: ?>


        <!--==================================================
            ESTADO INICIAL
        ===================================================-->

        <section class="estado-inicial">

            <div class="estado-inicial-icono">

                <i class="fas fa-building-columns"></i>

            </div>

            <h2>
                Selecciona una carrera para comenzar
            </h2>

            <p>
                Elige una carrera en el selector superior
                para visualizar sus grupos y conocer
                cuántos alumnos tienen canalizaciones activas.
            </p>

        </section>


    <?php endif; ?>


</main>


<script
    src="canalizacionesgenerales.js"
></script>


</body>
</html>