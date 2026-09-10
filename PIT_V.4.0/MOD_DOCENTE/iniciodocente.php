<?php
require_once("../sesion.php");

if (!in_array("DOCENTE", $_SESSION["roles"])) {
    header("Location: ../indexloguin.php");
    exit();
}

require_once '../base_pit/conect_pit.php';
$roles_adicionales = [];
/*=========================================================
    FOTOGRAFÍA DEL TUTOR
=========================================================*/

$es_tutor = in_array("TUTOR", $_SESSION["roles"]);

$fotografia_tutor = null;
$nombre_personal = "";

if ($es_tutor) {

    $id_usuario = $_SESSION["id_usuario"];

    $sql_foto = "
        SELECT
            id_personal,
            nombre,
            apellido_p,
            apellido_m,
            fotografia
        FROM personal_academico
        WHERE id_usuario = ?
        AND activo = 1
        LIMIT 1
    ";

    $stmt_foto = $conn->prepare($sql_foto);
    $stmt_foto->bind_param("i", $id_usuario);
    $stmt_foto->execute();

    $resultado_foto = $stmt_foto->get_result();

    if ($resultado_foto->num_rows > 0) {

        $datos_personal = $resultado_foto->fetch_assoc();

        $fotografia_tutor = $datos_personal["fotografia"];

        $nombre_personal =
            $datos_personal["nombre"] . " " .
            $datos_personal["apellido_p"] . " " .
            $datos_personal["apellido_m"];
    }
}
/*=========================
    ROLES ADICIONALES
=========================*/

if(in_array("TUTOR", $_SESSION["roles"])){

    $roles_adicionales[] = "
    <div class='badge-extra'>
        🎓 Tutor Institucional
    </div>";
}

if(in_array("COORDINADOR", $_SESSION["roles"])){

    $roles_adicionales[] = "
    <div class='badge-extra'>
        ⭐ Coordinador PIT
    </div>";
}
// Ruta base
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/SIST V.4.0/PIT_V.4.0/";
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>Inicio | Docente</title>

    <link rel="stylesheet" href="css/estilo.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<?php include("../includes_pit/sidebar_docente.php"); ?>
<div class="separador"></div>
<div class="separador"></div>

<div class="admin-container">

    <!--==================================
                HERO
    ===================================-->

    <section class="hero">

        <div class="hero-text">

            <div class="hero-top">

                <div class="hero-icon">
                    👨‍🏫
                </div>

                <div>

                    <h2 id="saludo">
                        Cargando saludo...
                    </h2>

                    <div>

    <span class="badge-admin">

        Portal del Docente

    </span>

    <?php if(!empty($roles_adicionales)){ ?>

        <div class="roles-docente">

            <?php

            foreach($roles_adicionales as $rol){

                echo $rol;

            }

            ?>

        </div>

    <?php } ?>

