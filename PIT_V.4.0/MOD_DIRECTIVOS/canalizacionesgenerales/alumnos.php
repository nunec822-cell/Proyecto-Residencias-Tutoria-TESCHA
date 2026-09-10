<?php
/*=========================================================
    ALUMNOS CON CANALIZACIONES ACTIVAS
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
    VALIDAR DIRECTIVO
=========================================================*/

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
    die("Error al validar el rol.");
}

$stmt_rol->bind_param("i", $id_usuario);
$stmt_rol->execute();

$resultado_rol = $stmt_rol->get_result();

$es_directivo =
    $resultado_rol->num_rows > 0;

$stmt_rol->close();


if (!$es_directivo) {
    http_response_code(403);
    die("Acceso no autorizado.");
}


/*=========================================================
    PARÁMETROS
=========================================================*/

$carrera = isset($_GET['carrera'])
    ? trim($_GET['carrera'])
    : '';

$grupo = isset($_GET['grupo'])
    ? trim($_GET['grupo'])
    : '';


if ($carrera === '' || $grupo === '') {
    header(
        "Location: index.php"
    );
    exit;
}


/*=========================================================
    OBTENER ALUMNOS
=========================================================*/

/*
    IMPORTANTE:

    DISTINCT evita duplicar alumnos si un mismo
    alumno tiene más de una canalización activa.
*/

$sql_alumnos = "
    SELECT DISTINCT

        t.id_tutorado,

        t.matricula,

        t.nombre,

        t.apellido_p,

        t.apellido_m,

        t.carrera,

        t.grupo,

        c.id_canalizacion,

        c.estado,

        c.fecha_canalizacion,

        c.observaciones,

        c.acciones,

        c.nuevos_reportes

    FROM tutorados t

    INNER JOIN canalizaciones c
        ON c.id_tutorado = t.id_tutorado

    WHERE t.carrera = ?
      AND t.grupo = ?
      AND t.activo = 1

      AND c.estado IN (
          'PENDIENTE',
          'EN PROCESO',
          'ATENDIDO'
      )

    ORDER BY
        t.apellido_p ASC,
        t.apellido_m ASC,
        t.nombre ASC,
        c.fecha_canalizacion DESC
";


$stmt_alumnos =
    $conn->prepare($sql_alumnos);

if (!$stmt_alumnos) {
    die("Error al preparar la consulta de alumnos.");
}

$stmt_alumnos->bind_param(
    "ss",
    $carrera,
    $grupo
);

$stmt_alumnos->execute();

$resultado_alumnos =
    $stmt_alumnos->get_result();


/*=========================================================
    EVITAR DUPLICADOS POR ALUMNO
=========================================================*/

$alumnos = [];

while ($fila = $resultado_alumnos->fetch_assoc()) {

    $id = (int) $fila['id_tutorado'];

    if (!isset($alumnos[$id])) {

        $alumnos[$id] = $fila;

    }
}

$stmt_alumnos->close();


/*=========================================================
    CONTADORES
=========================================================*/

$total_alumnos = count($alumnos);

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
    SIDEBAR
