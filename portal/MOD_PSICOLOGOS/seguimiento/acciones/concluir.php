<?php
/*=========================================================
    PSICÓLOGO · concluir el proceso (solo POST)
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
$r = sist_psico_concluir($conn, (int) $_SESSION['id_usuario'], $idPsico);

if (!$r['ok']) {
    header('Location: ../index.php?error=' . urlencode($r['error']) . ($idPsico > 0 ? '&id=' . $idPsico : ''));
    exit();
}

sist_correos_procesar($conn);   /* después del commit; un fallo de correo no deshace nada */

header('Location: ../index.php?ok=' . urlencode('Proceso concluido. Se avisó al alumno y al tutor.') . ($idPsico > 0 ? '&id=' . $idPsico : ''));
exit();