</div>

                </div>

            </div>

            <h1 class="titulo-sistema">

                Programa Institucional de Tutorías

            </h1>

            <p class="subtitulo">

                Bienvenido al Portal del Docente.<br>

                Consulta avisos institucionales y mantente informado
                sobre actividades relacionadas con el Programa
                Institucional de Tutorías.

            </p>

            <div class="fecha-hora">

                <span id="fecha"></span>

                <span id="hora"></span>

            </div>

        </div>

        <div class="hero-image">

            <img src="<?php echo $base_url; ?>../assets_pit/docente.jpg">

        </div>

    </section>

    <!--==================================
            BIENVENIDA
    ===================================-->

    <section class="bienvenida-principal">

        <div class="bienvenida-icono">

            <i class="fa-solid fa-chalkboard-user"></i>

        </div>

        <div class="bienvenida-texto">

            <h2>

                Bienvenido al Portal del Docente

            </h2>

            <p>

                Este espacio ha sido diseñado para mantener una comunicación
                directa entre el Departamento de Desarrollo Académico y el
                personal docente del Tecnológico de Estudios Superiores de Chalco.

            </p>

            <p>

                Aquí podrás consultar avisos institucionales, actividades,
                convocatorias y comunicados relacionados con el Programa
                Institucional de Tutorías.

            </p>

            <div class="mensaje-espera">

                <i class="fa-solid fa-bell"></i>

                <span>

                    Mantente atento a esta sección, ya que aquí aparecerán
                    avisos importantes dirigidos específicamente a los docentes.

                </span>

            </div>

        </div>

    </section>
    <?php if($es_tutor){ ?>

<!--==================================
        FOTOGRAFÍA DEL TUTOR
===================================-->

<section class="fotografia-tutor-section">

    <div class="fotografia-tutor-header">

        <div class="fotografia-tutor-icono">

            <i class="fa-solid fa-camera"></i>

        </div>

        <div>

            <h2>
                Fotografía de perfil del Tutor
            </h2>

            <p>
                Esta fotografía será utilizada para que tus alumnos
                puedan identificarte en la sección
                <strong>“Contacta a tu Tutor”</strong>.
            </p>

        </div>

    </div>


    <div class="fotografia-tutor-contenido">


        <!--==================================
                PREVISUALIZACIÓN
        ===================================-->

        <div class="fotografia-preview">

            <?php if(!empty($fotografia_tutor)){ ?>

                <img
                    src="../uploads/tutores/<?php echo htmlspecialchars($fotografia_tutor); ?>"
                    alt="Fotografía del Tutor"
                >

            <?php }else{ ?>

                <div class="fotografia-sin-foto">

                    <i class="fa-solid fa-user"></i>

                    <span>
                        Sin fotografía
                    </span>

                </div>

            <?php } ?>

        </div>


        <!--==================================
                INFORMACIÓN
        ===================================-->

        <div class="fotografia-info">

            <h3>

                <?php echo htmlspecialchars($nombre_personal); ?>

            </h3>

            <?php if(empty($fotografia_tutor)){ ?>

                <div class="fotografia-alerta">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                    <div>

                        <strong>
                            Fotografía pendiente
                        </strong>

                        <p>
                            Aún no has registrado tu fotografía
                            de perfil como Tutor Institucional.
                        </p>

                    </div>

                </div>

            <?php }else{ ?>

                <div class="fotografia-correcta">

                    <i class="fa-solid fa-circle-check"></i>

                    Fotografía registrada correctamente

                </div>

            <?php } ?>


            <div class="fotografia-recomendaciones">

                <h4>
                    <i class="fa-solid fa-circle-info"></i>
                    Recomendaciones
                </h4>

                <ul>

                    <li>
                        Fotografía reciente.
                    </li>

                    <li>
                        Rostro visible y centrado.
                    </li>

                    <li>
                        Buena iluminación.
                    </li>

                    <li>
                        Fondo limpio y sencillo.
                    </li>

                    <li>
                        Vestimenta formal o apropiada para el entorno institucional.
                    </li>

                    <li>
                        Evitar fotografías grupales, filtros o fotografías informales.
                    </li>

                </ul>

            </div>


            <!--==================================
                    FORMULARIO
            ===================================-->

            <form
                action="subir_fotografia.php"
                method="POST"
                enctype="multipart/form-data"
                class="form-fotografia"
            >

                <label for="fotografia">

                    <i class="fa-solid fa-image"></i>

                    <?php if(empty($fotografia_tutor)){ ?>

                        Seleccionar fotografía

                    <?php }else{ ?>

                        Cambiar fotografía

                    <?php } ?>

                </label>

                <input
                    type="file"
                    id="fotografia"
                    name="fotografia"
                    accept="image/jpeg,image/png,image/webp"
                    required
                >

                <span id="nombre-fotografia">
                    Ningún archivo seleccionado
                </span>

                <button type="submit">

                    <i class="fa-solid fa-cloud-arrow-up"></i>

                    Guardar fotografía

                </button>

            </form>

            <small class="fotografia-formatos">

                JPG, PNG o WEBP · Máximo 5 MB

            </small>

        </div>

    </div>

</section>

<?php } ?>
    <!--==================================
            AVISOS DOCENTE
    ===================================-->

    <section class="avisos-section">

        <div class="avisos-header">

            <div>

                <h2>
                    📢 Avisos para Docentes
                </h2>

                <p>

                    Consulta aquí los comunicados publicados por el
                    Departamento de Desarrollo Académico.

                </p>

            </div>

        </div>

        <div class="grid-avisos">

<?php

$sql = "

SELECT
    a.*,
    GROUP_CONCAT(t.nombre SEPARATOR ', ') AS destinatarios

FROM avisos a

INNER JOIN aviso_tipos at
ON a.id_aviso = at.id_aviso

INNER JOIN tipos t
ON at.id_tipo = t.id_tipo

WHERE
(
    t.nombre='DOCENTE'
    OR (
        t.nombre='TUTOR'
        AND '".(in_array("TUTOR", $_SESSION["roles"]) ? 1 : 0)."'
    )
    OR (
        t.nombre='COORDINADOR'
        AND '".(in_array("COORDINADOR", $_SESSION["roles"]) ? 1 : 0)."'
    )
)

