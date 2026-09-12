<?php

/*=========================================================
    SESIÓN
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*=========================================================
    RESPUESTA JSON
=========================================================*/

header('Content-Type: application/json; charset=utf-8');


/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../../base_pit/conect_pit.php");


/*=========================================================
    FUNCIÓN DE RESPUESTA
=========================================================*/

function respuesta($success, $message, $data = [])
{
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


/*=========================================================
    SOLO POST
=========================================================*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    respuesta(
        false,
        'Método de solicitud no permitido.'
    );

}


/*=========================================================
    DATOS RECIBIDOS
=========================================================*/

$csrf_token = $_POST['csrf_token'] ?? '';

$id_disponibilidad =
    filter_input(
        INPUT_POST,
        'id_disponibilidad',
        FILTER_VALIDATE_INT
    );

$usuario =
    trim($_POST['usuario'] ?? '');

$password =
    $_POST['password'] ?? '';

$motivo =
    trim($_POST['motivo'] ?? '');


/*=========================================================
    VALIDAR TOKEN
=========================================================*/

if (
    empty($_SESSION['csrf_solicitud_cita']) ||
    empty($csrf_token) ||
    !hash_equals(
        $_SESSION['csrf_solicitud_cita'],
        $csrf_token
    )
) {

    respuesta(
        false,
        'La sesión de seguridad ha expirado. Actualiza la página e inténtalo nuevamente.'
    );

}


/*=========================================================
    VALIDACIONES BÁSICAS
=========================================================*/

if (!$id_disponibilidad) {

    respuesta(
        false,
        'No se recibió correctamente el espacio seleccionado.'
    );

}


if ($usuario === '') {

    respuesta(
        false,
        'Debes ingresar tu usuario del PIT.'
    );

}


if ($password === '') {

    respuesta(
        false,
        'Debes ingresar tu contraseña del PIT.'
    );

}


if ($motivo === '') {

    respuesta(
        false,
        'Debes indicar el motivo de tu solicitud.'
    );

}


if (mb_strlen($motivo) > 1000) {

    respuesta(
        false,
        'El motivo de la solicitud no puede superar los 1000 caracteres.'
    );

}


/*=========================================================
    BUSCAR USUARIO
=========================================================*/

$sqlUsuario = "
    SELECT
        u.id_usuario,
        u.usuario,
        u.password,
        u.activo,
        t.id_tutorado,
        t.nombre,
        t.apellido_p,
        t.apellido_m,
        t.matricula,
        t.activo AS tutorado_activo
    FROM usuarios u
    INNER JOIN tutorados t
        ON t.id_usuario = u.id_usuario
    WHERE u.usuario = ?
    LIMIT 1
";

$stmtUsuario = $conn->prepare($sqlUsuario);

if (!$stmtUsuario) {

    respuesta(
        false,
        'No fue posible validar tus datos en este momento.'
    );

}

$stmtUsuario->bind_param(
    "s",
    $usuario
);

$stmtUsuario->execute();

$resultadoUsuario =
    $stmtUsuario->get_result();

$datosUsuario =
    $resultadoUsuario->fetch_assoc();

$stmtUsuario->close();


/*=========================================================
    USUARIO NO ENCONTRADO
=========================================================*/

if (!$datosUsuario) {

    respuesta(
        false,
        'El usuario o la contraseña son incorrectos.'
    );

}


/*=========================================================
    USUARIO ACTIVO
=========================================================*/

if ((int)$datosUsuario['activo'] !== 1) {

    respuesta(
        false,
        'Tu cuenta del PIT se encuentra inactiva.'
    );

}


/*=========================================================
    TUTORADO ACTIVO
=========================================================*/

if ((int)$datosUsuario['tutorado_activo'] !== 1) {

    respuesta(
        false,
        'Tu registro como tutorado se encuentra inactivo.'
    );

}


/*=========================================================
    VERIFICAR CONTRASEÑA
=========================================================*/

if (
    !password_verify(
        $password,
        $datosUsuario['password']
    )
) {

    respuesta(
        false,
        'El usuario o la contraseña son incorrectos.'
    );

}


/*=========================================================
    OBTENER ID TUTORADO
=========================================================*/

$id_tutorado =
    (int)$datosUsuario['id_tutorado'];


/*=========================================================
    INICIAR TRANSACCIÓN
=========================================================*/

$conn->begin_transaction();


try {

    /*=====================================================
        BLOQUEAR DISPONIBILIDAD
    =====================================================*/

    $sqlDisponibilidad = "
        SELECT
            id_disponibilidad,
            id_psicologo,
            fecha,
            hora_inicio,
            hora_fin,
            cupos,
            cupos_disponibles,
            estado
        FROM disponibilidad_psicologos
        WHERE id_disponibilidad = ?
        FOR UPDATE
    ";

    $stmtDisponibilidad =
        $conn->prepare(
            $sqlDisponibilidad
        );

    if (!$stmtDisponibilidad) {

        throw new Exception(
            'No fue posible verificar la disponibilidad.'
        );

    }

    $stmtDisponibilidad->bind_param(
        "i",
        $id_disponibilidad
    );

    $stmtDisponibilidad->execute();

    $resultadoDisponibilidad =
        $stmtDisponibilidad->get_result();

    $disponibilidad =
        $resultadoDisponibilidad->fetch_assoc();

    $stmtDisponibilidad->close();


    /*=====================================================
        DISPONIBILIDAD NO EXISTE
    =====================================================*/

    if (!$disponibilidad) {

        throw new Exception(
            'El espacio seleccionado ya no existe.'
        );

    }


    /*=====================================================
        VALIDAR ESTADO
    =====================================================*/

    if (
        $disponibilidad['estado'] !== 'DISPONIBLE'
    ) {

        throw new Exception(
            'Este espacio ya no se encuentra disponible.'
        );

    }


    /*=====================================================
        VALIDAR CUPO
    =====================================================*/

    if (
        (int)$disponibilidad['cupos_disponibles'] <= 0
    ) {

        throw new Exception(
            'Este espacio ya fue ocupado por otro estudiante.'
        );

    }


    /*=====================================================
        VALIDAR FECHA Y HORA
    =====================================================*/

    $fechaHoraInicio = strtotime(
        $disponibilidad['fecha'] .
        ' ' .
        $disponibilidad['hora_inicio']
    );

    if (
        $fechaHoraInicio === false ||
        $fechaHoraInicio <= time()
    ) {

        throw new Exception(
            'Este horario ya no se encuentra disponible.'
        );

    }


    /*=====================================================
        COMPROBAR CITA ACTIVA DEL TUTORADO
    =====================================================*/

    $sqlCitaActiva = "
        SELECT
            c.id_cita
        FROM citas_psicologia c
        WHERE c.id_tutorado = ?
          AND c.estado IN (
              'PENDIENTE',
              'CONFIRMADA',
              'ATENDIENDO'
          )
        LIMIT 1
        FOR UPDATE
    ";

    $stmtCitaActiva =
        $conn->prepare(
            $sqlCitaActiva
        );

    if (!$stmtCitaActiva) {

        throw new Exception(
            'No fue posible comprobar tus citas actuales.'
        );

    }

    $stmtCitaActiva->bind_param(
        "i",
        $id_tutorado
    );

    $stmtCitaActiva->execute();

    $resultadoCitaActiva =
        $stmtCitaActiva->get_result();

    $citaActiva =
        $resultadoCitaActiva->fetch_assoc();

    $stmtCitaActiva->close();


    /*=====================================================
        YA TIENE CITA ACTIVA
    =====================================================*/

    if ($citaActiva) {

        throw new Exception(
            'Ya tienes una solicitud de atención psicológica pendiente o activa. No puedes reservar otro espacio hasta que esta cita finalice o sea cancelada.'
        );

    }


    /*=====================================================
        INSERTAR CITA
    =====================================================*/

    $sqlInsertar = "
        INSERT INTO citas_psicologia (
            id_disponibilidad,
            id_tutorado,
            motivo,
            estado
        )
        VALUES (
            ?,
            ?,
            ?,
            'PENDIENTE'
        )
    ";

    $stmtInsertar =
        $conn->prepare(
            $sqlInsertar
        );

    if (!$stmtInsertar) {

        throw new Exception(
            'No fue posible registrar la cita.'
        );

    }

    $stmtInsertar->bind_param(
        "iis",
        $id_disponibilidad,
        $id_tutorado,
        $motivo
    );

    if (!$stmtInsertar->execute()) {

        $stmtInsertar->close();

        throw new Exception(
            'No fue posible registrar la cita.'
        );

    }

    $id_cita =
        $conn->insert_id;

    $stmtInsertar->close();


    /*=====================================================
        RESTAR CUPO
    =====================================================*/

    $sqlActualizar = "
        UPDATE disponibilidad_psicologos
        SET cupos_disponibles =
            cupos_disponibles - 1
        WHERE id_disponibilidad = ?
          AND cupos_disponibles > 0
    ";

    $stmtActualizar =
        $conn->prepare(
            $sqlActualizar
        );

    if (!$stmtActualizar) {

        throw new Exception(
            'No fue posible actualizar el espacio disponible.'
        );

    }

    $stmtActualizar->bind_param(
        "i",
        $id_disponibilidad
    );

    if (!$stmtActualizar->execute()) {

        $stmtActualizar->close();

        throw new Exception(
            'No fue posible actualizar el espacio disponible.'
        );

    }


    /*=====================================================
        VERIFICAR ACTUALIZACIÓN
    =====================================================*/

    if ($stmtActualizar->affected_rows !== 1) {

        $stmtActualizar->close();

        throw new Exception(
            'El espacio acaba de ser ocupado por otro estudiante.'
        );

    }

    $stmtActualizar->close();


    /*=====================================================
        CONFIRMAR TRANSACCIÓN
    =====================================================*/

    $conn->commit();


    /*=====================================================
        RESPUESTA EXITOSA
    =====================================================*/

    respuesta(
        true,
        'Tu cita ha sido registrada correctamente.',
        [
            'id_cita' => $id_cita
        ]
    );


} catch (Throwable $e) {

    /*=====================================================
        DESHACER TODO
    =====================================================*/

    $conn->rollback();


    respuesta(
        false,
        $e->getMessage()
    );

}