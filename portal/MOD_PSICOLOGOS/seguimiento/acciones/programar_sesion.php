<?php
/*=========================================================
    PSICÓLOGO · programar una sesión (solo POST)
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('psicologia.atender');

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

$idPsico = (int) ($_POST['id_psico'] ?? 0);
$r = sist_psico_programar_sesion(
    $conn,
    (int) $_SESSION['id_usuario'],
    $idPsico,
    (string) ($_POST['fecha'] ?? ''),
    (string) ($_POST['hora_inicio'] ?? ''),
    (string) ($_POST['hora_fin'] ?? ''),
    (string) ($_POST['lugar'] ?? '')
);

if (!$r['ok']) {
    header('Location: ../index.php?error=' . urlencode($r['error']) . ($idPsico > 0 ? '&id=' . $idPsico : ''));
    exit();
}

sist_correos_procesar($conn);   /* después del commit; un fallo de correo no deshace nada */

header('Location: ../index.php?ok=' . urlencode('Sesión programada. Se avisó al alumno y al tutor.') . ($idPsico > 0 ? '&id=' . $idPsico : ''));
exit();
