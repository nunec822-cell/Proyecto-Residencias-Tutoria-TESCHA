<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

require_once("../../sesion.php");

requerir_rol('DOCENTE');
sist_requerir_csrf_post();   /* el formulario debe traer el token de la sesión */

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

$id_tutorado = (int)($_POST['id_tutorado'] ?? 0);
$id_materia  = (int)($_POST['id_materia'] ?? 0);
$grupo       = sist_esc($_POST['grupo'] ?? '');
$unidad      = sist_esc($_POST['unidad'] ?? '');

/* Solo alumnos de un grupo que imparte este docente */
if (!sist_docente_tiene_alumno($conn, (int) $id_usuario, $id_tutorado, (string) ($_POST['grupo'] ?? ''))) {
    http_response_code(403);
    echo json_encode(['success' => false, 'mensaje' => 'No tienes acceso a ese alumno o grupo.']);
    exit();
}

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