<?php
/*=========================================================
    ADMIN · guardar el correo de un usuario (solo POST)
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('usuarios.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}
sist_requerir_csrf();

include __DIR__ . '/../../../base_pit/conect_pit.php';

/* A dónde volver (solo parámetros conocidos) */
parse_str((string) ($_POST['volver'] ?? ''), $v);
$volver = http_build_query(array_filter([
    'q'      => (string) ($v['q'] ?? ''),
    'ver'    => in_array($v['ver'] ?? '', ['sin_correo', 'todos'], true) ? $v['ver'] : '',
    'pagina' => (int) ($v['pagina'] ?? 0) ?: '',
], fn($x) => $x !== '' && $x !== null));
$base = '../index.php' . ($volver !== '' ? '?' . $volver . '&' : '?');

$id     = (int) ($_POST['id_usuario'] ?? 0);
$correo = trim((string) ($_POST['correo'] ?? ''));

if ($id <= 0) {
    header('Location: ' . $base . 'error=' . urlencode('Usuario no válido.'));
    exit();
}
if ($correo !== '' && !sist_correo_valido($correo)) {
    header('Location: ' . $base . 'error=' . urlencode('El correo no es válido.'));
    exit();
}

try {
    $valor = $correo === '' ? null : $correo;
    $stmt  = sist_db_preparar($conn, "UPDATE usuarios SET correo = ? WHERE id_usuario = ?");
    $stmt->bind_param('si', $valor, $id);
    sist_db_ejecutar($stmt, 'guardar correo');
    $stmt->close();
} catch (Throwable $e) {
    sist_log('correos/guardar: ' . $e->getMessage());
    header('Location: ' . $base . 'error=' . urlencode('No se pudo guardar el correo (¿se aplicó la migración 002?).'));
    exit();
}

header('Location: ' . $base . 'ok=' . urlencode($correo === '' ? 'Correo eliminado.' : 'Correo guardado.'));
exit();
