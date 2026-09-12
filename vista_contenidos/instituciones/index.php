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

    <h1>Administrar Instituciones</h1>

    <!-- BOTONES DE ACCIÓN -->
    <div class="acciones-admin">

        <a
            href="../editar_instituciones.php?id=1"
            class="btn-volver"
        >
            ← Volver a Editar Instituciones
        </a>

        <a
            href="agregar.php"
            class="btn-subir"
            style="text-decoration:none;"
        >
            + Agregar Institución
        </a>

    </div>

    <br>

    <table class="tabla-admin">

        <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>Título</th>
            <th>Acciones</th>
        </tr>

        <?php

        $sql = "
        SELECT *
        FROM instituciones_items
        ORDER BY id DESC
        ";

        $resultado = mysqli_query($conexion, $sql);

        while($fila = mysqli_fetch_assoc($resultado)){
        ?>

        <tr>

            <td>
                <?php echo $fila['id']; ?>
            </td>

            <td>

                <img
                    src="../../vista_inst_vinculantes/images/<?php echo $fila['imagen']; ?>"
                    width="80"
                >

            </td>

            <td>
                <?php echo $fila['titulo']; ?>
            </td>

            <td>

    <div class="acciones-tabla">

        <a href="editar.php?id=<?php echo $fila['id']; ?>"
           class="btn-editar-inst">
           Editar
        </a>

        <a href="eliminar.php?id=<?php echo $fila['id']; ?>"
           class="btn-eliminar-inst"
           onclick="return confirm('¿Eliminar institución?')">
           Eliminar
        </a>

    </div>

</td>

        </tr>

        <?php } ?>

    </table>

</div>

<?php include("../../includes/footer.php"); ?>