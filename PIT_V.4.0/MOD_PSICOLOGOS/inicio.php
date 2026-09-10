<?php

/*=========================================================
    PANEL DEL DEPARTAMENTO DE PSICOLOGÍA
    TESCHA
=========================================================*/

require_once("../sesion.php");

if (!in_array("PSICOLOGO", $_SESSION["roles"])) {

    header("Location: ../indexloguin.php");
    exit();

}

require_once("../base_pit/conect_pit.php");

$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/SIST V.4.0/PIT_V.4.0/";


/*=========================================================
    OBTENER INFORMACIÓN DEL PSICÓLOGO
=========================================================*/

$id_usuario = $_SESSION["id_usuario"];

$sqlPsicologo = "

SELECT *

FROM administradores_psicologos

WHERE id_usuario = ?

LIMIT 1

";

$stmtPsicologo = $conn->prepare($sqlPsicologo);

$stmtPsicologo->bind_param("i",$id_usuario);

$stmtPsicologo->execute();

$resultadoPsicologo = $stmtPsicologo->get_result();

$psicologo = $resultadoPsicologo->fetch_assoc();

$id_psicologo = $psicologo["id_registro"];

$nombre_psicologo = $psicologo["nombre"];


/*=========================================================
    OBTENER AVISOS DEL PSICÓLOGO
=========================================================*/

$sqlAvisos = "

SELECT *

FROM avisos_psicologia

WHERE id_psicologo = ?

ORDER BY fecha_publicacion DESC

";

$stmtAvisos = $conn->prepare($sqlAvisos);

$stmtAvisos->bind_param("i",$id_psicologo);

$stmtAvisos->execute();

$avisos = $stmtAvisos->get_result();

/*=========================================================
    OBTENER AVISOS INSTITUCIONALES PARA PSICOLOGÍA
=========================================================*/

$sqlAvisosInstitucionales = "

SELECT 
    a.id_aviso,
    a.titulo,
    a.descripcion,
    a.link,
    a.imagen,
    a.pdf,
    a.estado,
    a.fecha_publicacion,
    a.fecha_expiracion,
    a.prioridad

FROM avisos a

INNER JOIN aviso_tipos at
    ON a.id_aviso = at.id_aviso

INNER JOIN tipos t
    ON at.id_tipo = t.id_tipo

WHERE t.id_tipo = 7

AND a.estado = 'ACTIVO'

AND (
    a.fecha_expiracion IS NULL
    OR a.fecha_expiracion >= NOW()
)

ORDER BY
    CASE a.prioridad
        WHEN 'ALTA' THEN 1
        WHEN 'MEDIA' THEN 2
        WHEN 'BAJA' THEN 3
        ELSE 4
    END,

    a.fecha_publicacion DESC

";

$stmtAvisosInstitucionales = $conn->prepare(
    $sqlAvisosInstitucionales
);

$stmtAvisosInstitucionales->execute();

$avisosInstitucionales =
    $stmtAvisosInstitucionales->get_result();

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Departamento de Psicología

</title>

<link
rel="stylesheet"
href="css/estilo.css">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<?php include("../includes_pit/sidebar_psicologos.php"); ?>


<div class="separador"></div>


<div class="contenedor-principal">

<!--==================================================
                        HERO
===================================================-->

<section class="hero">

    <div class="hero-texto">

        <span class="badge">

            Departamento de Psicología

        </span>

        <h1>

            <span id="saludo"></span>

            <?php echo htmlspecialchars($nombre_psicologo); ?> 👋

        </h1>

        <p>

            Bienvenido al panel del Departamento de Psicología.

            Desde este espacio podrás administrar tus publicaciones,
            compartir información con la comunidad estudiantil,
            organizar tus actividades y próximamente administrar
            tus horarios de atención psicológica.

        </p>

        <div class="fecha">

            <span id="fecha"></span>

            <span id="hora"></span>

        </div>

    </div>

    <div class="hero-imagen">

        <img
        src="img/bienvenida.jpg"
        alt="Departamento de Psicología">

    </div>

</section>


<!--==================================================
                GESTIÓN DE AVISOS
===================================================-->

