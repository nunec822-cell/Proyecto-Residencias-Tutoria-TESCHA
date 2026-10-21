<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

require_once("../../sesion.php");

requerir_rol('ADMIN');

/* Solo POST con token: un enlace o imagen de otra página ya no puede borrar asignaciones */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}
sist_requerir_csrf();

include("../../base_pit/conect_pit.php");

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $borrar = sist_db_preparar($conn, "DELETE FROM docente_grupos WHERE id_asignacion = ?");
    $borrar->bind_param('i', $id);
    sist_db_ejecutar($borrar, 'eliminar asignación');
    $borrar->close();
}

header("Location: index.php");
exit();
