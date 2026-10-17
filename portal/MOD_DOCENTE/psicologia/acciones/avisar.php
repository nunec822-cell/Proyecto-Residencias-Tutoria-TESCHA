<?php
/*=========================================================
    PROFESOR · enviar el aviso al tutor (solo POST)
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('psicologia.avisar');

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

$r = sist_psico_avisar(
    $conn,
    (int) $_SESSION['id_usuario'],
    (int) ($_POST['id_tutorado'] ?? 0),
    (string) ($_POST['motivo'] ?? '')
);

if (!$r['ok']) {
    header('Location: ../index.php?error=' . urlencode($r['error']));
    exit();
}

sist_correos_procesar($conn);   /* después del commit; un fallo de correo no deshace nada */

header('Location: ../index.php?ok=' . urlencode('Aviso enviado al tutor (folio ' . $r['id'] . ').') . '&id=' . (int) $r['id']);
exit();
