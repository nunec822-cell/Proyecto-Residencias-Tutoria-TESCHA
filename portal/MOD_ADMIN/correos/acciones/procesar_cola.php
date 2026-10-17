<?php
/*=========================================================
    ADMIN · enviar ahora los correos pendientes (solo POST)
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('usuarios.gestionar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}
sist_requerir_csrf();

include __DIR__ . '/../../../base_pit/conect_pit.php';

$r = sist_correos_procesar($conn, 25);

$msg = 'Enviados: ' . $r['enviados'] . ' · con error: ' . $r['fallidos'] . ' · aún pendientes: ' . $r['pendientes'] . '.';
header('Location: ../index.php?' . ($r['fallidos'] > 0 ? 'error=' : 'ok=') . urlencode($msg));
exit();
