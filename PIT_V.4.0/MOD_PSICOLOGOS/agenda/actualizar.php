<?php
/*=========================================================
    ACTUALIZAR DISPONIBILIDAD
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
    VALIDAR MÉTODO POST
=========================================================*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit();

}


/*=========================================================
    OBTENER USUARIO DE LA SESIÓN
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
    RECIBIR ID
=========================================================*/

$id_disponibilidad =
    filter_input(
        INPUT_POST,
        "id_disponibilidad",
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
    RECIBIR DATOS
=========================================================*/

$fecha =
    trim(
        $_POST["fecha"] ?? ""
    );


$hora_inicio =
    trim(
        $_POST["hora_inicio"] ?? ""
    );


$hora_fin =
    trim(
        $_POST["hora_fin"] ?? ""
    );


/*=========================================================
    VALIDAR CAMPOS
=========================================================*/

if (
    empty($fecha) ||
    empty($hora_inicio) ||
    empty($hora_fin)
) {

    header(
        "Location: editar.php?id="
        . $id_disponibilidad
        . "&error=campos"
    );

    exit();

}


/*=========================================================
    VALIDAR FECHA
=========================================================*/

$fecha_obj =
    DateTime::createFromFormat(
        "Y-m-d",
        $fecha
    );


if (
    !$fecha_obj ||
    $fecha_obj->format("Y-m-d") !== $fecha
) {

    header(
        "Location: editar.php?id="
        . $id_disponibilidad
        . "&error=fecha"
    );

    exit();

}


/*=========================================================
    VALIDAR FECHA PASADA
=========================================================*/

$hoy =
    date("Y-m-d");


if ($fecha < $hoy) {

    header(
        "Location: editar.php?id="
        . $id_disponibilidad
        . "&error=fecha_pasada"
    );

    exit();

}


/*=========================================================
    VALIDAR HORAS
=========================================================*/

if ($hora_inicio >= $hora_fin) {

    header(
        "Location: editar.php?id="
        . $id_disponibilidad
        . "&error=horario"
    );

    exit();

}


/*=========================================================
    VALIDAR FORMATO DE HORAS
=========================================================*/

$inicio_obj =
    DateTime::createFromFormat(
        "H:i",
        $hora_inicio
    );


$fin_obj =
    DateTime::createFromFormat(
        "H:i",
        $hora_fin
    );


if (
    !$inicio_obj ||
    !$fin_obj
) {

    header(
        "Location: editar.php?id="
        . $id_disponibilidad
        . "&error=hora"
    );

    exit();

}


/*=========================================================
    VERIFICAR QUE LA DISPONIBILIDAD PERTENEZCA
    AL PSICÓLOGO ACTUAL
=========================================================*/

$sql_pertenencia = "

    SELECT
        id_disponibilidad

    FROM disponibilidad_psicologos

    WHERE id_disponibilidad = ?
    AND id_psicologo = ?

    LIMIT 1

";


$stmt_pertenencia =
    $conn->prepare(
        $sql_pertenencia
    );


if (!$stmt_pertenencia) {

    die(
        "Error al validar disponibilidad."
    );

}


$stmt_pertenencia->bind_param(
    "ii",
    $id_disponibilidad,
    $id_psicologo
);


$stmt_pertenencia->execute();


$resultado_pertenencia =
    $stmt_pertenencia->get_result();


if (
    $resultado_pertenencia->num_rows === 0
) {

    $stmt_pertenencia->close();

    header(
        "Location: index.php?error=no_encontrado"
    );

    exit();

}


$stmt_pertenencia->close();


/*=========================================================
    VERIFICAR HORARIOS SUPERPUESTOS
=========================================================

    IMPORTANTE:

    Se excluye el registro que estamos editando.

=========================================================*/

$sql_superpuesto = "

    SELECT
        id_disponibilidad

    FROM disponibilidad_psicologos

    WHERE id_psicologo = ?

    AND fecha = ?

    AND id_disponibilidad <> ?

    AND estado = 'DISPONIBLE'

    AND hora_inicio < ?
    AND hora_fin > ?

    LIMIT 1

";


$stmt_superpuesto =
    $conn->prepare(
        $sql_superpuesto
    );


if (!$stmt_superpuesto) {

    die(
        "Error al validar horario."
    );

}


$stmt_superpuesto->bind_param(
    "isiss",
    $id_psicologo,
    $fecha,
    $id_disponibilidad,
    $hora_fin,
    $hora_inicio
);


$stmt_superpuesto->execute();


$resultado_superpuesto =
    $stmt_superpuesto->get_result();


if (
    $resultado_superpuesto->num_rows > 0
) {

    $stmt_superpuesto->close();

    header(
        "Location: editar.php?id="
        . $id_disponibilidad
        . "&error=superpuesto"
    );

    exit();

}


$stmt_superpuesto->close();


/*=========================================================
    ACTUALIZAR
=========================================================*/

$sql_actualizar = "

    UPDATE disponibilidad_psicologos

    SET
        fecha = ?,
        hora_inicio = ?,
        hora_fin = ?

    WHERE id_disponibilidad = ?
    AND id_psicologo = ?

";


$stmt =
    $conn->prepare(
        $sql_actualizar
    );


if (!$stmt) {

    die(
        "Error al preparar actualización."
    );

}


$stmt->bind_param(
    "sssii",
    $fecha,
    $hora_inicio,
    $hora_fin,
    $id_disponibilidad,
    $id_psicologo
);


/*=========================================================
    EJECUTAR
=========================================================*/

if ($stmt->execute()) {

    $stmt->close();

    header(
        "Location: index.php?success=actualizado"
    );

    exit();

}


/*=========================================================
    ERROR DUPLICADO
=========================================================*/

if ($stmt->errno === 1062) {

    $stmt->close();

    header(
        "Location: editar.php?id="
        . $id_disponibilidad
        . "&error=superpuesto"
    );

    exit();

}


/*=========================================================
    ERROR GENERAL
=========================================================*/

$stmt->close();


header(
    "Location: editar.php?id="
    . $id_disponibilidad
    . "&error=actualizar"
);

exit();

?>
