<?php

require_once("../PIT_V.4.0/sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../PIT_V.4.0/indexloguin.php");
    exit();
}
include("../base_sist/conect_sist.php");
include("../PIT_V.4.0/includes_pit/sidebar_admin.php");

// OBTENER ID
$id = $_GET['id'];

// CONSULTA
$sql = "SELECT * FROM instituciones_contenidos WHERE id='$id'";
$resultado = mysqli_query($conexion, $sql);

$fila = mysqli_fetch_assoc($resultado);

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="css/estilo.css">
<div class="separador"></div>
<div class="separador"></div>
<div class="contenedor-admin">

    <h1>Editar Instituciones Vinculantes</h1>

    <div class="layout-editor">

        <!-- PANEL IZQUIERDO -->
        <div class="editor-panel">

            <form
                action="guardar_instituciones.php"
                method="POST"
                class="form-admin"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $fila['id']; ?>"
                >

                <div class="grupo-form">

                    <label>Título Principal</label>

                    <input
                        type="text"
                        name="titulo_principal"
                        value="<?php echo $fila['titulo_principal']; ?>"
                    >

                </div>

                <div class="grupo-form">

                    <label>Descripción Principal</label>

                    <textarea
                        name="descripcion_principal"
                        rows="5"
                    ><?php echo $fila['descripcion_principal']; ?></textarea>

                </div>

                <button
                    type="submit"
                    class="btn-guardar"
                >
                    Guardar Cambios
                </button>

            </form>

            <hr>

            <h2 class="titulo-galeria-admin">
                Administrar Instituciones
            </h2>

            <a
                href="instituciones/index.php"
                class="btn-subir"
                style="text-decoration:none;"
            >
                Abrir Administrador de Instituciones
            </a>

        </div>

        <!-- PANEL DERECHO -->
        <div class="preview-panel">

            <div class="preview-header">
                Vista real del sitio
            </div>

            <iframe
                src="../vista_inst_vinculantes/index.php"
                id="previewFrame"
            ></iframe>

        </div>

    </div>

</div>

<script src="js/admin.js"></script>

<?php include("../includes/footer.php"); ?>