<?php

require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}

include("../../base_pit/conect_pit.php");

if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    mysqli_query($conn,"
        DELETE FROM docente_grupos
        WHERE id_asignacion = $id
    ");

}

header("Location: index.php");
exit();