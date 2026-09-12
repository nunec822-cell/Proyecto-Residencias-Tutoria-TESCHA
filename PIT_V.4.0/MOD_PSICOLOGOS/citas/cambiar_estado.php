<?php

/*=========================================================
    CAMBIAR ESTADO DE CITA
    SIST V.4.0 - PIT V4.0
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

    header(
        "Location: ../../indexloguin.php"
    );

    exit();

}


/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../../base_pit/conect_pit.php");


/*=========================================================
    OBTENER USUARIO
=========================================================*/

$id_usuario =

    isset($_SESSION["id_usuario"])
        ? (int) $_SESSION["id_usuario"]
        : 0;


if ($id_usuario <= 0) {

    header(
        "Location: index.php?error=sesion"
    );

    exit();

}


/*=========================================================
    OBTENER PSICÓLOGO
=========================================================*/

$sqlPsicologo = "

    SELECT

        id_registro

    FROM administradores_psicologos

    WHERE id_usuario = ?

      AND activo = 1

    LIMIT 1

";


$stmtPsicologo =

    $conn->prepare(
        $sqlPsicologo
    );


if (!$stmtPsicologo) {

    header(
        "Location: index.php?error=psicologo"
    );

    exit();

}


$stmtPsicologo->bind_param(
    "i",
    $id_usuario
);


$stmtPsicologo->execute();


$resultadoPsicologo =

    $stmtPsicologo->get_result();


$psicologo =

    $resultadoPsicologo->fetch_assoc();


$stmtPsicologo->close();


/*=========================================================
    VALIDAR PSICÓLOGO
=========================================================*/

if (!$psicologo) {

    header(
        "Location: index.php?error=psicologo"
    );

    exit();

}


$id_psicologo =

    (int) $psicologo["id_registro"];


/*=========================================================
    RECIBIR ID DE CITA
=========================================================*/

$id_cita =

    isset($_GET["id"])
        ? (int) $_GET["id"]
        : 0;


if ($id_cita <= 0) {

    header(
        "Location: index.php?error=id_cita"
    );

    exit();

}


/*=========================================================
    RECIBIR ACCIÓN
=========================================================*/

$accion =

    isset($_GET["accion"])
        ? trim($_GET["accion"])
        : "";


/*=========================================================
    ACCIONES PERMITIDAS
=========================================================*/

$accionesPermitidas = [

    "confirmar",
    "cancelar",
    "atendiendo",
    "atendida",
    "no_asistio"

];


if (
    !in_array(
        $accion,
        $accionesPermitidas,
        true
    )
) {

    header(
        "Location: index.php?error=accion"
    );

    exit();

}


/*=========================================================
    OBTENER CITA
=========================================================

    Se comprueba que la cita pertenece al psicólogo
    que inició sesión.

=========================================================*/

$sqlCita = "

    SELECT

        c.id_cita,
        c.estado,
        c.id_disponibilidad

    FROM citas_psicologia c

    INNER JOIN disponibilidad_psicologos d

        ON d.id_disponibilidad =
           c.id_disponibilidad

    WHERE c.id_cita = ?

      AND d.id_psicologo = ?

    LIMIT 1

";


$stmtCita =

    $conn->prepare(
        $sqlCita
    );


if (!$stmtCita) {

    header(
        "Location: index.php?error=consulta"
    );

    exit();

}


$stmtCita->bind_param(
    "ii",
    $id_cita,
    $id_psicologo
);


$stmtCita->execute();


$resultadoCita =

    $stmtCita->get_result();


$cita =

    $resultadoCita->fetch_assoc();


$stmtCita->close();


/*=========================================================
    VALIDAR CITA
=========================================================*/

if (!$cita) {

    header(
        "Location: index.php?error=cita_no_encontrada"
    );

    exit();

}


$estadoActual =

    $cita["estado"];


/*=========================================================
    DETERMINAR NUEVO ESTADO
=========================================================*/

$nuevoEstado = "";


/*=========================================================
    CONFIRMAR
=========================================================*/

if ($accion === "confirmar") {

    if (
        $estadoActual !== "PENDIENTE"
    ) {

        header(
            "Location: index.php?error=estado_no_valido"
        );

        exit();

    }


    $nuevoEstado =
        "CONFIRMADA";

}


/*=========================================================
    CANCELAR
=========================================================*/

elseif ($accion === "cancelar") {

    if (
        $estadoActual !== "PENDIENTE"
        &&
        $estadoActual !== "CONFIRMADA"
    ) {

        header(
            "Location: index.php?error=estado_no_valido"
        );

        exit();

    }


    $nuevoEstado =
        "CANCELADA";

}


/*=========================================================
    INICIAR ATENCIÓN
=========================================================*/

elseif ($accion === "atendiendo") {

    if (
        $estadoActual !== "CONFIRMADA"
    ) {

        header(
            "Location: index.php?error=estado_no_valido"
        );

        exit();

    }


    $nuevoEstado =
        "ATENDIENDO";

}


/*=========================================================
    FINALIZAR ATENCIÓN
=========================================================*/

elseif ($accion === "atendida") {

    if (
        $estadoActual !== "ATENDIENDO"
    ) {

        header(
            "Location: index.php?error=estado_no_valido"
        );

        exit();

    }


    $nuevoEstado =
        "ATENDIDA";

}


/*=========================================================
    NO ASISTIÓ
=========================================================*/

elseif ($accion === "no_asistio") {

    if (
        $estadoActual !== "CONFIRMADA"
    ) {

        header(
            "Location: index.php?error=estado_no_valido"
        );

        exit();

    }


    $nuevoEstado =
        "NO_ASISTIO";

}


/*=========================================================
    VALIDAR NUEVO ESTADO
=========================================================*/

if (empty($nuevoEstado)) {

    header(
        "Location: index.php?error=estado"
    );

    exit();

}


/*=========================================================
    ACTUALIZAR CITA
=========================================================

    Se vuelve a comprobar el psicólogo en el UPDATE.

=========================================================*/

$sqlActualizar = "

    UPDATE citas_psicologia c

    INNER JOIN disponibilidad_psicologos d

        ON d.id_disponibilidad =
           c.id_disponibilidad

    SET

        c.estado = ?

    WHERE c.id_cita = ?

      AND d.id_psicologo = ?

";


$stmtActualizar =

    $conn->prepare(
        $sqlActualizar
    );


if (!$stmtActualizar) {

    header(
        "Location: index.php?error=actualizar"
    );

    exit();

}


$stmtActualizar->bind_param(
    "sii",
    $nuevoEstado,
    $id_cita,
    $id_psicologo
);


/*=========================================================
    EJECUTAR
=========================================================*/

$actualizado =

    $stmtActualizar->execute();


$filasAfectadas =

    $stmtActualizar->affected_rows;


$stmtActualizar->close();


/*=========================================================
    VALIDAR EJECUCIÓN
=========================================================*/

if (!$actualizado) {

    header(
        "Location: index.php?error=actualizar"
    );

    exit();

}


/*=========================================================
    VALIDAR CAMBIO
=========================================================*/

if ($filasAfectadas <= 0) {

    header(
        "Location: index.php?error=sin_cambios"
    );

    exit();

}


/*=========================================================
    REDIRECCIÓN EXITOSA
=========================================================

    Enviamos:

        success
        accion
        cita

    index.php utilizará estos datos para mostrar
    el modal correspondiente.

=========================================================*/

header(

    "Location: index.php"
    . "?success=estado_cita"
    . "&accion="
    . urlencode($accion)
    . "&cita="
    . $id_cita

);

exit();

?>