<?php
/*=========================================================
    JEFE DE CARRERA · confirmar la asignación (solo POST)
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('asesorias.asignar');

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

$idUsuario  = (int) $_SESSION['id_usuario'];
$idAsesoria = (int) ($_POST['id_asesoria'] ?? 0);

/* Un jefe de carrera solo puede asignar asesorías de su propia carrera */
$carrera = null;
if (tiene_rol('JEFE_CARRERA')) {
    $carrera = (string) (sist_db_valor($conn, "SELECT carrera FROM personal_academico WHERE id_usuario = ? AND activo = 1", 'i', [$idUsuario]) ?? '');
    if ($carrera === '') {
        header('Location: ../index.php?error=' . urlencode('Tu usuario no tiene carrera registrada.'));
        exit();
    }
}

$r = sist_asesoria_asignar(
    $conn,
    $idAsesoria,
    $idUsuario,
    $carrera,
    (int) ($_POST['id_profesor'] ?? 0),
    trim((string) ($_POST['fecha'] ?? '')),
    trim((string) ($_POST['hora_inicio'] ?? '')),
    trim((string) ($_POST['hora_fin'] ?? '')),
    (string) ($_POST['lugar'] ?? '')
);

if (!$r['ok']) {
    header('Location: ../index.php?error=' . urlencode($r['error']));
    exit();
}

sist_correos_procesar($conn);   /* después del commit; un fallo de correo no deshace nada */

header('Location: ../index.php?ok=' . urlencode('Asesoría asignada (folio ' . $idAsesoria . '). Se avisó al alumno, al administrador, al tutor y al profesor.'));
exit();
