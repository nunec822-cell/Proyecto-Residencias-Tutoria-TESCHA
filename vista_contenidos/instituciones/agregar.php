<?php


require_once("../../PIT_V.4.0/sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../PIT_V.4.0/indexloguin.php");
    exit();
}
include("../../base_sist/conect_sist.php");
include("../../PIT_V.4.0/includes_pit/sidebar_admin.php");

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="../css/estilo.css">
<div class="separador"></div>
<div class="separador"></div>
<div class="contenedor-admin">

    <h1>Agregar Institución</h1>

    <form
        action="guardar.php"
        method="POST"
        enctype="multipart/form-data"
        class="form-admin"
    >

        <div class="grupo-form">

            <label>Título</label>

            <input
                type="text"
                name="titulo"
                required
            >

        </div>

        <div class="grupo-form">

            <label>Descripción</label>

            <textarea
                name="descripcion"
                rows="5"
                required
            ></textarea>

        </div>

        <div class="grupo-form">

            <label>Link</label>

            <input
                type="text"
                name="link"
                required
            >

        </div>

        <div class="grupo-form">

            <label>Imagen</label>

            <input
                type="file"
                name="imagen"
                required
            >

        </div>

        <button
            type="submit"
            class="btn-guardar"
        >
            Guardar
        </button>

    </form>

</div>

<?php include("../../includes/footer.php"); ?>