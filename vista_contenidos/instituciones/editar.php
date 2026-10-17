<?php
require_once __DIR__ . '/../../core/bootstrap.php';

require_once("../../portal/sesion.php");

requerir_rol('ADMIN');
include("../../base_sist/conect_sist.php");
include("../../portal/includes_pit/sidebar_admin.php");


$id = (int)($_GET['id']);

$sql = "
SELECT *
FROM instituciones_items
WHERE id='$id'
";

$resultado = mysqli_query($conexion,$sql);

$fila = mysqli_fetch_assoc($resultado);

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="../css/estilo.css">
<div class="separador"></div>
<div class="separador"></div>
<div class="contenedor-admin">

    <h1>Editar Institución</h1>

    <form
        action="actualizar.php"
        method="POST"
        enctype="multipart/form-data"
        class="form-admin"
    >

        <input
            type="hidden"
            name="id"
            value="<?php echo $fila['id']; ?>"
        >

        <div class="grupo-form">

            <label>Título</label>

            <input
                type="text"
                name="titulo"
                value="<?php echo $fila['titulo']; ?>"
            >

        </div>

        <div class="grupo-form">

            <label>Descripción</label>

            <textarea
                name="descripcion"
                rows="5"
            ><?php echo $fila['descripcion']; ?></textarea>

        </div>

        <div class="grupo-form">

            <label>Link</label>

            <input
                type="text"
                name="link"
                value="<?php echo $fila['link']; ?>"
            >

        </div>

        <div class="grupo-form">

            <label>Nueva Imagen</label>

            <input
                type="file"
                name="imagen"
            >

            <input
                type="hidden"
                name="imagen_actual"
                value="<?php echo $fila['imagen']; ?>"
            >

            <br><br>

            <img
                src="../../vista_inst_vinculantes/images/<?php echo $fila['imagen']; ?>"
                width="150"
            >

        </div>

        <button
            type="submit"
            class="btn-guardar"
        >
            Actualizar
        </button>

    </form>

</div>

<?php include("../../includes/footer.php"); ?>