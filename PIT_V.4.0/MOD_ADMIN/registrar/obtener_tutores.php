<?php

include("../../base_pit/conect_pit.php");

$carrera = $_GET['carrera'] ?? '';

$sql = "
SELECT
    pa.id_usuario,
    CONCAT(
        pa.nombre,' ',
        pa.apellido_p,' ',
        pa.apellido_m
    ) AS tutor
FROM personal_academico pa
INNER JOIN usuario_tipo ut
    ON pa.id_usuario = ut.id_usuario
INNER JOIN tipos t
    ON ut.id_tipo = t.id_tipo
WHERE
    t.nombre = 'TUTOR'
    AND pa.carrera = ?
    AND pa.activo = 1
    AND ut.activo = 1
ORDER BY pa.nombre
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $carrera);
$stmt->execute();

$resultado = $stmt->get_result();

$tutores = [];

while($fila = $resultado->fetch_assoc()){
    $tutores[] = $fila;
}

header("Content-Type: application/json");
echo json_encode($tutores);