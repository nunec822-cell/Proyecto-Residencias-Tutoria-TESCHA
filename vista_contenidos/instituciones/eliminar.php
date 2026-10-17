<?php
require_once __DIR__ . '/../../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');

include("../../base_sist/conect_sist.php");

$id = (int)($_GET['id']);

$sqlImagen="
SELECT imagen
FROM instituciones_items
WHERE id='$id'
";

$res=mysqli_query($conexion,$sqlImagen);

$fila=mysqli_fetch_assoc($res);

if(file_exists("../../vista_inst_vinculantes/images/".basename((string)$fila['imagen']))){

    unlink("../../vista_inst_vinculantes/images/".basename((string)$fila['imagen']));
}

$sql="
DELETE FROM instituciones_items
WHERE id='$id'
";

mysqli_query($conexion,$sql);

header("Location:index.php");
exit();

?>