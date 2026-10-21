<?php
require_once __DIR__ . '/../../core/bootstrap.php';

requerir_permiso('contenidos.gestionar');
sist_requerir_csrf_enlace();   /* el enlace debe traer el token de la sesión */

include("../../base_sist/conect_sist.php");

$id = (int) ($_GET['id'] ?? 0);

$fila = $id > 0
    ? (sist_db_filas($conexion, "SELECT imagen FROM instituciones_items WHERE id = ?", 'i', [$id])[0] ?? null)
    : null;

if ($fila) {

    /* Solo se borra un ARCHIVO con nombre (antes, con imagen vacía, apuntaba a la carpeta) */
    $imagen = basename((string) $fila['imagen']);
    $ruta   = __DIR__ . '/../../vista_inst_vinculantes/images/' . $imagen;

    if ($imagen !== '' && is_file($ruta)) {
        unlink($ruta);
    }

    $borrar = sist_db_preparar($conexion, "DELETE FROM instituciones_items WHERE id = ?");
    $borrar->bind_param('i', $id);
    sist_db_ejecutar($borrar, 'eliminar institución');
    $borrar->close();
}

header("Location: index.php");
exit();
