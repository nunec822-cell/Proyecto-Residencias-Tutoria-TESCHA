<?php
require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}

include("../../base_pit/conect_pit.php");
?>
<?php

/*=========================================
    ESTADÍSTICAS GENERALES
=========================================*/

function contar($conn,$sql){

    $r=$conn->query($sql);

    if($r){

        $f=$r->fetch_row();

        return $f[0];

    }

    return 0;

}

$estadisticas=[];

/*=====================================
DOCENTES
=====================================*/

$estadisticas["DOCENTE"]=contar($conn,"
SELECT COUNT(*)
FROM personal_academico pa
INNER JOIN usuario_tipo ut ON ut.id_usuario=pa.id_usuario
INNER JOIN tipos tp ON tp.id_tipo=ut.id_tipo
WHERE
tp.nombre='DOCENTE'
AND pa.activo=1
AND ut.activo=1");

/*=====================================
TUTORES
=====================================*/

$estadisticas["TUTOR"]=contar($conn,"
SELECT COUNT(*)
FROM personal_academico pa
INNER JOIN usuario_tipo ut ON ut.id_usuario=pa.id_usuario
INNER JOIN tipos tp ON tp.id_tipo=ut.id_tipo
WHERE
tp.nombre='TUTOR'
AND pa.activo=1
AND ut.activo=1");

/*=====================================
COORDINADORES
=====================================*/

$estadisticas["COORDINADOR"]=contar($conn,"
SELECT COUNT(*)
FROM personal_academico pa
INNER JOIN usuario_tipo ut ON ut.id_usuario=pa.id_usuario
INNER JOIN tipos tp ON tp.id_tipo=ut.id_tipo
WHERE
tp.nombre='COORDINADOR'
AND pa.activo=1
AND ut.activo=1");

/*=====================================
JEFES
=====================================*/

$estadisticas["JEFE_CARRERA"]=contar($conn,"
SELECT COUNT(*)
FROM personal_academico pa
INNER JOIN usuario_tipo ut ON ut.id_usuario=pa.id_usuario
INNER JOIN tipos tp ON tp.id_tipo=ut.id_tipo
WHERE
tp.nombre='JEFE_CARRERA'
AND pa.activo=1
AND ut.activo=1");

/*=====================================
DIRECTIVOS
=====================================*/

$estadisticas["DIRECTIVO"]=contar($conn,"
SELECT COUNT(*)
FROM directivos
WHERE activo=1");

/*=====================================
PSICÓLOGOS
=====================================*/

$estadisticas["PSICOLOGO"]=contar($conn,"
SELECT COUNT(*)
FROM administradores_psicologos ap
INNER JOIN usuario_tipo ut ON ut.id_usuario=ap.id_usuario
INNER JOIN tipos tp ON tp.id_tipo=ut.id_tipo
WHERE
tp.nombre='PSICOLOGO'
AND ap.activo=1
AND ut.activo=1");

/*=====================================
TUTORADOS
=====================================*/

$estadisticas["TUTORADO"]=contar($conn,"
SELECT COUNT(*)
FROM tutorados
WHERE activo=1");

function total($tipo,$estadisticas){

    return $estadisticas[$tipo] ?? 0;

}
include("../../includes_pit/sidebar_admin.php"); 

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Centro de Búsqueda</title>

<link rel="stylesheet" href="estilos.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<body>
<div class="separador"></div>
<div class="separador"></div>
<div class="contenedor-buscar">

    <!--=========================
            TITULO
    ==========================-->

    <div class="titulo">

        <h1>
            <i class="fa-solid fa-magnifying-glass"></i>
            Centro de Búsqueda PIT
        </h1>

        <p>
            Consulte rápidamente cualquier usuario registrado dentro del sistema.
        </p>

    </div>


    <!--=========================
            BUSCADOR
    ==========================-->

    <div class="busqueda">

        <input
            type="text"
            id="txtBuscar"
            placeholder="Buscar por matrícula, nombre, apellido, número de empleado, carrera o grupo...">

        <button id="btnBuscar">

            <i class="fa-solid fa-search"></i>

            Buscar

        </button>
        
    </div>

    <div id="resultadoBusqueda"></div>


    <!--=========================
        ESTADISTICAS
    ==========================-->

    <h2 class="subtitulo">

    Resumen General del Sistema

    </h2>

    <p class="descripcion">

    Cantidad de usuarios registrados actualmente por perfil.

    </p>

    <div class="tarjetas">

        <div class="tarjeta">
            <i class="fa-solid fa-user-graduate"></i>
            <h3>Tutorados</h3>
            <span><?= total('TUTORADO',$estadisticas); ?></span>
        </div>

        <div class="tarjeta">
            <i class="fa-solid fa-chalkboard-user"></i>
            <h3>Docentes</h3>
            <span><?= total('DOCENTE',$estadisticas); ?></span>
        </div>

        <div class="tarjeta">
            <i class="fa-solid fa-users"></i>
            <h3>Tutores</h3>
            <span><?= total('TUTOR',$estadisticas); ?></span>
        </div>

        <div class="tarjeta">
            <i class="fa-solid fa-user-tie"></i>
            <h3>Directivos</h3>
            <span><?= total('DIRECTIVO',$estadisticas); ?></span>
        </div>

        <div class="tarjeta">
            <i class="fa-solid fa-building-columns"></i>
            <h3>Jefes Carrera</h3>
            <span><?= total('JEFE_CARRERA',$estadisticas); ?></span>
        </div>

        <div class="tarjeta">
            <i class="fa-solid fa-sitemap"></i>
            <h3>Coordinadores</h3>
            <span><?= total('COORDINADOR',$estadisticas); ?></span>
        </div>

        <div class="tarjeta">
            <i class="fa-solid fa-brain"></i>
            <h3>Psicólogos</h3>
            <span><?= total('PSICOLOGO',$estadisticas); ?></span>
        </div>

    </div>



    <!--=========================
        BOTONES
    ==========================-->

    <div class="leyenda">

        Seleccione una categoría para realizar la búsqueda

    </div>


    <div class="menu">

        <button class="opcion">Docentes</button>

        <button class="opcion">Tutorados</button>

        <button class="opcion">Directivos</button>

        <button class="opcion">Jefes de Carrera</button>

        <button class="opcion">Tutores</button>

        <button class="opcion">Coordinadores</button>

        <button class="opcion">Psicólogos</button>

    </div>


    <!--=========================
        CONTENIDO DINAMICO
    ==========================-->

    <div id="contenido">

        <div class="mensaje">

            <i class="fa-solid fa-arrow-up"></i>

            <h2>

                Seleccione una categoría

            </h2>

            <p>

                Al seleccionar una opción aparecerán aquí las consultas correspondientes.

            </p>

        </div>

    </div>
    
</div>


<script src="script.js"></script>

</body>

</html>

<?php include("../../../includes/footer.php"); ?>