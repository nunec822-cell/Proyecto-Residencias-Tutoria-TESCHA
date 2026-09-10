<?php
session_start();
require_once '../base_pit/conect_pit.php';

// ==========================
// VALIDAR ID
// ==========================
if (!isset($_GET['id'])) {
    die("ID de aviso no proporcionado");
}

$id = $_GET['id'];

// ==========================
// OBTENER AVISO
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
// ACTUALIZAR AVISO
// ==========================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $link = $_POST['link'];

    $imagen = $aviso['imagen'];
    $pdf = $aviso['pdf'];

    // IMAGEN
    if (!empty($_FILES['imagen']['name'])) {
        $imagen = time() . "_" . $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/avisos/imagenes/" . $imagen);
    }

    // PDF
    if (!empty($_FILES['pdf']['name'])) {
        $pdf = time() . "_" . $_FILES['pdf']['name'];
        move_uploaded_file($_FILES['pdf']['tmp_name'], "../uploads/avisos/pdfs/" . $pdf);
    }

    // UPDATE REAL
    $sql = "UPDATE avisos 
            SET titulo=?, descripcion=?, link=?, imagen=?, pdf=?
            WHERE id_aviso=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $titulo, $descripcion, $link, $imagen, $pdf, $id);

    if ($stmt->execute()) {

        echo "<script>
            alert('Aviso actualizado correctamente');
            window.location.href='inicioadmin.php';
        </script>";

    } else {

        echo "<script>
            alert('Error al actualizar aviso');
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Aviso</title>

    <link rel="stylesheet" href="css/estilo.css">

    <style>
        .form-container{
            max-width:600px;
            margin:40px auto;
            background:#fff;
            padding:25px;
            border-radius:12px;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
        }

        input, textarea{
            width:100%;
            padding:10px;
            margin-bottom:15px;
            border:1px solid #ccc;
            border-radius:8px;
        }

        button{
            width:100%;
            padding:12px;
            background:#8B1C1C;
            color:#fff;
            border:none;
            border-radius:8px;
            cursor:pointer;
            font-weight:bold;
        }
    </style>
</head>

<body>

<?php include '../includes_pit/header.php'; ?>

<div class="form-container">

    <h2>✏️ Editar Aviso</h2>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="titulo" value="<?php echo $aviso['titulo']; ?>">

        <textarea name="descripcion"><?php echo $aviso['descripcion']; ?></textarea>

        <input type="text" name="link" value="<?php echo $aviso['link']; ?>">

        <label>Imagen actual:</label>
        <p><?php echo $aviso['imagen']; ?></p>

        <input type="file" name="imagen">

        <label>PDF actual:</label>
        <p><?php echo $aviso['pdf']; ?></p>

        <input type="file" name="pdf">

        <button type="submit">Actualizar Aviso</button>

    </form>

</div>

<?php include '../../includes/footer.php'; ?>

</body>
</html>