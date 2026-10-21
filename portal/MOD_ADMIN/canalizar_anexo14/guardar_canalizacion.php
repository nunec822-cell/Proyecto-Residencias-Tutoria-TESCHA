<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

require_once("../../sesion.php");

requerir_rol('ADMIN');
sist_requerir_csrf_post();   /* el formulario debe traer el token de la sesión */

include("../../base_pit/conect_pit.php");

/*=========================================
DATOS
=========================================*/

$id_tutorado   = (int) ($_POST['id_tutorado'] ?? 0);
$id_tutor      = (int) ($_POST['id_tutor'] ?? 0);
$observaciones = trim((string) ($_POST['observaciones'] ?? ''));
$id_admin      = (int) $_SESSION['id_usuario'];

if ($id_tutorado <= 0 || $id_tutor <= 0) {
    echo "ERROR";
    exit();
}

/*=========================================
VERIFICAR E INSERTAR (una sola transacción)
=========================================*/

try {
    $conn->begin_transaction();

    $abierta = (int) sist_db_valor(
        $conn,
        "SELECT COUNT(*) FROM canalizaciones
         WHERE id_tutorado = ? AND estado IN ('PENDIENTE', 'EN PROCESO') FOR UPDATE",
        'i',
        [$id_tutorado]
    );

    if ($abierta > 0) {
        $conn->rollback();
        echo "EXISTE";
        exit();
    }

    $ins = sist_db_preparar(
        $conn,
        "INSERT INTO canalizaciones (id_tutorado, id_tutor, id_admin, observaciones) VALUES (?, ?, ?, ?)"
    );
    $ins->bind_param('iiis', $id_tutorado, $id_tutor, $id_admin, $observaciones);
    sist_db_ejecutar($ins, 'crear canalización');
    $id_canalizacion = (int) $conn->insert_id;
    $ins->close();

    $up = sist_db_preparar(
        $conn,
        "UPDATE anexo14_reportes SET id_canalizacion = ?
         WHERE id_tutorado = ? AND id_canalizacion IS NULL"
    );
    $up->bind_param('ii', $id_canalizacion, $id_tutorado);
    sist_db_ejecutar($up, 'ligar reportes a la canalización');
    $up->close();

    $conn->commit();
    echo "OK";
} catch (Throwable $e) {
    try { $conn->rollback(); } catch (Throwable $e2) {}
    sist_log('guardar_canalizacion: ' . $e->getMessage());
    echo "ERROR";
}
