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
    
    <h1>Administrar Avisos y Novedades</h1>

    <div class="acciones-admin">

        <a
            href="../index.php"
            class="btn-volver"
        >
            ← Volver al Gestor
        </a>

        <a
            href="agregar.php"
            class="btn-subir"
        >
            + Agregar Aviso
        </a>

    </div>

    <table class="tabla-admin">

        <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>Título</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>

        <?php

        $sql = "
        SELECT *
        FROM avisos_inicio
        ORDER BY id DESC
        ";

        $resultado = mysqli_query($conexion,$sql);

        while($fila = mysqli_fetch_assoc($resultado)){

        ?>

        <tr>

            <td>
                <?php echo $fila['id']; ?>
            </td>

            <td>

                <?php if(!empty($fila['imagen'])){ ?>

                    <img
                        src="../../assets/avisos/<?php echo $fila['imagen']; ?>"
                        width="80"
                    >

                <?php } ?>

            </td>

            <td>
                <?php echo $fila['titulo']; ?>
            </td>

            <td>
                <?php echo $fila['estado']; ?>
            </td>

            <td>

                <div class="acciones-tabla">

                    <a
                        href="editar.php?id=<?php echo $fila['id']; ?>"
                        class="btn-editar-inst"
                    >
                        Editar
                    </a>

                    <a
                        href="eliminar.php?id=<?php echo $fila['id']; ?>"
                        class="btn-eliminar-inst"
                        onclick="return confirm('¿Eliminar aviso?')"
                    >
                        Eliminar
                    </a>

                </div>

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

<?php include("../../includes/footer.php"); ?>