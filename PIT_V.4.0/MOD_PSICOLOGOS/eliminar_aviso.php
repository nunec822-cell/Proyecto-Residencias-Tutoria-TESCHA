<?php

require_once("../sesion.php");

if (!in_array("PSICOLOGO", $_SESSION["roles"])) {

    header("Location: ../indexloguin.php");
    exit();

}

require_once("../base_pit/conect_pit.php");


/*=========================================================
    VALIDAR ID
=========================================================*/

$id_aviso = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


if ($id_aviso <= 0) {

    header("Location: inicio.php");
    exit();

}


/*=========================================================
    OBTENER PSICÓLOGO
=========================================================*/

$id_usuario = $_SESSION["id_usuario"];

$sqlPsicologo = "
    SELECT id_registro
    FROM administradores_psicologos
    WHERE id_usuario = ?
    LIMIT 1
";

$stmt = $conn->prepare($sqlPsicologo);

$stmt->bind_param(
    "i",
    $id_usuario
);

$stmt->execute();

$resultado = $stmt->get_result();

$psicologo = $resultado->fetch_assoc();


if (!$psicologo) {

    header("Location: inicio.php");
    exit();

}


$id_psicologo = $psicologo["id_registro"];


/*=========================================================
    OBTENER ARCHIVOS DEL AVISO
=========================================================*/

$sqlAviso = "
    SELECT imagen, pdf
    FROM avisos_psicologia
    WHERE id_aviso = ?
    AND id_psicologo = ?
    LIMIT 1
";

$stmt = $conn->prepare($sqlAviso);

$stmt->bind_param(
    "ii",
    $id_aviso,
    $id_psicologo
);

$stmt->execute();

$resultado = $stmt->get_result();

$aviso = $resultado->fetch_assoc();


if (!$aviso) {

    header("Location: inicio.php");
    exit();

}


/*=========================================================
    ELIMINAR IMAGEN
=========================================================*/

if (!empty($aviso["imagen"])) {

    $rutaImagen =
        __DIR__ .
        "/uploads/imagenes/" .
        $aviso["imagen"];

    if (file_exists($rutaImagen)) {

        unlink($rutaImagen);

    }

}


/*=========================================================
    ELIMINAR PDF
=========================================================*/

if (!empty($aviso["pdf"])) {

    $rutaPDF =
        __DIR__ .
        "/uploads/imagenes/pdf/" .
        $aviso["pdf"];

    if (file_exists($rutaPDF)) {

        unlink($rutaPDF);

    }

}


/*=========================================================
    ELIMINAR REGISTRO
=========================================================*/

$sqlEliminar = "
    DELETE FROM avisos_psicologia
    WHERE id_aviso = ?
    AND id_psicologo = ?
";

$stmt = $conn->prepare($sqlEliminar);

$stmt->bind_param(
    "ii",
    $id_aviso,
    $id_psicologo
);

$stmt->execute();


/*=========================================================
    REGRESAR
=========================================================*/

header("Location: inicio.php");

exit();

?>