<?php

require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}

include("../../base_pit/conect_pit.php");

$id_personal = $_POST['id_personal'];
$periodo     = trim($_POST['periodo']);

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