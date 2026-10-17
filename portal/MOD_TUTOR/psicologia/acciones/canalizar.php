<?php
/*=========================================================
    TUTOR · canalizar a atención psicológica (solo POST)
    Con id_psico canaliza un aviso de profesor; sin él, canaliza directo a un tutorado suyo.
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

$r = sist_psico_canalizar(
    $conn,
    (int) $_SESSION['id_usuario'],
    (int) ($_POST['id_psico'] ?? 0),
    (int) ($_POST['id_tutorado'] ?? 0),
    (string) ($_POST['motivo'] ?? '')
);

if (!$r['ok']) {
    header('Location: ../index.php?error=' . urlencode($r['error']));
    exit();
}

sist_correos_procesar($conn);   /* después del commit; un fallo de correo no deshace nada */

$q = 'ok=' . urlencode('Canalización registrada (folio ' . $r['id'] . '). Se avisó al alumno, a los psicólogos, al administrador y al jefe de carrera.')
   . '&id=' . (int) $r['id'];
if (!empty($r['sin_jefe'])) {
    $q .= '&aviso=' . urlencode('No hay un jefe de carrera registrado para la carrera del alumno; solo se avisó al administrador.');
}

header('Location: ../index.php?' . $q);
exit();
