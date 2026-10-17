<?php
/*=========================================================
    Guarda los permisos de un ROL o de un USUARIO.
    Solo POST, con token CSRF, y con el permiso permisos.gestionar.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once("../../sesion.php");
requerir_permiso('permisos.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

sist_requerir_csrf();

include("../../base_pit/conect_pit.php");

$accion   = $_POST['accion'] ?? '';
$permisos = array_values(array_unique(array_map('intval', (array) ($_POST['permisos'] ?? []))));

try {
    $conn->begin_transaction();

    if ($accion === 'rol') {
        $id_tipo = (int) ($_POST['id_tipo'] ?? 0);

        $stmt = $conn->prepare("SELECT 1 FROM tipos WHERE id_tipo = ?");
        $stmt->bind_param('i', $id_tipo);
        $stmt->execute();
        if (!$stmt->get_result()->fetch_row()) {
            throw new Exception('Rol inválido');
        }

        $del = $conn->prepare("DELETE FROM rol_permiso WHERE id_tipo = ?");
        $del->bind_param('i', $id_tipo);
        $del->execute();

        /* Solo se insertan ids que existan en el catálogo */
        $ins = $conn->prepare(
            "INSERT IGNORE INTO rol_permiso (id_tipo, id_permiso)
             SELECT ?, id_permiso FROM permisos WHERE id_permiso = ?"
        );
        foreach ($permisos as $id_permiso) {
            $ins->bind_param('ii', $id_tipo, $id_permiso);
            $ins->execute();
        }

        $destino = 'index.php?ok=1&id_tipo=' . $id_tipo;

    } elseif ($accion === 'usuario') {
        $id_usuario = (int) ($_POST['id_usuario'] ?? 0);

        $stmt = $conn->prepare("SELECT 1 FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param('i', $id_usuario);
        $stmt->execute();
        if (!$stmt->get_result()->fetch_row()) {
            throw new Exception('Usuario inválido');
        }

        $del = $conn->prepare("DELETE FROM usuario_permiso WHERE id_usuario = ?");
        $del->bind_param('i', $id_usuario);
        $del->execute();

        $ins = $conn->prepare(
            "INSERT IGNORE INTO usuario_permiso (id_usuario, id_permiso)
             SELECT ?, id_permiso FROM permisos WHERE id_permiso = ?"
        );
        foreach ($permisos as $id_permiso) {
            $ins->bind_param('ii', $id_usuario, $id_permiso);
            $ins->execute();
        }

        $destino = 'index.php?ok=1&usuario=' . urlencode((string) ($_POST['usuario'] ?? ''));

    } else {
        throw new Exception('Acción inválida');
    }

    $conn->commit();
    header('Location: ' . $destino);
    exit();

} catch (Throwable $ex) {
    $conn->rollback();
    sist_log('Error al guardar permisos: ' . $ex->getMessage());
    header('Location: index.php?error=1');
    exit();
}
