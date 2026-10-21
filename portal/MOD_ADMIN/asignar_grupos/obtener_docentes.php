<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

requerir_permiso('grupos.asignar');

include("../../base_pit/conect_pit.php");

$carrera = trim((string) ($_GET['carrera'] ?? ''));

echo '<option value="">Seleccione un docente</option>';

if ($carrera === '') {
    exit();
}

$docentes = sist_db_filas(
    $conn,
    "SELECT id_personal, CONCAT(apellido_p, ' ', apellido_m, ' ', nombre) AS docente
     FROM personal_academico
     WHERE activo = 1 AND carrera = ?
     ORDER BY apellido_p",
    's',
    [$carrera]
);

foreach ($docentes as $fila) {
    echo '<option value="' . (int) $fila['id_personal'] . '">' . e(mb_strtoupper($fila['docente'])) . '</option>';
}
