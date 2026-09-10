<?php

require_once("../sesion.php");

if (!in_array("DOCENTE", $_SESSION["roles"])) {
    header("Location: ../indexloguin.php");
    exit();
}

if (!in_array("TUTOR", $_SESSION["roles"])) {
    header("Location: iniciodocente.php");
    exit();
}

require_once("../base_pit/conect_pit.php");

$id_usuario = $_SESSION["id_usuario"];

/*=========================================================
    OBTENER PERSONAL ACADÉMICO
=========================================================*/

$sql_personal = "
    SELECT
        id_personal,
        fotografia
    FROM personal_academico
    WHERE id_usuario = ?
    AND activo = 1
    LIMIT 1
";

$stmt = $conn->prepare($sql_personal);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: iniciodocente.php?foto=error_personal");
    exit();
}

$personal = $resultado->fetch_assoc();

$id_personal = $personal["id_personal"];
$fotografia_anterior = $personal["fotografia"];


/*=========================================================
    VALIDAR ARCHIVO
=========================================================*/

if (!isset($_FILES["fotografia"])) {
    header("Location: iniciodocente.php?foto=sin_archivo");
    exit();
}

$archivo = $_FILES["fotografia"];


/*=========================================================
    VALIDAR ERROR DE SUBIDA
=========================================================*/

if ($archivo["error"] !== UPLOAD_ERR_OK) {
    header("Location: iniciodocente.php?foto=error_subida");
    exit();
}


/*=========================================================
    TAMAÑO MÁXIMO
    5 MB
=========================================================*/

$maximo = 5 * 1024 * 1024;

if ($archivo["size"] > $maximo) {
    header("Location: iniciodocente.php?foto=demasiado_grande");
    exit();
}


/*=========================================================
    VALIDAR QUE REALMENTE SEA UNA IMAGEN
=========================================================*/

$informacion_imagen = getimagesize($archivo["tmp_name"]);

if ($informacion_imagen === false) {
    header("Location: iniciodocente.php?foto=archivo_invalido");
    exit();
}


/*=========================================================
    TIPOS PERMITIDOS
=========================================================*/

$tipos_permitidos = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp"
];

$mime = $informacion_imagen["mime"];

if (!isset($tipos_permitidos[$mime])) {
    header("Location: iniciodocente.php?foto=formato_invalido");
    exit();
}

$extension = $tipos_permitidos[$mime];


/*=========================================================
    VALIDAR DIMENSIONES
=========================================================*/

$ancho = $informacion_imagen[0];
$alto  = $informacion_imagen[1];

/*
    Evitamos imágenes demasiado pequeñas.
*/

if ($ancho < 300 || $alto < 300) {
    header("Location: iniciodocente.php?foto=dimensiones");
    exit();
}


/*=========================================================
    CARPETA
=========================================================*/

$carpeta = __DIR__ . "/../uploads/tutores/";


if (!is_dir($carpeta)) {

    if (!mkdir($carpeta, 0755, true)) {
        header("Location: iniciodocente.php?foto=error_carpeta");
        exit();
    }

}


/*=========================================================
    NOMBRE SEGURO
=========================================================*/

$nombre_archivo = "tutor_" . $id_personal . "_" . bin2hex(random_bytes(8)) . "." . $extension;

$ruta_destino = $carpeta . $nombre_archivo;


/*=========================================================
    MOVER ARCHIVO
=========================================================*/

if (!move_uploaded_file($archivo["tmp_name"], $ruta_destino)) {
    header("Location: iniciodocente.php?foto=error_guardar");
    exit();
}


/*=========================================================
    ELIMINAR FOTOGRAFÍA ANTERIOR
=========================================================*/

if (!empty($fotografia_anterior)) {

    $ruta_anterior = $carpeta . basename($fotografia_anterior);

    if (file_exists($ruta_anterior)) {
        unlink($ruta_anterior);
    }
}


/*=========================================================
    GUARDAR EN BASE DE DATOS
=========================================================*/

$sql_update = "
    UPDATE personal_academico
    SET fotografia = ?
    WHERE id_personal = ?
";

$stmt_update = $conn->prepare($sql_update);

$stmt_update->bind_param(
    "si",
    $nombre_archivo,
    $id_personal
);

if (!$stmt_update->execute()) {

    /*
        Si falla la BD eliminamos el archivo
        que acabamos de subir.
    */

    if (file_exists($ruta_destino)) {
        unlink($ruta_destino);
    }

    header("Location: iniciodocente.php?foto=error_bd");
    exit();
}


/*=========================================================
    REGRESAR
=========================================================*/

header("Location: iniciodocente.php?foto=success");
exit();

?>