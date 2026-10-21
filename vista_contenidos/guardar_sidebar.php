<?php
require_once __DIR__ . '/../core/bootstrap.php';
requerir_permiso('contenidos.gestionar');
sist_requerir_csrf_post();   /* el formulario debe traer el token de la sesión */

include("../base_sist/conect_sist.php");

$id = (int)($_POST['id']);
$tema = sist_esc($_POST['tema']);

$sql = "
UPDATE tema_sidebar
SET tema='$tema'
WHERE id='$id'
";

mysqli_query(
    $conexion,
    $sql
);

header(
    "Location: editar_sidebar.php?ok=1"
);
exit;