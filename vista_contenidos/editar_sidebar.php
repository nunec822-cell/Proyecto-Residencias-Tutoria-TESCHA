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
FROM tema_sidebar
LIMIT 1
";

$res = mysqli_query($conexion,$sql);

$tema = mysqli_fetch_assoc($res);

?>
<div class="separador"></div>
<div class="separador"></div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/estilo.css">

<div class="contenedor-admin">

    <h1>
        Personalización del Sidebar
    </h1>

    <div class="layout-editor">

        <!-- PANEL IZQUIERDO -->
        <div class="editor-panel">

            <form
                action="guardar_sidebar.php"
                method="POST"
                class="form-admin"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $tema['id']; ?>"
                >

                <div class="grupo-form">

                    <label>
                        Seleccionar tema visual
                    </label>

                    <select
                        name="tema"
                        required
                    >

                        <option value="normal"
                        <?php if($tema['tema']=="normal") echo "selected"; ?>>
                            🔴 Normal
                        </option>

                        <option value="navidad"
                        <?php if($tema['tema']=="navidad") echo "selected"; ?>>
                            🎄 Navidad
                        </option>

                        <option value="muertos"
                        <?php if($tema['tema']=="muertos") echo "selected"; ?>>
                            🎃 Día de Muertos
                        </option>

                        <option value="patria"
                        <?php if($tema['tema']=="patria") echo "selected"; ?>>
                            🇲🇽 Independencia
                        </option>

                        <option value="amor"
                        <?php if($tema['tema']=="amor") echo "selected"; ?>>
                            ❤️ Amor y Amistad
                        </option>

                    </select>

                </div>

                <div class="grupo-form">

                    <h2 class="titulo-galeria-admin">
                        Vista previa de temas
                    </h2>

                    <p>🔴 Normal = Tema institucional</p>
                    <p>🎄 Navidad = Verde con decoración navideña</p>
                    <p>🎃 Día de Muertos = Naranja con decoración cultural</p>
                    <p>🇲🇽 Independencia = Colores patrios</p>
                    <p>❤️ Amor y Amistad = Rosa con corazones</p>

                </div>

                <button
                    type="submit"
                    class="btn-guardar"
                >
                    Guardar Tema
                </button>

            </form>

        </div>

        <!-- PANEL DERECHO -->
        <div class="preview-panel">

            <div class="preview-header">
                Vista real del Sidebar
            </div>

            <iframe
                src="../index.php"
                id="previewFrame"
            ></iframe>

        </div>

    </div>

</div>

<script src="js/admin.js"></script>

<?php include("../includes/footer.php"); ?>