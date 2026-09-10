<?php
include("../../../base_pit/conect_pit.php");

header('Content-Type: application/json');

/*================================================
    FUNCIÓN PRINCIPAL REUTILIZABLE
================================================*/
function obtenerUsuarios($conn, $tipo)
{
    $sql = "SELECT
                u.id_usuario,
                u.usuario,
                t.nombre AS tipo,

                pa.no_empleado,
                pa.nombre,
                pa.apellido_p,
                pa.apellido_m,
                pa.carrera

            FROM usuarios u

            INNER JOIN usuario_tipo ut
                ON u.id_usuario = ut.id_usuario

            INNER JOIN tipos t
                ON ut.id_tipo = t.id_tipo

            LEFT JOIN personal_academico pa
                ON u.id_usuario = pa.id_usuario

            WHERE t.nombre = '$tipo'
              AND ut.activo = 1
              AND u.activo = 1";

    return $conn->query($sql);
}

/*================================================
    TIPO DE CONSULTA RECIBIDA
================================================*/
$tipo = $_GET['tipo'] ?? '';
$modo = $_GET['modo'] ?? 'general';

if ($tipo === 'DOCENTE' && $modo === 'carrera') {

    $result = obtenerDocentesPorCarrera($conn);

    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode($data);
    exit;
}

$result = obtenerUsuarios($conn, $tipo);

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);

function obtenerDocentesPorCarrera($conn)
{
    $sql = "SELECT
                pa.carrera,
                pa.no_empleado,
                pa.nombre,
                pa.apellido_p,
                pa.apellido_m
            FROM usuarios u
            INNER JOIN usuario_tipo ut
                ON u.id_usuario = ut.id_usuario
            INNER JOIN tipos t
                ON ut.id_tipo = t.id_tipo
            INNER JOIN personal_academico pa
                ON u.id_usuario = pa.id_usuario
            WHERE t.nombre = 'DOCENTE'
            ORDER BY pa.carrera, pa.nombre";

    return $conn->query($sql);
}