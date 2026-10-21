<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

require_once("../../sesion.php");

requerir_rol('ADMIN');
sist_requerir_csrf_enlace();   /* el enlace debe traer el token de la sesión */

include("../../base_pit/conect_pit.php");

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $up = sist_db_preparar(
        $conn,
        "UPDATE canalizaciones
         SET estado = 'CERRADO', fecha_cierre = NOW(), nuevos_reportes = 0
         WHERE id_tutorado = ? AND estado = 'ATENDIDO'"
    );
    $up->bind_param('i', $id);
    sist_db_ejecutar($up, 'cerrar canalización');
    $up->close();
}

header("Location: ../revision_anexo14/index.php");
exit();
