<?php
/*=========================================================
    TUTOR · guardar la canalización a asesoría (solo POST)
    permiso → método → CSRF → lógica (core/asesorias.php) → correos → redirigir
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('asesorias.canalizar');

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

$r = sist_asesoria_crear(
    $conn,
    (int) $_SESSION['id_usuario'],
    (int) ($_POST['id_tutorado'] ?? 0),
    (int) ($_POST['id_materia'] ?? 0),
    (string) ($_POST['motivo'] ?? '')
);

if (!$r['ok']) {
    header('Location: ../index.php?error=' . urlencode($r['error']));
    exit();
}

sist_correos_procesar($conn);   /* después del commit; un fallo de correo no deshace nada */

$q = 'ok=' . urlencode('Canalización registrada (folio ' . $r['id'] . '). Se avisó al jefe de carrera, al alumno y al administrador.')
   . '&id=' . (int) $r['id'];
if (!empty($r['sin_jefe'])) {
    $q .= '&aviso=' . urlencode('No hay un jefe de carrera registrado para la carrera del alumno; solo se avisó al administrador.');
}

header('Location: ../index.php?' . $q);
exit();