=========================================================*/

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
        Alumnos canalizados | PIT V.4.0
    </title>


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


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
                SEGUIMIENTO ACADÉMICO
            </span>

            <h1>
                Alumnos con canalización activa
            </h1>

            <p>
                Consulta los alumnos del grupo seleccionado
                que actualmente cuentan con una canalización activa.
            </p>

        </div>


        <a
            href="index.php?carrera=<?= urlencode($carrera); ?>"
            class="btn-regresar"
        >

            <i class="fas fa-arrow-left"></i>

            Regresar a grupos

        </a>

    </section>



    <!--==================================================
        CARRERA / GRUPO
    ===================================================-->

    <section class="ruta-seguimiento">

        <div>

            <span>
                CARRERA
            </span>

            <strong>
                <?= htmlspecialchars(
                    $carrera,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>

        </div>


        <i class="fas fa-chevron-right"></i>


        <div>

            <span>
                GRUPO
            </span>

            <strong>
                <?= htmlspecialchars(
                    $grupo,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </strong>

        </div>

    </section>



    <!--==================================================
        RESUMEN
    ===================================================-->

    <section class="resumen-grid">


        <article class="resumen-card">

            <div class="resumen-icono">

                <i class="fas fa-users"></i>

            </div>

            <div>

                <span>
                    Alumnos canalizados
                </span>

                <strong>
                    <?= $total_alumnos; ?>
                </strong>

            </div>

        </article>



        <article class="resumen-card">

            <div class="resumen-icono">

                <i class="fas fa-clock"></i>

            </div>

            <div>

                <span>
                    Pendientes
                </span>

                <strong>
                    <?= $pendientes; ?>
                </strong>

            </div>

        </article>



        <article class="resumen-card">

            <div class="resumen-icono">

                <i class="fas fa-spinner"></i>

            </div>

            <div>

                <span>
                    En proceso
                </span>

                <strong>
                    <?= $en_proceso; ?>
                </strong>

            </div>

        </article>



        <article class="resumen-card">

            <div class="resumen-icono">

                <i class="fas fa-circle-check"></i>

            </div>

            <div>

                <span>
                    Atendidos
                </span>

                <strong>
                    <?= $atendidos; ?>
                </strong>

            </div>

        </article>


    </section>



    <!--==================================================
        LISTA
    ===================================================-->

    <section class="seccion-alumnos">


        <div class="seccion-titulo">

            <div>

                <span class="mini-etiqueta">
                    ALUMNOS
                </span>

                <h2>
                    Canalizaciones activas
                </h2>

                <p>
                    Alumnos que requieren seguimiento.
                </p>

            </div>


            <div class="buscador-contenedor">

                <i class="fas fa-search"></i>

                <input
                    type="text"
                    id="buscarAlumno"
                    placeholder="Buscar alumno o matrícula..."
                    autocomplete="off"
                >

            </div>

        </div>



        <div
            class="alumnos-grid"
            id="alumnosGrid"
        >


            <?php if ($total_alumnos > 0): ?>


                <?php foreach ($alumnos as $alumno): ?>


                    <?php

                    $nombre_completo = trim(
                        $alumno['nombre'] . ' ' .
                        $alumno['apellido_p'] . ' ' .
                        $alumno['apellido_m']
                    );

                    $estado_clase = '';

                    $estado_icono = '';

                    switch ($alumno['estado']) {

                        case 'PENDIENTE':

                            $estado_clase =
                                'estado-pendiente';

                            $estado_icono =
                                'fa-clock';

                            break;


                        case 'EN PROCESO':

                            $estado_clase =
                                'estado-proceso';

                            $estado_icono =
                                'fa-spinner';

                            break;


                        case 'ATENDIDO':

                            $estado_clase =
                                'estado-atendido';

                            $estado_icono =
                                'fa-circle-check';

                            break;

                    }

                    ?>


                    <article
                        class="alumno-card"
                        data-alumno="<?= htmlspecialchars(
                            mb_strtolower(
                                $nombre_completo . ' ' .
                                $alumno['matricula'],
                                'UTF-8'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                    >


                        <div class="alumno-card-header">


                            <div class="alumno-avatar">

                                <i class="fas fa-user-graduate"></i>

                            </div>


                            <span
                                class="estado-alumno <?= $estado_clase; ?>"
                            >

                                <i
                                    class="fas <?= $estado_icono; ?>"
                                ></i>

                                <?= htmlspecialchars(
                                    $alumno['estado'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </span>


                        </div>



                        <div class="alumno-info">


                            <span class="matricula">

                                <i class="fas fa-id-card"></i>

                                <?= htmlspecialchars(
                                    $alumno['matricula'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </span>


                            <h3>

                                <?= htmlspecialchars(
                                    $nombre_completo,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </h3>


                            <span class="grupo-alumno">

                                <i class="fas fa-users"></i>

                                Grupo
                                <?= htmlspecialchars(
                                    $alumno['grupo'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>

                            </span>


                        </div>



                        <div class="canalizacion-info">


                            <div>

                                <span>
                                    Fecha de canalización
                                </span>

                                <strong>

                                    <?= date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $alumno[
                                                'fecha_canalizacion'
                                            ]
                                        )
                                    ); ?>

                                </strong>

                            </div>


                            <?php if (
                                (int) $alumno['nuevos_reportes'] > 0
                            ): ?>

                                <span class="reportes-nuevos">

                                    <i class="fas fa-bell"></i>

                                    <?= (int)
                                        $alumno['nuevos_reportes']; ?>

                                    nuevos reportes

                                </span>

                            <?php endif; ?>


                        </div>



                        <?php if (
                            !empty($alumno['observaciones'])
                        ): ?>

                            <div class="observaciones">

                                <span>
                                    Observaciones
                                </span>

                                <p>

                                    <?= htmlspecialchars(
                                        $alumno['observaciones'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ); ?>

                                </p>

                            </div>

                        <?php endif; ?>


                    </article>


                <?php endforeach; ?>


            <?php else: ?>


                <div class="estado-vacio-general">

                    <div class="estado-vacio-icono">

                        <i class="fas fa-circle-check"></i>

                    </div>

                    <h3>
                        No hay canalizaciones activas
                    </h3>

                    <p>
                        Actualmente no existen alumnos con
                        canalizaciones activas en este grupo.
                    </p>

                </div>


            <?php endif; ?>


        </div>



        <div
            id="sinResultadosAlumnos"
            class="sin-resultados"
        >

            <i class="fas fa-search"></i>

            <h3>
                No encontramos al alumno
            </h3>

            <p>
                Intenta buscar por nombre o matrícula.
            </p>

        </div>


    </section>


</main>


<script
    src="canalizacionesgenerales.js"
></script>


</body>
</html>