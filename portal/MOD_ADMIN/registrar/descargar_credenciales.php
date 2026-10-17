<?php
/*=========================================================
    Descarga ÚNICA de las credenciales recién generadas.

    Las contraseñas ya no se guardan en ningún archivo del
    servidor: viven en la sesión del administrador desde que
    registra a los usuarios hasta que las descarga aquí.
    Tras la descarga se eliminan.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
requerir_permiso('usuarios.gestionar');

$pendientes = $_SESSION['credenciales_pendientes'] ?? [];

if (empty($pendientes)) {
    http_response_code(404);
    echo 'No hay credenciales pendientes de descargar. '
       . 'Se generan al registrar usuarios y se pueden descargar una sola vez.';
    exit();
}

/* Evita que Excel interprete un texto como fórmula */
$limpiar = function ($valor) {
    $valor = (string) $valor;
    return ($valor !== '' && strpos('=+-@', $valor[0]) !== false) ? "'" . $valor : $valor;
};

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="credenciales_usuarios.csv"');
header('Cache-Control: no-store');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF"; /* BOM para que Excel respete los acentos */

$salida = fopen('php://output', 'w');

fputcsv($salida, ['ID', 'Fecha', 'Usuario', 'Nombre Completo', 'Contraseña', 'Tipo', 'Roles', 'Estado']);

foreach ($pendientes as $fila) {
    fputcsv($salida, array_map($limpiar, $fila));
}

fclose($salida);

unset($_SESSION['credenciales_pendientes']);
exit();
