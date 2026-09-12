
<?php
/*=========================================================
    CANALIZACIONES POR CARRERA
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
    CONEXIÓN A LA BASE DE DATOS
=========================================================*/

require_once("../../base_pit/conect_pit.php");


/*
    IMPORTANTE:
    Tu conexión utiliza la variable $conn.
*/


if (!isset($conn)) {

    die("Error: no se encontró la conexión a la base de datos.");

}


/*=========================================================
    VALIDAR ROL JEFE DE CARRERA
=========================================================*/

$es_jefe_carrera = false;


/*
    Primero revisamos si el rol ya viene guardado
    dentro de la sesión.
*/

if (isset($_SESSION['rol'])) {

    if (
        $_SESSION['rol'] === 'JEFE_CARRERA' ||
        $_SESSION['rol'] === 'JEFE DE CARRERA'
    ) {

        $es_jefe_carrera = true;

    }

}


/*=========================================================
    REVISAR ROLES EN SESIÓN
=========================================================*/

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


/*=========================================================
    VALIDAR ROL DIRECTAMENTE EN BASE DE DATOS
=========================================================*/

/*
    Si la sesión no contiene el rol de manera reconocible,
    hacemos la comprobación directamente en la BD.
*/

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

    $stmt_rol = $conn->prepare($sql_rol);


    if ($stmt_rol) {

        $stmt_rol->bind_param(
            "i",
            $id_usuario
        );

        $stmt_rol->execute();

        $resultado_rol = $stmt_rol->get_result();


        if ($resultado_rol->num_rows > 0) {

            $es_jefe_carrera = true;

        }


        $stmt_rol->close();

    }

}


/*=========================================================
    DENEGAR ACCESO
=========================================================*/

if (!$es_jefe_carrera) {

    http_response_code(403);

    die("Acceso no autorizado.");

}


/*=========================================================
    OBTENER INFORMACIÓN DEL JEFE DE CARRERA
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

    die(
        "Error al preparar la consulta del jefe de carrera: "
        . $conn->error
    );

}


$stmt_jefe->bind_param(
    "i",
    $id_usuario
);


$stmt_jefe->execute();


$resultado_jefe = $stmt_jefe->get_result();


/*=========================================================
    VALIDAR INFORMACIÓN DEL JEFE
=========================================================*/

if ($resultado_jefe->num_rows === 0) {

    $stmt_jefe->close();

    die(
        "No se encontró información académica asociada "
        . "al usuario."
    );

}


$jefe = $resultado_jefe->fetch_assoc();


$stmt_jefe->close();


/*=========================================================
    DATOS DEL JEFE
=========================================================*/

$nombre_jefe = trim(
    $jefe['nombre'] . ' ' .
    $jefe['apellido_p'] . ' ' .
    $jefe['apellido_m']
);


$carrera_jefe = trim(
    $jefe['carrera']
);


/*=========================================================
    OBTENER GRUPOS DE LA CARRERA
=========================================================*/

/*
    IMPORTANTE:

    Se muestran TODOS los grupos pertenecientes a la carrera.

    El número de alumnos canalizados cuenta alumnos DISTINTOS.

    Estados considerados activos:

        PENDIENTE
        EN PROCESO
        ATENDIDO

    CERRADO NO se considera activo.
*/


$sql_grupos = "

    SELECT

        t.grupo,

        COUNT(
            DISTINCT CASE

                WHEN c.estado IN (
                    'PENDIENTE',
                    'EN PROCESO',
                    'ATENDIDO'
                )

                THEN t.id_tutorado

            END
        ) AS alumnos_canalizados,


        COUNT(
            DISTINCT t.id_tutorado
        ) AS total_alumnos


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

    die(
        "Error al preparar la consulta de grupos: "
        . $conn->error
    );

}


$stmt_grupos->bind_param(
    "s",
    $carrera_jefe
);


$stmt_grupos->execute();


$resultado_grupos = $stmt_grupos->get_result();


/*=========================================================
    ARREGLO DE GRUPOS
=========================================================*/

$grupos = [];


$total_grupos = 0;

$total_alumnos = 0;

$total_canalizados = 0;


/*=========================================================
    RECORRER GRUPOS
=========================================================*/

