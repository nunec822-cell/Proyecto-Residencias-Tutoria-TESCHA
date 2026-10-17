<?php
/*=========================================================
    PROFESOR · guardar seguimiento de una asesoría (solo POST)
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('asesorias.seguimiento');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}
sist_requerir_csrf();

include __DIR__ . '/../../../base_pit/conect_pit.php';

if (!sist_asesorias_listo($conn)) {
    header('Location: ../index.php?error=' . urlencode('Falta aplicar la migración 002 en la base de datos.'));
    exit();
}

$r = sist_asesoria_seguimiento(
    $conn,
    (int) ($_POST['id_asesoria'] ?? 0),
    (int) $_SESSION['id_usuario'],
    (string) ($_POST['nota'] ?? ''),
    !empty($_POST['finalizar'])
);

if (!$r['ok']) {
    header('Location: ../index.php?error=' . urlencode($r['error']));
    exit();
}

header('Location: ../index.php?ok=' . urlencode($r['estado'] === 'FINALIZADA' ? 'Asesoría finalizada.' : 'Seguimiento guardado.'));
exit();
