<?php
/*=========================================================
    ELIMINAR DISPONIBILIDAD
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
    VALIDAR MÉTODO
=========================================================*/

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    header("Location: index.php");
    exit();

}


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
    VERIFICAR PERFIL DEL PSICÓLOGO
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
    VERIFICAR QUE LA DISPONIBILIDAD PERTENEZCA
    AL PSICÓLOGO ACTUAL
=========================================================*/

$sql_disponibilidad = "

    SELECT
        id_disponibilidad,
        fecha,
        hora_inicio,
        hora_fin

    FROM disponibilidad_psicologos

    WHERE id_disponibilidad = ?
    AND id_psicologo = ?

    LIMIT 1

";


$stmt_disponibilidad =
    $conn->prepare(
        $sql_disponibilidad
    );


if (!$stmt_disponibilidad) {

    die(
        "Error al validar la disponibilidad."
    );

}


$stmt_disponibilidad->bind_param(
    "ii",
    $id_disponibilidad,
    $id_psicologo
);


$stmt_disponibilidad->execute();


$resultado_disponibilidad =
    $stmt_disponibilidad->get_result();


/*=========================================================
    VERIFICAR EXISTENCIA
=========================================================*/

if (
    $resultado_disponibilidad->num_rows === 0
) {

    $stmt_disponibilidad->close();

    header(
        "Location: index.php?error=no_encontrado"
    );

    exit();

}


$datos_disponibilidad =
    $resultado_disponibilidad->fetch_assoc();


$stmt_disponibilidad->close();


/*=========================================================
    VERIFICAR SI TIENE CITAS
=========================================================

    No eliminamos una disponibilidad si existen
    citas relacionadas.

=========================================================*/

$sql_citas = "

    SELECT
        COUNT(*) AS total

    FROM citas_psicologia

    WHERE id_disponibilidad = ?

";


$stmt_citas =
    $conn->prepare(
        $sql_citas
    );


if (!$stmt_citas) {

    die(
        "Error al verificar las citas."
    );

}


$stmt_citas->bind_param(
    "i",
    $id_disponibilidad
);


$stmt_citas->execute();


$resultado_citas =
    $stmt_citas->get_result();


$datos_citas =
    $resultado_citas->fetch_assoc();


$total_citas =
    (int) $datos_citas["total"];


$stmt_citas->close();


/*=========================================================
    NO ELIMINAR SI TIENE CITAS
=========================================================*/

if ($total_citas > 0) {

    header(
        "Location: index.php?error=tiene_citas"
    );

    exit();

}


/*=========================================================
    ELIMINAR DISPONIBILIDAD
=========================================================*/

$sql_eliminar = "

    DELETE FROM disponibilidad_psicologos

    WHERE id_disponibilidad = ?
    AND id_psicologo = ?

";


$stmt_eliminar =
    $conn->prepare(
        $sql_eliminar
    );


if (!$stmt_eliminar) {

    die(
        "Error al preparar eliminación."
    );

}


$stmt_eliminar->bind_param(
    "ii",
    $id_disponibilidad,
    $id_psicologo
);


/*=========================================================
    EJECUTAR
=========================================================*/

if ($stmt_eliminar->execute()) {

    $stmt_eliminar->close();

    header(
        "Location: index.php?success=eliminado"
    );

    exit();

}


/*=========================================================
    ERROR
=========================================================*/

$stmt_eliminar->close();


header(
    "Location: index.php?error=eliminar"
);

exit();

?>

