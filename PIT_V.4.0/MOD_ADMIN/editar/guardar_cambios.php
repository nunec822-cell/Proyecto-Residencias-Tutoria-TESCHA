<?php

include("../../base_pit/conect_pit.php");

/*=========================================
VALIDAR MÉTODO
=========================================*/

if($_SERVER["REQUEST_METHOD"] != "POST")
{
    header("Location:index.php");
    exit();
}

/*=========================================
DATOS GENERALES
=========================================*/

$id_usuario = intval($_POST['id_usuario']);

$usuario = trim($_POST['usuario']);

$activo = intval($_POST['activo']);

$password = "";

if(isset($_POST['password']))
{
    $password = trim($_POST['password']);
}

/*=========================================
VALIDAR CONTRASEÑA REPETIDA
=========================================*/

if(!empty($password))
{

    $sql = "
    SELECT
        id_usuario,
        password
    FROM usuarios
    WHERE id_usuario <> ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $id_usuario
    );

    $stmt->execute();

    $resultado =
    $stmt->get_result();

    while(
        $fila =
        $resultado->fetch_assoc()
    )
    {

        if(
            password_verify(
                $password,
                $fila['password']
            )
        )
        {

            header(
                "Location:index.php?password=1"
            );
            exit();

        }

    }

}

/*=========================================
TRANSACCIÓN
=========================================*/

$conn->begin_transaction();

