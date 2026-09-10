<?php
/*=========================================================
    CAMBIAR ESTADO DE DISPONIBILIDAD
    SIST V.4.0 - PIT V.4.0
=========================================================*/


/*=========================================================
    SESIÓN
=========================================================*/

require_once("../../sesion.php");


/*=========================================================
    VALIDAR ROL
=========================================================*/

if (
    !isset($_SESSION["roles"]) ||
    !in_array("PSICOLOGO", $_SESSION["roles"])
) {

    header("Location: ../../indexloguin.php");
    exit();

}


/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../../base_pit/conect_pit.php");


/*=========================================================
    OBTENER ID DEL USUARIO
=========================================================*/

$id_usuario =
    (int) $_SESSION["id_usuario"];


/*=========================================================
    OBTENER ID DEL PSICÓLOGO
=========================================================*/

$sql_psicologo = "

    SELECT
        id_registro

    FROM administradores_psicologos

    WHERE id_usuario = ?
    AND activo = 1

    LIMIT 1

";


$stmt_psicologo =
    $conn->prepare($sql_psicologo);


if (!$stmt_psicologo) {

    die(
        "Error al preparar la consulta del psicólogo."
    );

}


$stmt_psicologo->bind_param(
    "i",
    $id_usuario
);


$stmt_psicologo->execute();


$resultado_psicologo =
    $stmt_psicologo->get_result();


/*=========================================================
    VERIFICAR PERFIL
=========================================================*/

if (
    $resultado_psicologo->num_rows === 0
) {

    $stmt_psicologo->close();

    header(
        "Location: index.php?error=no_psicologo"
    );

    exit();

}


$datos_psicologo =
    $resultado_psicologo->fetch_assoc();


$id_psicologo =
    (int) $datos_psicologo["id_registro"];


$stmt_psicologo->close();


/*=========================================================
    RECIBIR ID DE DISPONIBILIDAD
=========================================================*/

$id_disponibilidad =
    filter_input(
        INPUT_GET,
        "id",
        FILTER_VALIDATE_INT
    );


/*=========================================================
    VALIDAR ID
=========================================================*/

if (
    !$id_disponibilidad ||
    $id_disponibilidad <= 0
) {

    header(
        "Location: index.php?error=id"
    );

    exit();

}


/*=========================================================
    OBTENER ESTADO ACTUAL
=========================================================

    También comprobamos el id_psicologo para garantizar
    que el registro pertenece al psicólogo actual.

=========================================================*/

$sql = "

    SELECT
        estado

    FROM disponibilidad_psicologos

    WHERE id_disponibilidad = ?
    AND id_psicologo = ?

    LIMIT 1

";


$stmt =
    $conn->prepare($sql);


if (!$stmt) {

    die(
        "Error al consultar la disponibilidad."
    );

}


$stmt->bind_param(
    "ii",
    $id_disponibilidad,
    $id_psicologo
);


$stmt->execute();


$resultado =
    $stmt->get_result();


/*=========================================================
    VERIFICAR DISPONIBILIDAD
=========================================================*/

if (
    $resultado->num_rows === 0
) {

    $stmt->close();

    header(
        "Location: index.php?error=no_encontrado"
    );

    exit();

}


$datos =
    $resultado->fetch_assoc();


$estado_actual =
    $datos["estado"];


$stmt->close();


/*=========================================================
    DETERMINAR NUEVO ESTADO
=========================================================*/

if (
    $estado_actual === "DISPONIBLE"
) {

    $nuevo_estado =
        "CERRADO";

} else {

    $nuevo_estado =
        "DISPONIBLE";

}


/*=========================================================
    ACTUALIZAR ESTADO
=========================================================*/

$sql_actualizar = "

    UPDATE disponibilidad_psicologos

    SET
        estado = ?

    WHERE id_disponibilidad = ?
    AND id_psicologo = ?

";


$stmt_actualizar =
    $conn->prepare(
        $sql_actualizar
    );


if (!$stmt_actualizar) {

    die(
        "Error al preparar actualización."
    );

}


$stmt_actualizar->bind_param(
    "sii",
    $nuevo_estado,
    $id_disponibilidad,
    $id_psicologo
);


/*=========================================================
    EJECUTAR
=========================================================*/

if (
    $stmt_actualizar->execute()
) {

    $stmt_actualizar->close();

    header(
        "Location: index.php?success=estado"
    );

    exit();

}


/*=========================================================
    ERROR
=========================================================*/

$stmt_actualizar->close();


header(
    "Location: index.php?error=estado"
);

exit();

?>

