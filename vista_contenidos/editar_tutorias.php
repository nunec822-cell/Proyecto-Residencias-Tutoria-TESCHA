<?php  

require_once("../PIT_V.4.0/sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../PIT_V.4.0/indexloguin.php");
    exit();
}
include("../base_sist/conect_sist.php");
include("../PIT_V.4.0/includes_pit/sidebar_admin.php");

// 🔥 OBTENER ID
$id = $_GET['id'];

// 🔥 CONSULTA
$sql = "SELECT * FROM tutorias_contenidos WHERE id='$id'";
$resultado = mysqli_query($conexion, $sql);

$fila = mysqli_fetch_assoc($resultado);

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="css/estilo.css">
<div class="separador"></div>
<div class="separador"></div>
<div class="contenedor-admin">

    <h1>Editar Vista Tutorías</h1>

    <div class="layout-editor">

        <!-- 🔥 PANEL IZQUIERDO -->
        <div class="editor-panel">

            <form 
                action="guardar_tutorias.php" 
                method="POST"
                enctype="multipart/form-data"
                class="form-admin"
            >

                <input 
                    type="hidden" 
                    name="id" 
                    value="<?php echo $fila['id']; ?>"
                >

                <!-- 🔵 BLOQUE 1 -->
                <h2 class="titulo-galeria-admin">
                    Bloque 1
                </h2>

                <div class="grupo-form">

                    <label>Título Bloque 1</label>

                    <input 
                        type="text"
                        name="titulo_bloque1"
                        value="<?php echo $fila['titulo_bloque1']; ?>"
                    >

                </div>

                <div class="grupo-form">

                    <label>Texto Resumen Bloque 1</label>

                    <textarea 
                        name="resumen_bloque1"
                        rows="5"
                    ><?php echo $fila['resumen_bloque1']; ?></textarea>

                </div>

                <div class="grupo-form">

                    <label>Texto Completo Bloque 1</label>

                    <textarea 
                        name="completo_bloque1"
                        rows="8"
                    ><?php echo $fila['completo_bloque1']; ?></textarea>

                </div>

                <div class="grupo-form">

                    <label>Imagen Bloque 1</label>

                    <input 
                        type="file"
                        name="imagen_bloque1"
                    >

                    <br><br>

                    <img 
                        src="../vista_tutorias/images/<?php echo $fila['imagen_bloque1']; ?>"
                        width="200"
                        id="preview_imagen_bloque1"
                    >

                </div>

                <hr>

                <!-- 🔴 BLOQUE 2 -->
                <h2 class="titulo-galeria-admin">
                    Bloque 2
                </h2>

                <div class="grupo-form">

                    <label>Título Bloque 2</label>

                    <input 
                        type="text"
                        name="titulo_bloque2"
                        value="<?php echo $fila['titulo_bloque2']; ?>"
                    >

                </div>

                <div class="grupo-form">

                    <label>Texto Resumen Bloque 2</label>

                    <textarea 
                        name="resumen_bloque2"
                        rows="5"
                    ><?php echo $fila['resumen_bloque2']; ?></textarea>

                </div>

                <div class="grupo-form">

                    <label>Texto Completo Bloque 2</label>

                    <textarea 
                        name="completo_bloque2"
                        rows="8"
                    ><?php echo $fila['completo_bloque2']; ?></textarea>

                </div>

                <div class="grupo-form">

                    <label>Imagen Bloque 2</label>

                    <input 
                        type="file"
                        name="imagen_bloque2"
                    >

                    <br><br>

                    <img 
                        src="../vista_tutorias/images/<?php echo $fila['imagen_bloque2']; ?>"
                        width="200"
                        id="preview_imagen_bloque2"
                    >

                </div>

                <hr>

                <!-- 🔥 RIESGOS -->
                <h2 class="titulo-galeria-admin">
                    Riesgos Escolares
                </h2>

                <?php for($i=1; $i<=6; $i++){ ?>

                    <div class="grupo-form">

                        <label>Título Riesgo <?php echo $i; ?></label>

                        <input 
                            type="text"
                            name="titulo_riesgo<?php echo $i; ?>"
                            value="<?php echo $fila['titulo_riesgo'.$i]; ?>"
                        >

                    </div>

                    <div class="grupo-form">

                        <label>Texto Riesgo <?php echo $i; ?></label>

                        <textarea 
                            name="texto_riesgo<?php echo $i; ?>"
                            rows="4"
                        ><?php echo $fila['texto_riesgo'.$i]; ?></textarea>

                    </div>

                    <div class="grupo-form">

                        <label>Imagen Riesgo <?php echo $i; ?></label>

                        <input 
                            type="file"
                            name="imagen_riesgo<?php echo $i; ?>"
                        >

                        <br><br>

                        <img 
                            src="../vista_tutorias/images/<?php echo $fila['imagen_riesgo'.$i]; ?>"
                            width="180"
                            id="preview_imagen_riesgo<?php echo $i; ?>"
                        >

                    </div>

                    <hr>

                <?php } ?>

                <!-- 🔥 BOTÓN -->
                <button type="submit" class="btn-guardar">
                    Guardar Cambios
                </button>

            </form>

        </div>

        <!-- 🔥 PANEL DERECHO -->
        <div class="preview-panel">

            <div class="preview-header">
                Vista real del sitio
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