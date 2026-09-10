<?php

include("../../base_pit/conect_pit.php");

$carrera = $_GET['carrera'] ?? '';

if(empty($carrera)){

    echo '<option value="">Seleccione un docente</option>';
    exit();

}

$sql = mysqli_query($conn,"
    SELECT
        id_personal,
        CONCAT(
            apellido_p,' ',
            apellido_m,' ',
            nombre
        ) AS docente
    FROM personal_academico
    WHERE activo = 1
    AND carrera = '$carrera'
    ORDER BY apellido_p
");

echo '<option value="">Seleccione un docente</option>';

while($fila = mysqli_fetch_assoc($sql)){

    echo '<option value="'.$fila['id_personal'].'">';
    echo strtoupper($fila['docente']);
    echo '</option>';

}