<?php

require_once("../../PIT_V.4.0/sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../PIT_V.4.0/indexloguin.php");
    exit();
}
include("../../base_sist/conect_sist.php");
include("../../PIT_V.4.0/includes_pit/sidebar_admin.php");

if(isset($_POST['guardar'])){

    $titulo = mysqli_real_escape_string(
        $conexion,
        $_POST['titulo']
    );

    $descripcion = mysqli_real_escape_string(
        $conexion,
        $_POST['descripcion']
    );

    $enlace = mysqli_real_escape_string(
        $conexion,
        $_POST['enlace']
    );

    $estado = $_POST['estado'];

    $imagen = "";
    $pdf = "";

    /* ==========================
       SUBIR IMAGEN
    ========================== */

    if(!empty($_FILES['imagen']['name'])){

        $imagen = time() . "_" .
        $_FILES['imagen']['name'];

        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            "../../assets/avisos/" . $imagen
        );

    }

    /* ==========================
       SUBIR PDF
    ========================== */

    if(!empty($_FILES['pdf']['name'])){

        $pdf = time() . "_" .
        $_FILES['pdf']['name'];

        move_uploaded_file(
            $_FILES['pdf']['tmp_name'],
            "../../assets/avisos/" . $pdf
        );

    }

    /* ==========================
       INSERTAR
    ========================== */

    $sql = "
    INSERT INTO avisos_inicio
    (
        titulo,
        descripcion,
        imagen,
        enlace,
        pdf,
        estado
    )
    VALUES
    (
        '$titulo',
        '$descripcion',
        '$imagen',
        '$enlace',
        '$pdf',
        '$estado'
    )
    ";

    mysqli_query($conexion,$sql);

    echo "
    <script>
        alert('Aviso agregado correctamente');
        window.location='index.php';
    </script>
    ";

}

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="../css/estilo.css">
<div class="separador"></div>
<div class="separador"></div>
<div class="contenedor-admin">

    <h1>Agregar Aviso</h1>

    <div class="acciones-admin">

        <a
            href="index.php"
            class="btn-volver"
        >
            ← Volver
        </a>

    </div>

    <form
        method="POST"
        enctype="multipart/form-data"
        class="form-admin"
    >

        <div class="grupo-form">

            <label>
                Título
            </label>

            <input
                type="text"
                name="titulo"
            >

        </div>

        <div class="grupo-form">

            <label>
                Descripción
            </label>

            <textarea
                name="descripcion"
                rows="8"
            ></textarea>

        </div>

        <div class="grupo-form">

            <label>
                Imagen (Opcional)
            </label>

            <input
                type="file"
                name="imagen"
                accept="image/*"
            >

        </div>

        <div class="grupo-form">

            <label>
                PDF (Opcional)
            </label>

            <input
                type="file"
                name="pdf"
                accept=".pdf"
            >

        </div>

        <div class="grupo-form">

            <label>
                Enlace (Opcional)
            </label>

            <input
                type="text"
                name="enlace"
                placeholder="https://ejemplo.com"
            >

        </div>

        <div class="grupo-form">

            <label>
                Estado
            </label>

            <select
                name="estado"
                style="
                width:100%;
                padding:15px;
                border-radius:12px;
                border:1px solid #d1d5db;
                "
            >
                <option value="Activo">
                    Activo
                </option>

                <option value="Inactivo">
                    Inactivo
                </option>

            </select>

        </div>

        <button
            type="submit"
            name="guardar"
            class="btn-guardar"
        >
            Guardar Aviso
        </button>

    </form>

</div>

<?php include("../../includes/footer.php"); ?>