<section class="gestion-avisos">

    <div class="gestion-header">

        <div>

            <span class="gestion-etiqueta">

                📢 Comunicación Institucional

            </span>

            <h2>

                Gestión de Avisos

            </h2>

            <p>

                Los avisos publicados desde este panel aparecerán
                automáticamente en la página institucional
                <strong>SIST Contacta a tu Psicólogo</strong>,
                permitiendo mantener informados a los estudiantes
                sobre campañas, talleres, conferencias,
                actividades y comunicados oficiales.

            </p>

        </div>

        <button
        class="btn-nuevo-aviso"
        id="abrirModal">

            <i class="fa-solid fa-plus"></i>

            Nuevo Aviso

        </button>

    </div>


    <!--==================================================
                GRID DE AVISOS
    ===================================================-->

    <div
    id="contenedorAvisos"
    class="contenedor-avisos">
    <?php

if($avisos->num_rows > 0){

    while($aviso = $avisos->fetch_assoc()){

?>

<div class="aviso-card">

    <?php if(!empty($aviso["imagen"])){ ?>

        <div class="aviso-imagen">

            <img
            src="uploads/imagenes/<?php echo htmlspecialchars($aviso["imagen"]); ?>"
            alt="Imagen del aviso">

        </div>

    <?php } ?>

    <div class="aviso-cuerpo">

        <!--=====================================
                    CABECERA
        ======================================-->

        <div class="aviso-top">

            <span class="badge-categoria">

                <?php echo htmlspecialchars($aviso["categoria"]); ?>

            </span>

            <span class="badge-estado">

                <?php echo htmlspecialchars($aviso["estado"]); ?>

            </span>

        </div>

        <!--=====================================
                    TÍTULO
        ======================================-->

        <h3>

            <?php echo htmlspecialchars($aviso["titulo"]); ?>

        </h3>

        <!--=====================================
                DESCRIPCIÓN
        ======================================-->

        <p>

            <?php echo nl2br(htmlspecialchars($aviso["descripcion"])); ?>

        </p>

        <!--=====================================
                    ARCHIVOS
        ======================================-->

        <?php

        if(
            !empty($aviso["pdf"]) ||
            !empty($aviso["enlace"])
        ){

        ?>

        <div class="aviso-recursos">

            <?php

            if(!empty($aviso["pdf"])){

            ?>

            <a
            href="uploads/imagenes/pdf/<?php echo urlencode($aviso["pdf"]); ?>"
            target="_blank"
            class="btn-pdf">

                <i class="fa-solid fa-file-pdf"></i>

                PDF

            </a>

            <?php

            }

            ?>


            <?php

            if(!empty($aviso["enlace"])){

            ?>

            <a
            href="<?php echo htmlspecialchars($aviso["enlace"]); ?>"
            target="_blank"
            class="btn-link">

                <i class="fa-solid fa-link"></i>

                Enlace

            </a>

            <?php

            }

            ?>

        </div>

        <?php } ?>

        <!--=====================================
                    PIE
        ======================================-->

        <div class="aviso-footer">

            <div class="fecha-aviso">

                <i class="fa-solid fa-calendar-days"></i>

                <?php

                echo date(
                    "d/m/Y H:i",
                    strtotime($aviso["fecha_publicacion"])
                );

                ?>

            </div>

            <div class="acciones-aviso">

                <a
                href="editar_aviso.php?id=<?php echo $aviso["id_aviso"]; ?>"
                class="btn-icon editar"
                title="Editar">

                    <i class="fa-solid fa-pen"></i>

                </a>

                <a
                href="eliminar_aviso.php?id=<?php echo $aviso["id_aviso"]; ?>"
                class="btn-icon eliminar"
                title="Eliminar"
                onclick="return confirm('¿Deseas eliminar este aviso?');">

                    <i class="fa-solid fa-trash"></i>

                </a>

            </div>

        </div>

    </div>

</div>

<?php

    }

}else{

?>

<div class="sin-avisos">

    <i class="fa-regular fa-folder-open"></i>

    <h3>

        No existen avisos publicados

    </h3>

    <p>

        Cuando publiques un aviso aparecerá aquí.
        Puedes comenzar haciendo clic en
        <strong>Nuevo Aviso</strong>.

    </p>

</div>

<?php

}

?>

</div>

</section>
<!--==================================================
                PANEL INFORMATIVO
===================================================-->

