<?php
/*=========================================================
    PLANTILLA · acción que modifica datos (solo POST)
    Patrón: permiso → método → CSRF → validar → consulta preparada → redirigir
=========================================================*/

require_once __DIR__ . '/../../../../core/bootstrap.php';
require_once __DIR__ . '/../../../sesion.php';
requerir_permiso('plantilla.crear');                       // 1) permiso, SIEMPRE

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {               // 2) solo POST
    header('Location: ../index.php');
    exit();
}
sist_requerir_csrf();                                      // 3) token CSRF

include __DIR__ . '/../../../base_pit/conect_pit.php';

$titulo = trim($_POST['titulo'] ?? '');                    // 4) validar entrada
if ($titulo === '' || mb_strlen($titulo) > 150) {
    header('Location: ../index.php?error=' . urlencode('El título es obligatorio (máx. 150 caracteres).'));
    exit();
}

$stmt = $conn->prepare(                                    // 5) consulta PREPARADA
    "INSERT INTO plantilla_registros (titulo, estado, creado_en) VALUES (?, 'activo', NOW())"
);
$stmt->bind_param('s', $titulo);
$stmt->execute();

header('Location: ../index.php?ok=1');                     // 6) redirigir (evita reenvío del formulario)
exit();
