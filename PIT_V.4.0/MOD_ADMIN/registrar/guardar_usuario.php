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

$tipo_usuario = trim($_POST['tipo_usuario']);
$usuario      = trim($_POST['usuario']);
$password     = $_POST['password'];
$activo       = intval($_POST['activo']);

/*=========================================
VALIDAR USUARIO REPETIDO
=========================================*/

$sql = "
SELECT id_usuario
FROM usuarios
WHERE usuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$usuario);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0)
{
    header("Location:index.php?usuario=1");
    exit();
}

/*=========================================
VALIDAR CONTRASEÑA REPETIDA
=========================================*/

$sql = "
SELECT password
FROM usuarios
";

$resultado = $conn->query($sql);

while($fila = $resultado->fetch_assoc())
{
    if(password_verify($password,$fila['password']))
    {
        header("Location:index.php?error=password");
        exit();
    }
}
/*=========================================
VALIDAR TUTOR EN TUTORADOS
=========================================*/

if(
    $tipo_usuario == "TUTORADO"
    &&
    empty($_POST['id_tutor'])
)
{
    header("Location:index.php?error=tutor");
    exit();
}

/*=========================================
ENCRIPTAR PASSWORD
=========================================*/

$password_hash =
password_hash(
    $password,
    PASSWORD_DEFAULT
);

/*=========================================
TRANSACCIÓN
=========================================*/

$conn->begin_transaction();

