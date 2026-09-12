<?php

require_once("../PIT_V.4.0/sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../PIT_V.4.0/indexloguin.php");
    exit();
}
include("../base_sist/conect_sist.php");
include("../PIT_V.4.0/includes_pit/sidebar_admin.php");

$sql = "
SELECT *
FROM modelo_educativo
LIMIT 1
";

$res = mysqli_query($conexion,$sql);

$modelo = mysqli_fetch_assoc($res);

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<div class="separador"></div>
<div class="separador"></div>
<link rel="stylesheet" href="css/estilo.css">

<div class="contenedor-admin">

    <h1>Editar Modelo Educativo</h1>

    <div class="layout-editor">

        <!-- PANEL IZQUIERDO -->
        <div class="editor-panel">

            <form
                action="guardar_modelo.php"
                method="POST"
                enctype="multipart/form-data"
                class="form-admin"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $modelo['id']; ?>"
                >

                <div class="grupo-form">

                    <label>Título</label>

                    <input
                        type="text"
                        name="titulo"
                        value="<?php echo $modelo['titulo']; ?>"
                        required
                    >

                </div>

                <div class="grupo-form">

                    <label>Descripción</label>

                    <textarea
                        name="descripcion"
                        rows="8"
                        required
                    ><?php echo $modelo['descripcion']; ?></textarea>

                </div>

                <div class="grupo-form">

                    <label>Enlace</label>

                    <input
                        type="text"
                        name="enlace"
                        value="<?php echo $modelo['enlace']; ?>"
                    >

                </div>

                <div class="grupo-form">

                    <label>Imagen</label>

                    <input
                        type="file"
                        name="imagen"
                    >

                    <?php if(!empty($modelo['imagen'])){ ?>

                        <p>
                            Actual:
                            <?php echo $modelo['imagen']; ?>
                        </p>

                        <img
                            src="../vista_tutorias/images/<?php echo $modelo['imagen']; ?>"
                            style="width:200px;border-radius:10px;margin-top:10px;"
                        >

                    <?php } ?>

                </div>

                <div class="grupo-form">

                    <label>PDF</label>

                    <input
                        type="file"
                        name="pdf"
                    >

                    <?php if(!empty($modelo['pdf'])){ ?>

                        <p>
                            PDF actual:
                            <?php echo $modelo['pdf']; ?>
                        </p>

                    <?php } ?>

                </div>

                <button
                    type="submit"
                    class="btn-guardar"
                >
                    Guardar Cambios
                </button>

            </form>

        </div>

        <!-- PANEL DERECHO -->
        <div class="preview-panel">

            <div class="preview-header">
                Vista previa Tutorías
            </div>

            <iframe
                src="../vista_tutorias/index.php"
                id="previewFrame"
            ></iframe>

        </div>

    </div>

</div>

<script src="js/admin.js"></script>

<?php include("../includes/footer.php"); ?>