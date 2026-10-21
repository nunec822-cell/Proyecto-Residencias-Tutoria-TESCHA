<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

requerir_permiso('grupos.asignar');

include("../../base_pit/conect_pit.php");

$carrera = trim((string) ($_GET['carrera'] ?? ''));

if ($carrera === '') {
    exit();
}

$grupos = sist_db_filas(
    $conn,
    "SELECT DISTINCT grupo FROM tutorados WHERE carrera = ? AND activo = 1 ORDER BY grupo",
    's',
    [$carrera]
);

foreach ($grupos as $fila) {
?>

<label class="grupo-item">

    <input
        type="checkbox"
        name="grupos[]"
        value="<?= e($fila['grupo']) ?>"
    >

    <span>
        <?= e($fila['grupo']) ?>
    </span>

</label>

<?php
}
