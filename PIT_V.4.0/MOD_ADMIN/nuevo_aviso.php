<?php
session_start();
require_once '../base_pit/conect_pit.php';

// =========================
// GUARDAR AVISO
// =========================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $link = $_POST['link'];

    // IMAGEN
    $imagen = null;
    if (!empty($_FILES['imagen']['name'])) {
        $imagen = time() . "_" . $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/avisos/imagenes/" . $imagen);
    }

    // PDF
    $pdf = null;
    if (!empty($_FILES['pdf']['name'])) {
        $pdf = time() . "_" . $_FILES['pdf']['name'];
        move_uploaded_file($_FILES['pdf']['tmp_name'], "../uploads/avisos/pdfs/" . $pdf);
    }

    // 1. INSERT AVISO
    $sql = "INSERT INTO avisos (titulo, descripcion, link, imagen, pdf)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $titulo, $descripcion, $link, $imagen, $pdf);
    $stmt->execute();

    $id_aviso = $stmt->insert_id;

    // 2. INSERT TIPOS (IMPORTANTE)
    if (!empty($_POST['tipos'])) {

        foreach ($_POST['tipos'] as $id_tipo) {

            $sqlTipo = "INSERT INTO aviso_tipos (id_aviso, id_tipo)
                        VALUES (?, ?)";

            $stmt2 = $conn->prepare($sqlTipo);
            $stmt2->bind_param("ii", $id_aviso, $id_tipo);
            $stmt2->execute();
        }
    }

    echo "<script>
        alert('Aviso creado correctamente con roles');
        window.location.href='inicioadmin.php';
    </script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Aviso</title>

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

        .form-container h2{
            text-align:center;
            margin-bottom:20px;
            color:#8B1C1C;
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

        button:hover{
            background:#a32222;
        }

        .back{
            display:block;
            text-align:center;
            margin-top:10px;
            color:#333;
        }
    </style>
</head>

<body>

<?php include '../includes_pit/header.php'; ?>

<div class="form-container">

    <h2>📢 Crear Nuevo Aviso</h2>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="titulo" placeholder="Título del aviso">

        <textarea name="descripcion" placeholder="Descripción del aviso"></textarea>

        <input type="text" name="link" placeholder="Link (opcional)">

        <label>Imagen</label>
        <input type="file" name="imagen">

        <label>PDF</label>
        <input type="file" name="pdf">

        <button type="submit">Guardar Aviso</button>
        <h3>📌 Dirigido a: desliza hacia abajo para ver mas opcciones </h3>

<?php
$sqlTipos = "SELECT * FROM tipos";
$resTipos = $conn->query($sqlTipos);

while ($tipo = $resTipos->fetch_assoc()) {
?>
    <label style="display:block; margin:5px 0;">
        <input type="checkbox" name="tipos[]" value="<?php echo $tipo['id_tipo']; ?>">
        <?php echo $tipo['nombre']; ?>
    </label>
<?php } ?>
    </form>

    <a class="back" href="inicioadmin.php">⬅ Volver</a>

</div>

<?php include '../../includes/footer.php'; ?>

</body>
</html>