<section class="informacion-panel">

    <div class="info-card">

        <div class="info-icono">

            <i class="fa-solid fa-bullhorn"></i>

        </div>

        <div>

            <h3>

                Comunicación con estudiantes

            </h3>

            <p>

                Publica avisos, campañas, talleres y comunicados para
                que aparezcan automáticamente en la vista
                <strong>SIST Contacta a tu Psicólogo</strong>.

            </p>

        </div>

    </div>

    <div class="info-card">

        <div class="info-icono">

            <i class="fa-solid fa-calendar-check"></i>

        </div>

        <div>

            <h3>

                Próximamente

            </h3>

            <p>

                Muy pronto podrás administrar tus horarios disponibles
                para que los estudiantes puedan agendar citas desde
                el sistema.

            </p>

        </div>

    </div>

    <div class="info-card">

        <div class="info-icono">

            <i class="fa-solid fa-heart"></i>

        </div>

        <div>

            <h3>

                Bienestar emocional

            </h3>

            <p>

                El Departamento de Psicología busca brindar atención,
                orientación y acompañamiento a toda la comunidad
                estudiantil.

            </p>

        </div>

    </div>

</section>

<!--==================================================
        AVISOS DE DESARROLLO ACADÉMICO
===================================================-->

<section class="avisos-institucionales">

    <div class="institucional-header">

        <div class="institucional-titulo">

            <span class="institucional-etiqueta">

                <i class="fa-solid fa-building-columns"></i>

                Comunicación Institucional

            </span>

            <h2>

                Avisos de Desarrollo Académico

            </h2>

            <p>

                Comunicados enviados por el Departamento de
                Desarrollo Académico dirigidos al
                Departamento de Psicología.

            </p>

        </div>

        <div class="institucional-icono">

            <i class="fa-solid fa-bullhorn"></i>

        </div>

    </div>


    <div class="institucional-grid">

        <?php if($avisosInstitucionales->num_rows > 0): ?>

            <?php while($avisoInst = $avisosInstitucionales->fetch_assoc()): ?>

                <article class="institucional-card">

                    <!-- PRIORIDAD -->

                    <div class="institucional-card-top">

                        <span
                        class="prioridad prioridad-<?php echo strtolower($avisoInst["prioridad"]); ?>">

                            <?php if($avisoInst["prioridad"] === "ALTA"): ?>

                                <i class="fa-solid fa-circle-exclamation"></i>

                            <?php elseif($avisoInst["prioridad"] === "MEDIA"): ?>

                                <i class="fa-solid fa-circle-info"></i>

                            <?php else: ?>

                                <i class="fa-solid fa-circle"></i>

                            <?php endif; ?>

                            Prioridad
                            <?php echo htmlspecialchars($avisoInst["prioridad"]); ?>

                        </span>

                        <span class="institucional-fecha">

                            <i class="fa-regular fa-calendar"></i>

                            <?php

                            echo date(
                                "d/m/Y",
                                strtotime($avisoInst["fecha_publicacion"])
                            );

                            ?>

                        </span>

                    </div>


                    <!-- IMAGEN -->

                    <?php if(!empty($avisoInst["imagen"])): ?>

                        <div class="institucional-imagen">

                            <img
                            src="<?php echo htmlspecialchars($base_url); ?>uploads/imagenes/<?php echo htmlspecialchars($avisoInst["imagen"]); ?>"
                            alt="Imagen del aviso">

                        </div>

                    <?php endif; ?>


                    <!-- CONTENIDO -->

                    <div class="institucional-contenido">

                        <div class="institucional-badge">

                            <i class="fa-solid fa-building"></i>

                            Desarrollo Académico

                        </div>


                        <h3>

                            <?php echo htmlspecialchars($avisoInst["titulo"]); ?>

                        </h3>


                        <?php if(!empty($avisoInst["descripcion"])): ?>

                            <p>

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $avisoInst["descripcion"]
                                    )
                                );

                                ?>

                            </p>

                        <?php endif; ?>


                        <!-- RECURSOS -->

                        <?php

                        if(
                            !empty($avisoInst["pdf"]) ||
                            !empty($avisoInst["link"])
                        ):

                        ?>

                            <div class="institucional-recursos">


                                <?php if(!empty($avisoInst["pdf"])): ?>

                                    <a
                                    href="<?php echo htmlspecialchars($base_url); ?>uploads/avisos/<?php echo urlencode($avisoInst["pdf"]); ?>"
                                    target="_blank"
                                    class="recurso-pdf">

                                        <i class="fa-solid fa-file-pdf"></i>

                                        Ver PDF

                                    </a>

                                <?php endif; ?>


                                <?php if(!empty($avisoInst["link"])): ?>

                                    <a
                                    href="<?php echo htmlspecialchars($avisoInst["link"]); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="recurso-link">

                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>

                                        Ver enlace

                                    </a>

                                <?php endif; ?>


                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- PIE -->

                    <div class="institucional-footer">

                        <span>

                            <i class="fa-solid fa-clock"></i>

                            Publicado el

                            <?php

                            echo date(
                                "d/m/Y H:i",
                                strtotime($avisoInst["fecha_publicacion"])
                            );

                            ?>

                        </span>

                    </div>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="sin-avisos-institucionales">

                <div class="sin-avisos-icono">

                    <i class="fa-regular fa-bell-slash"></i>

                </div>

                <h3>

                    No hay avisos institucionales

                </h3>

                <p>

                    Actualmente no existen comunicados de
                    Desarrollo Académico dirigidos al
                    Departamento de Psicología.

                </p>

            </div>

        <?php endif; ?>

    </div>

