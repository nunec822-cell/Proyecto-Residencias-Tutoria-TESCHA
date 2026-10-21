<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

require_once("../../sesion.php");

requerir_rol('ADMIN');
sist_requerir_csrf_post();   /* el formulario debe traer el token de la sesión */

include("../../base_pit/conect_pit.php");

$id_personal = (int) ($_POST['id_personal'] ?? 0);
$periodo     = trim((string) ($_POST['periodo'] ?? ''));
$grupos      = is_array($_POST['grupos'] ?? null) ? $_POST['grupos'] : [];

if ($id_personal > 0 && $periodo !== '' && mb_strlen($periodo) <= 20) {

    /* Consultas preparadas: el grupo y el periodo nunca se pegan al SQL */
    $insertar = sist_db_preparar(
        $conn,
        "INSERT INTO docente_grupos (id_personal, grupo, periodo)
         SELECT ?, ?, ?
         FROM DUAL
         WHERE NOT EXISTS (
             SELECT 1 FROM docente_grupos WHERE id_personal = ? AND grupo = ? AND periodo = ?
         )"
    );

    foreach ($grupos as $grupo) {
        $grupo = trim((string) $grupo);
        if ($grupo === '' || mb_strlen($grupo) > 20) {
            continue;
        }
        $insertar->bind_param('ississ', $id_personal, $grupo, $periodo, $id_personal, $grupo, $periodo);
        sist_db_ejecutar($insertar, 'asignar grupo');
    }

    $insertar->close();
}

header("Location: index.php");
exit();