AND a.estado='ACTIVO'

GROUP BY a.id_aviso

ORDER BY a.fecha_publicacion DESC

";

$result = $conn->query($sql);

if($result && $result->num_rows > 0){

while($row = $result->fetch_assoc()){

?>

<div class="aviso-card">

    <div class="aviso-top">

        <div>

            <i class="fa-solid fa-bullhorn"></i>

            Aviso Institucional

        </div>

        <span class="fecha-aviso">

            <i class="fa-solid fa-calendar-days"></i>

            <?php echo date("d/m/Y", strtotime($row['fecha_publicacion'])); ?>

        </span>

    </div>
    <div class="destinatarios">

<?php

$roles = explode(", ", $row['destinatarios']);

foreach($roles as $rol){

    if($rol=="DOCENTE"){
        echo "<span class='rol-aviso docente'>DOCENTE</span>";
    }

    if($rol=="TUTOR"){
        echo "<span class='rol-aviso tutor'>TUTOR</span>";
    }

    if($rol=="COORDINADOR"){
        echo "<span class='rol-aviso coordinador'>COORDINADOR</span>";
    }
}

?>

</div>

    <!-- IMAGEN -->

<?php

if(!empty($row['imagen'])){

?>

<img
class="aviso-imagen"
src="../uploads/avisos/imagenes/<?php echo $row['imagen']; ?>">

<?php

}else{

?>

<img
class="aviso-imagen"
src="<?php echo $base_url; ?>../assets_pit/tescha.png">

<?php

}

?>

<div class="aviso-contenido">

    <h3>

        <?php echo $row['titulo']; ?>

    </h3>

    <p>

        <?php

        if(!empty($row['descripcion'])){

            echo nl2br($row['descripcion']);

        }else{

            echo "Este aviso no contiene descripción.";

        }

        ?>

    </p>

    <div class="aviso-recursos">

        <?php if(!empty($row['pdf'])){ ?>

        <a
        class="btn-pdf"
        href="../uploads/avisos/pdfs/<?php echo $row['pdf']; ?>"
        target="_blank">

            <i class="fa-solid fa-file-pdf"></i>

            Descargar PDF

        </a>

        <?php } ?>

        <?php if(!empty($row['link'])){ ?>

        <a
        class="btn-link"
        href="<?php echo $row['link']; ?>"
        target="_blank">

            <i class="fa-solid fa-link"></i>

            Más información

        </a>

        <?php } ?>

    </div>

</div>

</div>

<?php

}

}else{

?>

<div class="sin-avisos">

    <i class="fa-regular fa-bell"></i>

    <h3>

        No existen avisos.

    </h3>

    <p>

        Cuando el Departamento de Desarrollo Académico
        publique información dirigida a los docentes,
        aparecerá aquí.

    </p>

</div>

<?php

}

?>

        </div>

    </section>

    <!--==================================
                FRASE
    ===================================-->

    <section class="frase">

        <p>

            "La labor docente transforma el conocimiento en oportunidades para el futuro."

        </p>

    </section>

</div>

<?php include '../../includes/footer.php'; ?>

<script src="js/animaciones.js"></script>
<script src="js/fotografia_tutor.js"></script>

<?php if($es_tutor && empty($fotografia_tutor)){ ?>

<!--==================================
        MODAL FOTOGRAFÍA TUTOR
===================================-->

<div
    id="modal-fotografia-tutor"
    class="modal-fotografia-tutor"
>

    <div class="modal-fotografia-contenido">

        <div class="modal-fotografia-icono">

            <i class="fa-solid fa-camera"></i>

        </div>

        <h2>
            Fotografía de Tutor pendiente
        </h2>

        <p>

            Hola, <strong><?php echo htmlspecialchars($nombre_personal); ?></strong>.

        </p>

        <p>

            Como Tutor Institucional, necesitas registrar
            una fotografía de perfil para que tus alumnos
            puedan identificarte en la sección
            <strong>“Contacta a tu Tutor”</strong>.

        </p>

        <div class="modal-fotografia-aviso">

            <i class="fa-solid fa-user-tie"></i>

            <span>

                Procura utilizar una fotografía formal,
                reciente, con buena iluminación y el rostro
                claramente visible.

            </span>

        </div>

        <div class="modal-fotografia-botones">

            <button
                type="button"
                id="btn-ir-fotografia"
                class="btn-modal-fotografia"
            >

                <i class="fa-solid fa-camera"></i>

                Registrar fotografía

            </button>

        </div>

    </div>

</div>

<?php } ?>
</body>
</html>