</section>
<!--==================================================
                    FRASE
===================================================-->

<section class="frase">

    "La orientación oportuna puede cambiar el rumbo de una vida."

</section>

</div>

<!--==================================================
                MODAL NUEVO AVISO
===================================================-->

<div
class="modal-aviso"
id="modalAviso">

<div class="modal-contenido">

<div class="modal-header">

<div>

<h2>

📢 Crear Nuevo Aviso

</h2>

<p>

Comparte información importante con la comunidad estudiantil.

</p>

</div>

<button
type="button"
class="cerrar-modal"
id="cerrarModal">

<i class="fa-solid fa-xmark"></i>

</button>

</div>

<form

id="formAviso"

action="guardar_aviso.php"

method="POST"

enctype="multipart/form-data"

autocomplete="off">

<div class="grupo">

<label>

Título <span>*</span>

</label>

<input

type="text"

name="titulo"

maxlength="200"

placeholder="Ej. Taller de manejo del estrés"

required>

</div>

<div class="grupo">

<label>

Descripción <span>*</span>

</label>

<textarea

name="descripcion"

rows="6"

placeholder="Escribe aquí el contenido del aviso..."

required></textarea>

</div>

<div class="fila">

<div class="grupo">

<label>

Categoría

</label>

<select name="categoria">

<option value="AVISO">

Aviso

</option>

<option value="TALLER">

Taller

</option>

<option value="CURSO">

Curso

</option>

<option value="CONFERENCIA">

Conferencia

</option>

<option value="CAMPAÑA">

Campaña

</option>

<option value="ACTIVIDAD">

Actividad

</option>

</select>

</div>

<div class="grupo">

<label>

Estado

</label>

<select name="estado">

<option value="PUBLICADO">

Publicado

</option>

<option value="BORRADOR">

Borrador

</option>

</select>

</div>

</div>

<div class="fila">

<div class="grupo">

<label>

Imagen (Opcional)

</label>

<input

type="file"

id="imagen"

name="imagen"

accept="image/png,image/jpeg,image/jpg,image/webp">

</div>

<div class="grupo">

<label>

PDF (Opcional)

</label>

<input

type="file"

id="pdf"

name="pdf"

accept=".pdf">

</div>

</div>

<div class="grupo">

<label>

Enlace (Opcional)

</label>

<input

type="url"

name="enlace"

placeholder="https://">

</div>

<div class="preview-imagen">

<img

id="preview"

src=""

alt="Vista previa"

style="display:none;">

<div id="textoPreview">

<i class="fa-regular fa-image"></i>

<p>

La vista previa de la imagen aparecerá aquí.

</p>

</div>

</div>

<div id="mensajeAviso"></div>

<div class="botones-modal">
                    <button
                type="button"
                class="btn-cancelar"
                id="cancelarModal">

                    Cancelar

                </button>

                <button
                type="submit"
                class="btn-publicar"
                id="btnGuardarAviso">

                    <i class="fa-solid fa-paper-plane"></i>

                    Publicar Aviso

                </button>

            </div>

        </form>

    </div>

</div>

<!--==================================================
                FOOTER
===================================================-->

<?php include("../../includes/footer.php"); ?>

<!--==================================================
                JAVASCRIPT
===================================================-->

<script src="js/animaciones.js"></script>

</body>

</html>