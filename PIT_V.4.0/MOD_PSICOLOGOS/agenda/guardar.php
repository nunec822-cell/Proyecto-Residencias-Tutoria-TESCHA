<?php
/*=========================================================
    GUARDAR DISPONIBILIDAD
    SIST V.4.0 - PIT V.4.0
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

    SELECT id_registro

    FROM administradores_psicologos

    WHERE id_usuario = ?
    AND activo = 1

    LIMIT 1

";


$stmt_psicologo =
    $conn->prepare($sql_psicologo);


if (!$stmt_psicologo) {

    die(
        "Error al preparar consulta del psicólogo."
    );

}


$stmt_psicologo->bind_param(
    "i",
    $id_usuario
);


$stmt_psicologo->execute();


$resultado_psicologo =
    $stmt_psicologo->get_result();


if (
    $resultado_psicologo->num_rows === 0
) {

    $stmt_psicologo->close();

    header(
        "Location: index.php?error=no_psicologo"
    );

    exit();

}


$psicologo =
    $resultado_psicologo->fetch_assoc();


$id_psicologo =
    (int) $psicologo["id_registro"];


$stmt_psicologo->close();


/*=========================================================
    RECIBIR DATOS
=========================================================*/

$fecha =
    trim($_POST["fecha"] ?? "");


$hora_inicio =
    trim($_POST["hora_inicio"] ?? "");


$hora_fin =
    trim($_POST["hora_fin"] ?? "");


/*=========================================================
    VALIDAR CAMPOS
=========================================================*/

if (
    empty($fecha) ||
    empty($hora_inicio) ||
    empty($hora_fin)
) {

    header(
        "Location: index.php?error=campos"
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
        "Location: index.php?error=fecha"
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
        "Location: index.php?error=fecha_pasada"
    );

    exit();

}


/*=========================================================
    VALIDAR HORARIOS
=========================================================*/

if ($hora_inicio >= $hora_fin) {

    header(
        "Location: index.php?error=horario"
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
        "Location: index.php?error=hora"
    );

    exit();

}


/*=========================================================
    VERIFICAR HORARIOS SUPERPUESTOS
=========================================================*/

$sql_superpuesto = "

    SELECT id_disponibilidad

    FROM disponibilidad_psicologos

    WHERE id_psicologo = ?

    AND fecha = ?

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
        "Error al preparar validación de horario."
    );

}


$stmt_superpuesto->bind_param(
    "isss",
    $id_psicologo,
    $fecha,
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
        "Location: index.php?error=superpuesto"
    );

    exit();

}


$stmt_superpuesto->close();


/*=========================================================
    INSERTAR DISPONIBILIDAD
=========================================================

    IMPORTANTE:

    El psicólogo NO introduce cupos.

    El sistema utiliza los valores iniciales
    establecidos por la base de datos.

=========================================================*/

$sql_insertar = "

    INSERT INTO disponibilidad_psicologos
    (
        id_psicologo,
        fecha,
        hora_inicio,
        hora_fin,
        estado
    )

    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        'DISPONIBLE'
    )

";


$stmt =
    $conn->prepare($sql_insertar);


if (!$stmt) {

    die(
        "Error al preparar registro."
    );

}


$stmt->bind_param(
    "isss",
    $id_psicologo,
    $fecha,
    $hora_inicio,
    $hora_fin
);


/*=========================================================
    EJECUTAR
=========================================================*/

if ($stmt->execute()) {

    $stmt->close();

    header(
        "Location: index.php?success=registrado"
    );

    exit();

}


/*=========================================================
    ERROR
=========================================================*/

$error =
    $stmt->errno;


$stmt->close();


/*=========================================================
    ERROR DE HORARIO DUPLICADO
=========================================================*/

if ($error === 1062) {

    header(
        "Location: index.php?error=superpuesto"
    );

    exit();

}


/*=========================================================
    ERROR GENERAL
=========================================================*/

header(
    "Location: index.php?error=guardar"
);

exit();

?>