while ($fila = $resultado_grupos->fetch_assoc()) {


    $fila['grupo'] = trim(
        $fila['grupo']
    );


    $fila['alumnos_canalizados'] = (int)
        $fila['alumnos_canalizados'];


    $fila['total_alumnos'] = (int)
        $fila['total_alumnos'];


    $grupos[] = $fila;


    $total_grupos++;


    $total_alumnos +=
        $fila['total_alumnos'];


    $total_canalizados +=
        $fila['alumnos_canalizados'];

}


$stmt_grupos->close();


/*=========================================================
    PORCENTAJE GENERAL
=========================================================*/

$porcentaje_general = 0;


if ($total_alumnos > 0) {

    $porcentaje_general = round(

        (
            $total_canalizados
            /
            $total_alumnos
        )
        *
        100

    );

}


/*=========================================================
    SIDEBAR
=========================================================*/

$pagina_actual = $_SERVER['PHP_SELF'];


$base_url = "/SIST V.4.0/PIT_V.4.0";


$sidebar_path =
    "../../includes_pit/sidebar_jefescarrera.php";

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
        Canalizaciones por carrera | PIT V.4.0
    </title>

     <link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        CONTENIDO PRINCIPAL
    ===================================================-->

    <main class="contenido-principal">


        <!--================================================
            ENCABEZADO
        =================================================-->

        <section class="encabezado-pagina">


            <div class="encabezado-texto">


                <span class="etiqueta-seccion">

                    SEGUIMIENTO ACADÉMICO

                </span>


                <h1>

                    Canalizaciones por carrera

                </h1>


                <p>

                    Consulta los alumnos que actualmente cuentan
                    con una canalización activa dentro de los grupos
                    de tu carrera.

                </p>


            </div>



            <!--============================================
                CARRERA
            =============================================-->

            <div class="carrera-badge">


                <div class="carrera-icono">

                    <span>
                        🎓
                    </span>

                </div>


                <div>


                    <span class="badge-label">

                        Carrera asignada

                    </span>


                    <strong>

                        <?= htmlspecialchars(
                            $carrera_jefe,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>

                    </strong>


                </div>


            </div>


        </section>



        <!--================================================
            TARJETAS RESUMEN
        =================================================-->

        <section class="resumen-grid">


            <!--============================================
                GRUPOS
            =============================================-->

            <article class="resumen-card">


                <div class="resumen-icono icono-grupos">

                    📚

                </div>


                <div class="resumen-info">


                    <span>

                        Grupos

                    </span>


                    <strong>

                        <?= $total_grupos; ?>

                    </strong>


                </div>


            </article>



            <!--============================================
                ALUMNOS
            =============================================-->

            <article class="resumen-card">


                <div class="resumen-icono icono-alumnos">

                    👥

                </div>


                <div class="resumen-info">


                    <span>

                        Alumnos

                    </span>


                    <strong>

                        <?= $total_alumnos; ?>

                    </strong>


                </div>


            </article>



            <!--============================================
                CANALIZADOS
            =============================================-->

            <article class="resumen-card">


                <div
                    class="resumen-icono icono-canalizaciones"
                >

                    📋

                </div>


                <div class="resumen-info">


                    <span>

                        Alumnos canalizados

                    </span>


                    <strong>

                        <?= $total_canalizados; ?>

                    </strong>


                </div>


            </article>



            <!--============================================
                PORCENTAJE
            =============================================-->

            <article class="resumen-card">


                <div
                    class="resumen-icono icono-porcentaje"
                >

                    📊

                </div>


                <div class="resumen-info">


                    <span>

                        Porcentaje

                    </span>


                    <strong>

                        <?= $porcentaje_general; ?>%

                    </strong>


                </div>


            </article>


        </section>



        <!--================================================
            SECCIÓN GRUPOS
        =================================================-->

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



                <!--========================================
                    BUSCADOR
                =========================================-->

                <div class="buscador-contenedor">


                    <span class="buscador-icono">

                        🔎

                    </span>


                    <input
                        type="text"
                        id="buscarGrupo"
                        placeholder="Buscar grupo..."
                        autocomplete="off"
                    >


                </div>


            </div>



            <!--================================================
                GRUPOS
            =================================================-->

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


                        $tiene_canalizaciones =
                            $cantidad > 0;


                        $porcentaje_grupo = 0;


                        if ($total_grupo > 0) {

                            $porcentaje_grupo = round(

                                (
                                    $cantidad
                                    /
                                    $total_grupo
                                )
                                *
                                100

                            );

                        }


                        ?>


                        <a

                            href="alumnos.php?grupo=<?= urlencode(
                                $nombre_grupo
                            ); ?>"

                            class="grupo-card <?=

                                $tiene_canalizaciones

                                ? 'tiene-canalizaciones'

                                : 'sin-canalizaciones';

                            ?>"

                            data-grupo="<?= htmlspecialchars(

                                mb_strtolower(
                                    $nombre_grupo,
                                    'UTF-8'
                                ),

                                ENT_QUOTES,
                                'UTF-8'

                            ); ?>"

                        >


                            <!--================================
                                PARTE SUPERIOR
                            =================================-->

                            <div class="grupo-card-top">


                                <div class="grupo-icono">

                                    📚

                                </div>


                                <span

                                    class="estado-grupo <?=

                                        $tiene_canalizaciones

                                        ? 'estado-activo'

                                        : 'estado-vacio';

                                    ?>"

                                >

                                    <?=

                                    $tiene_canalizaciones

                                    ? 'CON CANALIZACIONES'

                                    : 'SIN CANALIZACIONES';

                                    ?>

                                </span>


                            </div>



                            <!--================================
                                NOMBRE
                            =================================-->

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



                            <!--================================
                                CANTIDAD
                            =================================-->

                            <div class="grupo-cantidad">


                                <strong>

                                    <?= $cantidad; ?>

                                </strong>


                                <span>

                                    <?=

                                    $cantidad === 1

                                    ? 'alumno con canalización activa'

                                    : 'alumnos con canalización activa';

                                    ?>

                                </span>


                            </div>



                            <!--================================
                                BARRA
                            =================================-->

                            <div class="grupo-barra">


                                <div class="barra-fondo">


                                    <div

                                        class="barra-progreso"

                                        style="width: <?=

                                            $porcentaje_grupo;

                                        ?>%;"

                                    ></div>


                                </div>


                                <span>

                                    <?= $cantidad; ?>

                                    de

                                    <?= $total_grupo; ?>

                                </span>


                            </div>



                            <!--================================
                                FOOTER TARJETA
                            =================================-->

                            <div class="grupo-footer">


                                <span>

                                    Ver alumnos

                                </span>


                                <span class="flecha">

                                    →

                                </span>


                            </div>


                        </a>


                    <?php endforeach; ?>


                <?php else: ?>


                    <!--====================================
                        SIN GRUPOS
                    =====================================-->

                    <div class="estado-vacio-general">


                        <div class="estado-vacio-icono">

                            📚

                        </div>


                        <h3>

                            No hay grupos registrados

                        </h3>


                        <p>

                            Actualmente no existen alumnos
                            registrados para la carrera
                            asignada al jefe de carrera.

                        </p>


                    </div>


                <?php endif; ?>


            </div>



            <!--================================================
                SIN RESULTADOS DE BÚSQUEDA
            =================================================-->

            <div
                id="sinResultados"
                class="sin-resultados"
            >


                <div>

                    🔎

                </div>


                <h3>

                    No encontramos ese grupo

                </h3>


                <p>

                    Intenta realizar la búsqueda con otro nombre.

                </p>


            </div>


        </section>



        <!--================================================
            INFORMACIÓN
        =================================================-->

        <section class="informacion-panel">


            <div class="informacion-icono">

                ℹ️

            </div>


            <div>


                <h3>

                    ¿Qué se considera una canalización activa?

                </h3>


                <p>

                    El sistema considera activas las canalizaciones
                    con estado

                    <strong>
                        PENDIENTE
                    </strong>,

                    <strong>
                        EN PROCESO
                    </strong>

                    o

                    <strong>
                        ATENDIDO
                    </strong>.

                    Las canalizaciones con estado

                    <strong>
                        CERRADO
                    </strong>

                    no se contabilizan.

                </p>


            </div>


        </section>


    </main>


</div>



<!--=========================================================
    JAVASCRIPT
=========================================================-->

<script
    src="canalizacionesporcarrera.js"
></script>


</body>

</html>
<?php include '../../../includes/footer.php'; ?>
