<?php
/*=========================================================
    PLANTILLA · endpoint de datos (para fetch/AJAX): responde JSON
    Sin sesión o sin permiso responde 401/403 en JSON automáticamente.
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('plantilla.ver');

include __DIR__ . '/../../../base_pit/conect_pit.php';

$total = (int) $conn->query("SELECT COUNT(*) FROM plantilla_registros")->fetch_row()[0];
$p     = sist_paginacion($total, 20);

$stmt = $conn->prepare("SELECT id, titulo, estado FROM plantilla_registros ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param('ii', $p['limite'], $p['offset']);
$stmt->execute();

sist_json([
    'success' => true,
    'pagina'  => $p['pagina'],
    'paginas' => $p['paginas'],
    'datos'   => $stmt->get_result()->fetch_all(MYSQLI_ASSOC),
]);