try
{

    /*=========================================
    VALIDAR USUARIO REPETIDO
    =========================================*/

    $sql = "
    SELECT id_usuario
    FROM usuarios
    WHERE usuario = ?
    AND id_usuario <> ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "si",
        $usuario,
        $id_usuario
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0)
    {
        throw new Exception(
            "Usuario repetido"
        );
    }

    /*=========================================
    ACTUALIZAR USUARIO
    =========================================*/

    if(!empty($password))
    {

        $password_hash =
        password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "
        UPDATE usuarios
        SET
            usuario = ?,
            password = ?,
            activo = ?
        WHERE id_usuario = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssii",
            $usuario,
            $password_hash,
            $activo,
            $id_usuario
        );

        $stmt->execute();

    }
    else
    {

        $sql = "
        UPDATE usuarios
        SET
            usuario = ?,
            activo = ?
        WHERE id_usuario = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sii",
            $usuario,
            $activo,
            $id_usuario
        );

        $stmt->execute();

    }

    /*=========================================
    OBTENER TIPO DE USUARIO
    =========================================*/

    $tipo_principal = "";

    $sql = "
    SELECT t.nombre
    FROM usuario_tipo ut
    INNER JOIN tipos t
    ON ut.id_tipo = t.id_tipo
    WHERE ut.id_usuario = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $id_usuario
    );

    $stmt->execute();

    $resultado =
    $stmt->get_result();

    $roles_actuales = [];

    while(
        $fila =
        $resultado->fetch_assoc()
    )
    {
        $roles_actuales[] =
        $fila['nombre'];
    }

    /*=========================================
    ACTUALIZAR ROLES DOCENTE
    =========================================*/

    if(
        in_array(
            "DOCENTE",
            $roles_actuales
        )
    )
    {

        $sql = "
        DELETE FROM usuario_tipo
        WHERE id_usuario = ?
        ";

        $stmt =
        $conn->prepare($sql);

        $stmt->bind_param(
            "i",
            $id_usuario
        );

        $stmt->execute();

        /* DOCENTE */

        $id_tipo = 5;

        $stmt =
        $conn->prepare("
        INSERT INTO usuario_tipo
        (
            id_usuario,
            id_tipo,
            activo
        )
        VALUES
        (
            ?,?,?
        )
        ");

        $stmt->bind_param(
            "iii",
            $id_usuario,
            $id_tipo,
            $activo
        );

        $stmt->execute();

        /* TUTOR */

        if(
            isset($_POST['roles'])
            &&
            in_array(
                "TUTOR",
                $_POST['roles']
            )
        )
        {

            $id_tipo = 6;

            $stmt =
            $conn->prepare("
            INSERT INTO usuario_tipo
            (
                id_usuario,
                id_tipo,
                activo
            )
            VALUES
            (
                ?,?,?
            )
            ");

            $stmt->bind_param(
                "iii",
                $id_usuario,
                $id_tipo,
                $activo
            );

            $stmt->execute();

        }

        /* COORDINADOR */

        if(
            isset($_POST['roles'])
            &&
            in_array(
                "COORDINADOR",
                $_POST['roles']
            )
        )
        {

            $id_tipo = 4;

            $stmt =
            $conn->prepare("
            INSERT INTO usuario_tipo
            (
                id_usuario,
                id_tipo,
                activo
            )
            VALUES
            (
                ?,?,?
            )
            ");

            $stmt->bind_param(
                "iii",
                $id_usuario,
                $id_tipo,
                $activo
            );

            $stmt->execute();

        }

    }
    /*=========================================
    PERSONAL ACADÉMICO
    =========================================*/

    $sql = "
    SELECT id_usuario
    FROM personal_academico
    WHERE id_usuario = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $id_usuario
    );

    $stmt->execute();

    $res =
    $stmt->get_result();

    if($res->num_rows > 0)
    {

        $sql = "
        UPDATE personal_academico
        SET
            no_empleado = ?,
            nombre = ?,
            apellido_p = ?,
            apellido_m = ?,
            carrera = ?,
            activo = ?
        WHERE id_usuario = ?
        ";

        $stmt =
        $conn->prepare($sql);

        $stmt->bind_param(
            "sssssii",
            $_POST['no_empleado'],
            $_POST['nombre'],
            $_POST['apellido_p'],
            $_POST['apellido_m'],
            $_POST['carrera'],
            $activo,
            $id_usuario
        );

        $stmt->execute();

    }

    /*=========================================
    DIRECTIVOS
    =========================================*/

    $sql = "
    SELECT id_usuario
    FROM directivos
    WHERE id_usuario = ?
    ";

    $stmt =
    $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $id_usuario
    );

    $stmt->execute();

    $res =
    $stmt->get_result();

    if($res->num_rows > 0)
    {

        $sql = "
        UPDATE directivos
        SET
            no_empleado = ?,
            nombre = ?,
            apellido_p = ?,
            apellido_m = ?,
            activo = ?
        WHERE id_usuario = ?
        ";

        $stmt =
        $conn->prepare($sql);

        $stmt->bind_param(
            "ssssii",
            $_POST['dir_no_empleado'],
            $_POST['dir_nombre'],
            $_POST['dir_apellido_p'],
            $_POST['dir_apellido_m'],
            $activo,
            $id_usuario
        );

        $stmt->execute();

    }

    /*=========================================
    PSICÓLOGOS
    =========================================*/

    $sql = "
    SELECT id_usuario
    FROM administradores_psicologos
    WHERE id_usuario = ?
    ";

    $stmt =
    $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $id_usuario
    );

    $stmt->execute();

    $res =
    $stmt->get_result();

    if($res->num_rows > 0)
    {

        $sql = "
        UPDATE administradores_psicologos
        SET
            no_empleado = ?,
            nombre = ?,
            apellido_p = ?,
            apellido_m = ?,
            correo_institucional = ?,
            activo = ?
        WHERE id_usuario = ?
        ";

        $stmt =
        $conn->prepare($sql);

        $stmt->bind_param(
            "sssssii",
            $_POST['psi_no_empleado'],
            $_POST['psi_nombre'],
            $_POST['psi_apellido_p'],
            $_POST['psi_apellido_m'],
            $_POST['correo_institucional'],
            $activo,
            $id_usuario
        );

        $stmt->execute();

    }

    /*=========================================
    TUTORADOS
    =========================================*/

    $sql = "
    SELECT id_usuario
    FROM tutorados
    WHERE id_usuario = ?
    ";

    $stmt =
    $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $id_usuario
    );

    $stmt->execute();

    $res =
    $stmt->get_result();

    if($res->num_rows > 0)
    {

        $sql = "
        UPDATE tutorados
        SET
            matricula = ?,
            nombre = ?,
            apellido_p = ?,
            apellido_m = ?,
            carrera = ?,
            grupo = ?,
            id_tutor = ?,
            activo = ?
        WHERE id_usuario = ?
        ";

        $stmt =
        $conn->prepare($sql);

        $stmt->bind_param(
            "ssssssiii",
            $_POST['matricula'],
            $_POST['alu_nombre'],
            $_POST['alu_apellido_p'],
            $_POST['alu_apellido_m'],
            $_POST['alu_carrera'],
            $_POST['grupo'],
            $_POST['id_tutor'],
            $activo,
            $id_usuario
        );

        $stmt->execute();

    }

    /*=========================================
    NOMBRE COMPLETO CSV
    =========================================*/

    $nombre_completo = "";

    if(isset($_POST['nombre']))
    {

        $nombre_completo =
        $_POST['nombre']." ".
        $_POST['apellido_p']." ".
        $_POST['apellido_m'];

    }
    elseif(isset($_POST['dir_nombre']))
    {

        $nombre_completo =
        $_POST['dir_nombre']." ".
        $_POST['dir_apellido_p']." ".
        $_POST['dir_apellido_m'];

    }
    elseif(isset($_POST['psi_nombre']))
    {

        $nombre_completo =
        $_POST['psi_nombre']." ".
        $_POST['psi_apellido_p']." ".
        $_POST['psi_apellido_m'];

    }
    elseif(isset($_POST['alu_nombre']))
    {

        $nombre_completo =
        $_POST['alu_nombre']." ".
        $_POST['alu_apellido_p']." ".
        $_POST['alu_apellido_m'];

    }
    /*=========================================
    ROLES CSV
    =========================================*/

    $roles_csv = [];

    $sql = "
    SELECT t.nombre
    FROM usuario_tipo ut
    INNER JOIN tipos t
    ON ut.id_tipo = t.id_tipo
    WHERE ut.id_usuario = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "i",
        $id_usuario
    );

    $stmt->execute();

    $resultado =
    $stmt->get_result();

    while(
        $fila =
        $resultado->fetch_assoc()
    )
    {
        $roles_csv[] =
        $fila['nombre'];
    }

    /*=========================================
    TIPO PRINCIPAL CSV
    =========================================*/

    $tipo_csv = "";

    if(in_array("TUTORADO",$roles_csv))
    {
        $tipo_csv = "TUTORADO";
    }
    elseif(in_array("PSICOLOGO",$roles_csv))
    {
        $tipo_csv = "PSICOLOGO";
    }
    elseif(in_array("DIRECTIVO",$roles_csv))
    {
        $tipo_csv = "DIRECTIVO";
    }
    elseif(in_array("JEFE_CARRERA",$roles_csv))
    {
        $tipo_csv = "JEFE_CARRERA";
    }
    else
    {
        $tipo_csv = "DOCENTE";
    }

    /*=========================================
    ESTADO CSV
    =========================================*/

    $estado_csv =
    ($activo == 1)
    ? "ACTIVO"
    : "INACTIVO";

    /*=========================================
    CONTRASEÑA CSV
    =========================================*/

    $password_csv = "";

    if(!empty($password))
    {
        $password_csv = $password;
    }
    else
    {

        $archivo_csv =
        __DIR__ .
        "/../../archivos/credenciales_usuarios.csv";

        if(file_exists($archivo_csv))
        {

            $lineas =
            array_map(
                "str_getcsv",
                file($archivo_csv)
            );

            foreach($lineas as $fila_csv)
            {

                if(
                    isset($fila_csv[0])
                    &&
                    intval($fila_csv[0])
                    ==
                    $id_usuario
                )
                {
                    $password_csv =
                    $fila_csv[4];

                    break;
                }

            }

        }

    }

    /*=========================================
    ACTUALIZAR CSV
    =========================================*/

    $archivo_csv =
    __DIR__ .
    "/../../archivos/credenciales_usuarios.csv";

    if(file_exists($archivo_csv))
    {

        $datos =
        array_map(
            "str_getcsv",
            file($archivo_csv)
        );

        foreach(
            $datos
            as
            $indice => $fila_csv
        )
        {

            if($indice == 0)
            {
                continue;
            }

            if(
                isset($fila_csv[0])
                &&
                intval($fila_csv[0])
                ==
                $id_usuario
            )
            {

                $encontrado = true;
                $datos[$indice] =
                [
                    $id_usuario,
                    $fila_csv[1],
                    $usuario,
                    $nombre_completo,
                    $password_csv,
                    $tipo_csv,
                    implode(", ",$roles_csv),
                $estado_csv
                ];

                break;

            }

        }
        
        $fp =
        fopen(
            $archivo_csv,
            "w"
        );

        foreach(
            $datos
            as
            $fila
        )
        {
            fputcsv(
                $fp,
                $fila
            );
        }

        fclose($fp);

    }

    /*=========================================
    COMMIT
    =========================================*/

    $conn->commit();

    header(
        "Location:index.php?ok=2"
    );

    exit();

}
catch(Exception $e)
{

    $conn->rollback();

    header(
        "Location:index.php?error=2"
    );

    exit();

}

?>