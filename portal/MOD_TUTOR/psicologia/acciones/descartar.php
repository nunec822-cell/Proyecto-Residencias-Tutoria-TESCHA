<?php
/*=========================================================
    TUTOR · no canalizar un aviso de profesor (solo POST)
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('psicologia.canalizar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}
sist_requerir_csrf();

include __DIR__ . '/../../../base_pit/conect_pit.php';

if (!sist_psico_listo($conn)) {
    header('Location: ../index.php?error=' . urlencode('Falta aplicar las migraciones 002 y 003 en la base de datos.'));
    exit();
}

$r = sist_psico_descartar(
    $conn,
    (int) $_SESSION['id_usuario'],
    (int) ($_POST['id_psico'] ?? 0),
    (string) ($_POST['motivo_descarte'] ?? '')
);

if (!$r['ok']) {
    header('Location: ../index.php?error=' . urlencode($r['error']));
    exit();
}

sist_correos_procesar($conn);

header('Location: ../index.php?ok=' . urlencode('Aviso revisado; se informó al profesor que no se canalizó por ahora.'));
exit();
