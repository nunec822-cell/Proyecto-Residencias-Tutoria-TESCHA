<?php

require_once("../../PIT_V.4.0/sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../PIT_V.4.0/indexloguin.php");
    exit();
}
include("../../base_sist/conect_sist.php");
include("../../PIT_V.4.0/includes_pit/sidebar_admin.php");

$id = $_GET['id'];

$sql = "SELECT * FROM avisos_inicio WHERE id='$id'";
$res = mysqli_query($conexion,$sql);

$aviso = mysqli_fetch_assoc($res);

/* ACTUALIZAR */
if(isset($_POST['actualizar'])){

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $enlace = $_POST['enlace'];
    $estado = $_POST['estado'];

    $imagen = $aviso['imagen'];
    $pdf = $aviso['pdf'];

    /* NUEVA IMAGEN */
    if(!empty($_FILES['imagen']['name'])){

        $imagen = time()."_".$_FILES['imagen']['name'];

        move_uploaded_file(
            $_FILES['imagen']['tmp_name'],
            "../../assets/avisos/".$imagen
        );
    }

    /* NUEVO PDF */
    if(!empty($_FILES['pdf']['name'])){

        $pdf = time()."_".$_FILES['pdf']['name'];

        move_uploaded_file(
            $_FILES['pdf']['tmp_name'],
            "../../assets/avisos/".$pdf
        );
    }

    $sqlUpdate = "
    UPDATE avisos_inicio SET

        titulo='$titulo',
        descripcion='$descripcion',
        imagen='$imagen',
        enlace='$enlace',
        pdf='$pdf',
        estado='$estado'

    WHERE id='$id'
    ";

    mysqli_query($conexion,$sqlUpdate);

    echo "
    <script>
        alert('Aviso actualizado correctamente');
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

    <h1>Editar Aviso</h1>

    <form method="POST" enctype="multipart/form-data" class="form-admin">

        <div class="grupo-form">
            <label>Título</label>
            <input
                type="text"
                name="titulo"
                value="<?php echo $aviso['titulo']; ?>"
            >
        </div>

        <div class="grupo-form">
            <label>Descripción</label>

            <textarea
                name="descripcion"
                rows="6"
            ><?php echo $aviso['descripcion']; ?></textarea>
        </div>

        <div class="grupo-form">

            <label>Imagen actual</label>

            <?php if($aviso['imagen']!=""){ ?>

                <img
                    src="../../assets/avisos/<?php echo $aviso['imagen']; ?>"
                    class="preview-mini"
                >

            <?php } ?>

            <br><br>

            <input
                type="file"
                name="imagen"
            >

        </div>

        <div class="grupo-form">

            <label>PDF actual</label>

            <?php if($aviso['pdf']!=""){ ?>

                <p>
                    <?php echo $aviso['pdf']; ?>
                </p>

            <?php } ?>

            <input
                type="file"
                name="pdf"
            >

        </div>

        <div class="grupo-form">

            <label>Enlace</label>

            <input
                type="text"
                name="enlace"
                value="<?php echo $aviso['enlace']; ?>"
            >

        </div>

        <div class="grupo-form">

            <label>Estado</label>

            <select
                name="estado"
                style="width:100%;padding:15px;"
            >

                <option
                    value="Activo"
                    <?php if($aviso['estado']=="Activo") echo "selected"; ?>
                >
                    Activo
                </option>

                <option
                    value="Inactivo"
                    <?php if($aviso['estado']=="Inactivo") echo "selected"; ?>
                >
                    Inactivo
                </option>

            </select>

        </div>

        <button
            type="submit"
            name="actualizar"
            class="btn-guardar"
        >
            Actualizar Aviso
        </button>

    </form>

</div>

<?php include("../../includes/footer.php"); ?>