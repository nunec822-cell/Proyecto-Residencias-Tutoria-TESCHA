
<?php

require_once("../../sesion.php");


/*=========================================
    VALIDAR ROL TUTOR
=========================================*/

if (
    !in_array(
        "TUTOR",
        $_SESSION["roles"]
    )
) {

    exit();

}


include("../../base_pit/conect_pit.php");


/*=========================================
    VALIDAR ID
=========================================*/

$id_canalizacion = intval(
    $_POST['id_canalizacion'] ?? 0
);


if (
    $id_canalizacion <= 0
) {

    echo "ERROR_ID";

    exit();

}


/*=========================================
    OBTENER TUTOR LOGUEADO
=========================================*/

$id_usuario =
$_SESSION['id_usuario'];


$sqlTutor = mysqli_query(
    $conn,
    "
    SELECT
        id_personal

    FROM personal_academico

    WHERE
        id_usuario = '$id_usuario'

    LIMIT 1
    "
);


/*=========================================
    VALIDAR CONSULTA TUTOR
=========================================*/

if (!$sqlTutor) {

    echo "ERROR_TUTOR:" .
        mysqli_error($conn);

    exit();

}


/*=========================================
    VALIDAR EXISTENCIA TUTOR
=========================================*/

if (
    mysqli_num_rows(
        $sqlTutor
    ) == 0
) {

    echo "TUTOR_NO_ENCONTRADO";

    exit();

}


$tutor =
mysqli_fetch_assoc(
    $sqlTutor
);


$id_tutor =
$tutor['id_personal'];


/*=========================================
    VERIFICAR CANALIZACIÓN
=========================================*/

$sql = mysqli_query(
    $conn,
    "
    SELECT

        estado,

        id_tutor,

        acciones

    FROM canalizaciones

    WHERE

        id_canalizacion =
        '$id_canalizacion'

    LIMIT 1
    "
);


/*=========================================
    VALIDAR CONSULTA
=========================================*/

if (!$sql) {

    echo "ERROR_CONSULTA:" .
        mysqli_error($conn);

    exit();

}


/*=========================================
    VERIFICAR EXISTENCIA
=========================================*/

if (
    mysqli_num_rows(
        $sql
    ) == 0
) {

    echo "NO_EXISTE";

    exit();

}


$datos =
mysqli_fetch_assoc(
    $sql
);


/*=========================================
    VERIFICAR QUE PERTENEZCA
    AL TUTOR
=========================================*/

if (
    intval(
        $datos['id_tutor']
    )
    !=
    intval(
        $id_tutor
    )
) {

    echo "NO_AUTORIZADO";

    exit();

}


/*=========================================
    VALIDAR ESTADO
=========================================*/

if (
    $datos['estado']
    !=
    'EN PROCESO'
) {

    echo "INVALIDO_ESTADO:" .
        $datos['estado'];

    exit();

}


/*=========================================
    VERIFICAR ACCIONES
=========================================*/

if (
    empty(
        trim(
            $datos['acciones']
            ??
            ''
        )
    )
) {

    echo "SIN_ACCIONES";

    exit();

}


/*=========================================
    FINALIZAR ATENCIÓN
=========================================*/

$actualizar = mysqli_query(
    $conn,
    "
    UPDATE canalizaciones

    SET

        estado = 'ATENDIDO',

        fecha_cierre = NOW()

    WHERE

        id_canalizacion =
        '$id_canalizacion'

        AND

        id_tutor =
        '$id_tutor'

        AND

        estado =
        'EN PROCESO'

    "
);


/*=========================================
    VERIFICAR ERROR SQL
=========================================*/

if (!$actualizar) {

    echo "ERROR_UPDATE:" .
        mysqli_error($conn);

    exit();

}


/*=========================================
    RESULTADO
=========================================*/

if (
    mysqli_affected_rows(
        $conn
    ) > 0
) {

    echo "OK";

}
else {

    echo "NO_ACTUALIZADO";

}

?>

