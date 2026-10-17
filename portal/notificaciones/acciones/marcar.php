<?php
/*=========================================================
    NOTIFICACIONES · marcar como leída (solo POST)
    Solo puede marcar las suyas (el id_usuario sale de la sesión).
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}
sist_requerir_csrf();

include __DIR__ . '/../../base_pit/conect_pit.php';

$idUsuario = (int) $_SESSION['id_usuario'];

try {
    if (!empty($_POST['todas'])) {
        $stmt = sist_db_preparar($conn, "UPDATE notificaciones SET leida = 1 WHERE id_usuario = ? AND leida = 0");
        $stmt->bind_param('i', $idUsuario);
    } else {
        $id   = (int) ($_POST['id'] ?? 0);
        $stmt = sist_db_preparar($conn, "UPDATE notificaciones SET leida = 1 WHERE id_notificacion = ? AND id_usuario = ?");
        $stmt->bind_param('ii', $id, $idUsuario);
    }
    sist_db_ejecutar($stmt, 'marcar leída');
    $stmt->close();
} catch (Throwable $e) {
    sist_log('notificaciones/marcar: ' . $e->getMessage());
    header('Location: ../index.php?error=' . urlencode('No se pudo actualizar la notificación.'));
    exit();
}

header('Location: ../index.php');
exit();
