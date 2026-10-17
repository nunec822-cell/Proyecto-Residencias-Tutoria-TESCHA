<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

require_once("../../sesion.php");

requerir_rol('ADMIN');

include("../../base_pit/conect_pit.php");

$id_personal = (int)($_POST['id_personal']);
$periodo     = sist_esc(trim($_POST['periodo']));

if(isset($_POST['grupos'])){

    foreach($_POST['grupos'] as $grupo){

        $verificar = mysqli_query($conn,"
            SELECT id_asignacion
            FROM docente_grupos
            WHERE id_personal = '$id_personal'
            AND grupo = '$grupo'
            AND periodo = '$periodo'
        ");

        if(mysqli_num_rows($verificar) == 0){

            mysqli_query($conn,"
                INSERT INTO docente_grupos
                (
                    id_personal,
                    grupo,
                    periodo
                )
                VALUES
                (
                    '$id_personal',
                    '$grupo',
                    '$periodo'
                )
            ");

        }

    }

}

header("Location: index.php");
exit();