try
{

    /*=========================================
    INSERT USUARIO
    =========================================*/

    $sql = "
    INSERT INTO usuarios
    (
        usuario,
        password,
        activo
    )
    VALUES
    (
        ?,?,?
    )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssi",
        $usuario,
        $password_hash,
        $activo
    );

    $stmt->execute();

    $id_usuario =
    $conn->insert_id;

    /*=========================================
    DOCENTE
    =========================================*/

    if($tipo_usuario == "DOCENTE")
    {

        $no_empleado =
        trim($_POST['doc_no_empleado']);

        $nombre =
        trim($_POST['doc_nombre']);

        $apellido_p =
        trim($_POST['doc_apellido_p']);

        $apellido_m =
        trim($_POST['doc_apellido_m']);

        $carrera =
        trim($_POST['doc_carrera']);

        /* DOCENTE */

        $id_tipo = 5;

        $stmt = $conn->prepare("
        INSERT INTO usuario_tipo
        (
            id_usuario,
            id_tipo
        )
        VALUES
        (
            ?,?
        )
        ");

        $stmt->bind_param(
            "ii",
            $id_usuario,
            $id_tipo
        );

        $stmt->execute();

        /* ROLES ADICIONALES */

        if(isset($_POST['roles']))
        {
            foreach($_POST['roles'] as $rol)
            {

                if($rol == "TUTOR")
                {
                    $id_tipo = 6;
                }
                elseif($rol == "COORDINADOR")
                {
                    $id_tipo = 4;
                }
                else
                {
                    continue;
                }

                $stmt = $conn->prepare("
                INSERT INTO usuario_tipo
                (
                    id_usuario,
                    id_tipo
                )
                VALUES
                (
                    ?,?
                )
                ");

                $stmt->bind_param(
                    "ii",
                    $id_usuario,
                    $id_tipo
                );

                $stmt->execute();
            }
        }

        $stmt = $conn->prepare("
        INSERT INTO personal_academico
        (
            id_usuario,
            no_empleado,
            nombre,
            apellido_p,
            apellido_m,
            carrera,
            activo
        )
        VALUES
        (
            ?,?,?,?,?,?,?
        )
        ");

        $stmt->bind_param(
            "isssssi",
            $id_usuario,
            $no_empleado,
            $nombre,
            $apellido_p,
            $apellido_m,
            $carrera,
            $activo
        );

        $stmt->execute();

    }

    /*=========================================
    JEFE DE CARRERA
    =========================================*/

    elseif($tipo_usuario == "JEFE_CARRERA")
    {

        $no_empleado =
        trim($_POST['jefe_no_empleado']);

        $nombre =
        trim($_POST['jefe_nombre']);

        $apellido_p =
        trim($_POST['jefe_apellido_p']);

        $apellido_m =
        trim($_POST['jefe_apellido_m']);

        $carrera =
        trim($_POST['jefe_carrera']);

        $id_tipo = 3;

        $stmt = $conn->prepare("
        INSERT INTO usuario_tipo
        (
            id_usuario,
            id_tipo
        )
        VALUES
        (
            ?,?
        )
        ");

        $stmt->bind_param(
            "ii",
            $id_usuario,
            $id_tipo
        );

        $stmt->execute();

        $stmt = $conn->prepare("
        INSERT INTO personal_academico
        (
            id_usuario,
            no_empleado,
            nombre,
            apellido_p,
            apellido_m,
            carrera,
            activo
        )
        VALUES
        (
            ?,?,?,?,?,?,?
        )
        ");

        $stmt->bind_param(
            "isssssi",
            $id_usuario,
            $no_empleado,
            $nombre,
            $apellido_p,
            $apellido_m,
            $carrera,
            $activo
        );

        $stmt->execute();

    }

    /*=========================================
    DIRECTIVO
    =========================================*/

    elseif($tipo_usuario == "DIRECTIVO")
    {

        $id_tipo = 2;

        $stmt = $conn->prepare("
        INSERT INTO usuario_tipo
        (
            id_usuario,
            id_tipo
        )
        VALUES
        (
            ?,?
        )
        ");

        $stmt->bind_param(
            "ii",
            $id_usuario,
            $id_tipo
        );

        $stmt->execute();

        $stmt = $conn->prepare("
        INSERT INTO directivos
        (
            id_usuario,
            no_empleado,
            nombre,
            apellido_p,
            apellido_m,
            activo
        )
        VALUES
        (
            ?,?,?,?,?,?
        )
        ");

        $stmt->bind_param(
            "issssi",
            $id_usuario,
            $_POST['dir_no_empleado'],
            $_POST['dir_nombre'],
            $_POST['dir_apellido_p'],
            $_POST['dir_apellido_m'],
            $activo
        );

        $stmt->execute();

    }

    /*=========================================
    PSICOLOGO
    =========================================*/

    elseif($tipo_usuario == "PSICOLOGO")
    {

        $id_tipo = 7;

        $stmt = $conn->prepare("
        INSERT INTO usuario_tipo
        (
            id_usuario,
            id_tipo
        )
        VALUES
        (
            ?,?
        )
        ");

        $stmt->bind_param(
            "ii",
            $id_usuario,
            $id_tipo
        );

        $stmt->execute();

        $stmt = $conn->prepare("
        INSERT INTO administradores_psicologos
        (
            id_usuario,
            no_empleado,
            nombre,
            apellido_p,
            apellido_m,
            correo_institucional,
            activo
        )
        VALUES
        (
            ?,?,?,?,?,?,?
        )
        ");

        $stmt->bind_param(
            "isssssi",
            $id_usuario,
            $_POST['psi_no_empleado'],
            $_POST['psi_nombre'],
            $_POST['psi_apellido_p'],
            $_POST['psi_apellido_m'],
            $_POST['correo_institucional'],
            $activo
        );

        $stmt->execute();

    }

    /*=========================================
    TUTORADO
    =========================================*/

    elseif($tipo_usuario == "TUTORADO")
    {

        $id_tipo = 8;

        $stmt = $conn->prepare("
        INSERT INTO usuario_tipo
        (
            id_usuario,
            id_tipo
        )
        VALUES
        (
            ?,?
        )
        ");

        $stmt->bind_param(
            "ii",
            $id_usuario,
            $id_tipo
        );

        $stmt->execute();

        $stmt = $conn->prepare("
        INSERT INTO tutorados
        (
            id_usuario,
            matricula,
            nombre,
            apellido_p,
            apellido_m,
            carrera,
            grupo,
            id_tutor,
            activo
        )
        VALUES
        (
            ?,?,?,?,?,?,?,?,?
        )
        ");

        $stmt->bind_param(
            "issssssii",
            $id_usuario,
            $_POST['matricula'],
            $_POST['alu_nombre'],
            $_POST['alu_apellido_p'],
            $_POST['alu_apellido_m'],
            $_POST['alu_carrera'],
            $_POST['grupo'],
            $_POST['id_tutor'],
            $activo
        );

        $stmt->execute();

    }

    /*=========================================
GUARDAR CREDENCIALES CSV
=========================================*/

$archivo_csv =
__DIR__ .
"/../../archivos/credenciales_usuarios.csv";

/*==============================
ESTADO
==============================*/

$estado_texto =
($activo == 1)
? "ACTIVO"
: "INACTIVO";

/*==============================
ROLES
==============================*/

$roles_csv = [];

if($tipo_usuario == "DOCENTE")
{
    $roles_csv[] = "DOCENTE";

    if(isset($_POST['roles']))
    {
        foreach($_POST['roles'] as $rol)
        {
            $roles_csv[] = $rol;
        }
    }

    $nombre_completo =
    $nombre . " " .
    $apellido_p . " " .
    $apellido_m;
}
elseif($tipo_usuario == "JEFE_CARRERA")
{
    $roles_csv[] = "JEFE_CARRERA";

    $nombre_completo =
    $_POST['jefe_nombre']." ".
    $_POST['jefe_apellido_p']." ".
    $_POST['jefe_apellido_m'];
}
elseif($tipo_usuario == "DIRECTIVO")
{
    $roles_csv[] = "DIRECTIVO";

    $nombre_completo =
    $_POST['dir_nombre']." ".
    $_POST['dir_apellido_p']." ".
    $_POST['dir_apellido_m'];
}
elseif($tipo_usuario == "PSICOLOGO")
{
    $roles_csv[] = "PSICOLOGO";

    $nombre_completo =
    $_POST['psi_nombre']." ".
    $_POST['psi_apellido_p']." ".
    $_POST['psi_apellido_m'];
}
else
{
    $roles_csv[] = "TUTORADO";

    $nombre_completo =
    $_POST['alu_nombre']." ".
    $_POST['alu_apellido_p']." ".
    $_POST['alu_apellido_m'];
}

/*==============================
CREAR ARCHIVO SI ESTÁ VACÍO
==============================*/

$crear_encabezado = false;

if(
    !file_exists($archivo_csv)
)
{
    $crear_encabezado = true;
}
elseif(
    filesize($archivo_csv) == 0
)
{
    $crear_encabezado = true;
}

$fp = fopen(
    $archivo_csv,
    "a"
);

if($crear_encabezado)
{
    fputcsv(
    $fp,
    [
        "ID",
        "Fecha",
        "Usuario",
        "Nombre Completo",
        "Contraseña",
        "Tipo",
        "Roles",
        "Estado"
    ]
);
}

/*==============================
AGREGAR REGISTRO
==============================*/

fputcsv(
    $fp,
    [
        $id_usuario,
        date("Y-m-d H:i:s"),
        $usuario,
        $nombre_completo,
        $password,
        $tipo_usuario,
        implode(
            ", ",
            $roles_csv
        ),
        $estado_texto
    ]
);

fclose($fp);

/*=========================================
CONFIRMAR
=========================================*/

$conn->commit();

header("Location:index.php?ok=1");
exit();

}
catch(Exception $e)
{

    $conn->rollback();

    header("Location:index.php?error=1");
    exit();

}

?>