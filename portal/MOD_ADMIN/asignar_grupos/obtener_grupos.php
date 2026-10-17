<?php
require_once __DIR__ . '/../../../core/bootstrap.php';
requerir_permiso('grupos.asignar');

include("../../base_pit/conect_pit.php");

$carrera = sist_esc($_GET['carrera'] ?? '');

if(empty($carrera)){
    exit();
}

$sql = mysqli_query($conn,"
    SELECT DISTINCT grupo
    FROM tutorados
    WHERE carrera = '$carrera'
    AND activo = 1
    ORDER BY grupo
");

while($fila = mysqli_fetch_assoc($sql)){

?>

<label class="grupo-item">

    <input
        type="checkbox"
        name="grupos[]"
        value="<?= $fila['grupo']; ?>"
    >

    <span>
        <?= $fila['grupo']; ?>
    </span>

</label>

<?php

}