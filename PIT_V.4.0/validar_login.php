
<?php
/*=========================================================
    PORTAL INSTITUCIONAL DE TUTORÍAS (PIT)
    Archivo: validar_login.php
    SIST V.4.0
=========================================================*/

session_start();

require_once("base_pit/conect_pit.php");


/*=========================================================
    VALIDAR ACCESO POR POST
=========================================================*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: indexloguin.php");
    exit();

}


/*=========================================================
    RECIBIR DATOS
=========================================================*/

$usuario = trim($_POST["usuario"] ?? "");
$password = trim($_POST["password"] ?? "");


/*=========================================================
    VALIDACIONES BÁSICAS
=========================================================*/

if (empty($usuario) || empty($password)) {

    $_SESSION["error_login"] =
        "Debe ingresar usuario y contraseña.";

    header("Location: indexloguin.php");
    exit();

}


/*=========================================================
    NORMALIZAR USUARIO
=========================================================*/

$usuario = mb_strtoupper($usuario, "UTF-8");


/*=========================================================
    BUSCAR USUARIO
=========================================================*/

$sql = "SELECT
            id_usuario,
            usuario,
            password,
            activo
        FROM usuarios
        WHERE usuario = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die("Error al preparar consulta.");

}

$stmt->bind_param("s", $usuario);

$stmt->execute();

$resultado = $stmt->get_result();


/*=========================================================
    ¿EXISTE EL USUARIO?
=========================================================*/

if ($resultado->num_rows === 0) {

    $_SESSION["error_login"] =
        "Usuario o contraseña incorrectos.";

    header("Location: indexloguin.php");
    exit();

}

$datosUsuario = $resultado->fetch_assoc();


/*=========================================================
    ¿USUARIO ACTIVO?
=========================================================*/

if ((int)$datosUsuario["activo"] !== 1) {

    $_SESSION["error_login"] =
        "La cuenta se encuentra inactiva.";

    header("Location: indexloguin.php");
    exit();

}


/*=========================================================
    VERIFICAR CONTRASEÑA
=========================================================*/

if (!password_verify(
    $password,
    $datosUsuario["password"]
)) {

    $_SESSION["error_login"] =
        "Usuario o contraseña incorrectos.";

    header("Location: indexloguin.php");
    exit();

}


/*=========================================================
    OBTENER ROLES DEL USUARIO
=========================================================*/

$sqlRoles = "
    SELECT
        t.id_tipo,
        t.nombre
    FROM usuario_tipo ut

    INNER JOIN tipos t
        ON ut.id_tipo = t.id_tipo

    WHERE ut.id_usuario = ?
    AND ut.activo = 1
";

$stmtRoles = $conn->prepare($sqlRoles);

if (!$stmtRoles) {

    die("Error al preparar consulta de roles.");

}

$stmtRoles->bind_param(
    "i",
    $datosUsuario["id_usuario"]
);

$stmtRoles->execute();

$resultadoRoles = $stmtRoles->get_result();


/*=========================================================
    GUARDAR ROLES
=========================================================*/

$roles = [];

$idsTipos = [];

while ($fila = $resultadoRoles->fetch_assoc()) {

    $roles[] = $fila["nombre"];

    $idsTipos[] = (int)$fila["id_tipo"];

}


/*=========================================================
    VALIDAR QUE TENGA AL MENOS UN ROL
=========================================================*/

if (empty($roles)) {

    session_destroy();

    session_start();

    $_SESSION["error_login"] =
        "No tiene permisos para acceder.";

    header("Location: indexloguin.php");

    exit();

}


/*=========================================================
    CREAR VARIABLES DE SESIÓN
=========================================================*/

$_SESSION["id_usuario"] =
    $datosUsuario["id_usuario"];

$_SESSION["usuario"] =
    $datosUsuario["usuario"];

$_SESSION["roles"] =
    $roles;


/*=========================================================
    GUARDAR ID DE TIPO
=========================================================*/

/*
    Si el usuario tiene un solo rol,
    guardamos directamente su id_tipo.

    DIRECTIVO = 2
*/

if (count($idsTipos) === 1) {

    $_SESSION["id_tipo"] =
        $idsTipos[0];

}


/*=========================================================
    INDICAR QUE EL USUARIO ESTÁ AUTENTICADO
=========================================================*/

$_SESSION["autenticado"] = true;


/*=========================================================
    REDIRECCIONES
=========================================================*/


/*=========================================================
    ADMINISTRADOR
=========================================================*/

if (in_array("ADMIN", $roles)) {

    header(
        "Location: MOD_ADMIN/inicioadmin.php"
    );

    exit();

}


/*=========================================================
    DIRECTIVO
=========================================================*/

if (in_array("DIRECTIVO", $roles)) {

    header(
        "Location: MOD_DIRECTIVOS/inicio.php"
    );

    exit();

}


/*=========================================================
    JEFE DE CARRERA
=========================================================*/

if (in_array("JEFE_CARRERA", $roles)) {

    header(
        "Location: MOD_JEFESCARRERA/iniciojefescarrera.php"
    );

    exit();

}


/*=========================================================
    PSICÓLOGO
=========================================================*/

if (in_array("PSICOLOGO", $roles)) {

    header(
        "Location: MOD_PSICOLOGOS/inicio.php"
    );

    exit();

}


/*=========================================================
    TUTORADO
=========================================================*/

if (in_array("TUTORADO", $roles)) {

    header(
        "Location: MOD_TUTORADO/Inicio_tutorado.php"
    );

    exit();

}


/*=========================================================
    DOCENTE / TUTOR / COORDINADOR
=========================================================*/

if (
    in_array("DOCENTE", $roles) ||
    in_array("TUTOR", $roles) ||
    in_array("COORDINADOR", $roles)
) {

    header(
        "Location: MOD_DOCENTE/iniciodocente.php"
    );

    exit();

}


/*=========================================================
    SI NO TIENE PERFIL VÁLIDO
=========================================================*/

session_destroy();

session_start();

$_SESSION["error_login"] =
    "No tiene permisos para acceder.";

header(
    "Location: indexloguin.php"
);

exit();

?>

