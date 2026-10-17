<?php
require_once __DIR__ . '/../../core/bootstrap.php';
requerir_permiso('avisos.gestionar');
sist_iniciar_sesion();
require_once '../base_pit/conect_pit.php';

// ==========================
// VALIDAR ID
// ==========================
if (!isset($_GET['id'])) {
    die("ID de aviso no proporcionado");
}

$id = $_GET['id'];

// ==========================
// OBTENER DATOS DEL AVISO
// ==========================
$sql = "SELECT * FROM avisos WHERE id_aviso = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$aviso = $result->fetch_assoc();

if (!$aviso) {
    die("Aviso no encontrado");
}

// ==========================
// RUTAS DE ARCHIVOS
// ==========================
$imgPath = "../uploads/avisos/imagenes/" . basename((string) $aviso['imagen']);
$pdfPath = "../uploads/avisos/pdfs/" . basename((string) $aviso['pdf']);

// ==========================
// ELIMINAR ARCHIVOS FÍSICOS
// ==========================
if (!empty($aviso['imagen']) && file_exists($imgPath)) {
    unlink($imgPath);
}

if (!empty($aviso['pdf']) && file_exists($pdfPath)) {
    unlink($pdfPath);
}

// ==========================
// ELIMINAR DE BD
// ==========================
$sqlDelete = "DELETE FROM avisos WHERE id_aviso = ?";
$stmt = $conn->prepare($sqlDelete);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    echo "<script>
        alert('Aviso eliminado correctamente');
        window.location.href='inicioadmin.php';
    </script>";

} else {

    echo "<script>
        alert('Error al eliminar aviso');
        window.location.href='inicioadmin.php';
    </script>";
}
?>