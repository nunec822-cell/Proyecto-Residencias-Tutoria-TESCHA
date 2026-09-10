<?php

require_once("../../sesion.php");

if (!in_array("DOCENTE", $_SESSION["roles"])) {
    exit();
}

include("../../base_pit/conect_pit.php");
header('Content-Type: application/json');
/*=========================================
DOCENTE
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

$docente = mysqli_query($conn,"
    SELECT id_personal
    FROM personal_academico
    WHERE id_usuario='$id_usuario'
");

$datos = mysqli_fetch_assoc($docente);

$id_docente = $datos['id_personal'];

/*=========================================
DATOS
=========================================*/

$id_tutorado = $_POST['id_tutorado'];
$id_materia  = $_POST['id_materia'];
$grupo       = $_POST['grupo'];
$unidad      = $_POST['unidad'];

$competencia_no_alcanzada =
isset($_POST['competencia_no_alcanzada']) ? 1 : 0;

$inasistencias =
isset($_POST['inasistencias']) ? 1 : 0;

$indisciplina =
isset($_POST['indisciplina']) ? 1 : 0;

$no_entrega_trabajos =
isset($_POST['no_entrega_trabajos']) ? 1 : 0;

$apoyo_psicologico =
isset($_POST['apoyo_psicologico']) ? 1 : 0;

$apoyo_economico =
isset($_POST['apoyo_economico']) ? 1 : 0;

$otro = mysqli_real_escape_string(
    $conn,
    $_POST['otro']
);

$observaciones = mysqli_real_escape_string(
    $conn,
    $_POST['observaciones']
);

/*=========================================
INSERTAR
=========================================*/

mysqli_query($conn,"
INSERT INTO anexo14_reportes(

id_docente,
id_tutorado,
id_materia,
grupo,
unidad,

competencia_no_alcanzada,
inasistencias,
indisciplina,
no_entrega_trabajos,
apoyo_psicologico,
apoyo_economico,

otro,
observaciones

)

VALUES(

'$id_docente',
'$id_tutorado',
'$id_materia',
'$grupo',
'$unidad',

'$competencia_no_alcanzada',
'$inasistencias',
'$indisciplina',
'$no_entrega_trabajos',
'$apoyo_psicologico',
'$apoyo_economico',

'$otro',
'$observaciones'

)
");

/*=========================================
SI EL ALUMNO TIENE UNA CANALIZACIÓN
ACTIVA, SUMAR NUEVOS REPORTES
=========================================*/

mysqli_query(
    $conn,
    "
    UPDATE canalizaciones
    SET nuevos_reportes =
        nuevos_reportes + 1
    WHERE id_tutorado='$id_tutorado'
    AND estado IN(
        'PENDIENTE',
        'EN PROCESO'
    )
    "
);

echo json_encode([
    "success" => true,
    "mensaje" => "Reporte guardado correctamente."
]);