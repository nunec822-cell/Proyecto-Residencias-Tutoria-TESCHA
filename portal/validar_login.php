<?php
/*=========================================================
    PORTAL INSTITUCIONAL DE TUTORÍAS (PIT)
    Archivo: validar_login.php

    - Consultas preparadas y password_verify
    - Límite de intentos (tabla login_intentos, si existe)
    - Regenera el id de sesión al entrar (anti fijación)
    - Carga los permisos del usuario en la sesión
=========================================================*/

require_once __DIR__ . '/../core/bootstrap.php';

sist_iniciar_sesion();

require_once __DIR__ . '/base_pit/conect_pit.php';

/* SIST_HASH_RELLENO (core/auth.php) iguala el tiempo de respuesta cuando el usuario no existe */

function login_error(string $mensaje): void
{
    $_SESSION['error_login'] = $mensaje;
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$usuario  = mb_strtoupper(trim($_POST['usuario'] ?? ''), 'UTF-8');
$password = trim($_POST['password'] ?? '');

if ($usuario === '' || $password === '') {
    login_error('Debe ingresar usuario y contraseña.');
}

if (sist_login_bloqueado($conn, $usuario)) {
    login_error('Demasiados intentos fallidos. Espere 15 minutos e inténtelo de nuevo.');
}

/*---------------------------------------------------------
    BUSCAR USUARIO Y VERIFICAR CONTRASEÑA
---------------------------------------------------------*/
$stmt = $conn->prepare(
    'SELECT id_usuario, usuario, password, activo
     FROM usuarios
     WHERE usuario = ?
     LIMIT 1'
);
$stmt->bind_param('s', $usuario);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();

$hash = $datos['password'] ?? SIST_HASH_RELLENO;
$correcta = password_verify($password, $hash);

if (!$datos || !$correcta) {
    sist_login_registrar_fallo($conn, $usuario);
    login_error('Usuario o contraseña incorrectos.');
}

if ((int) $datos['activo'] !== 1) {
    login_error('La cuenta se encuentra inactiva.');
}

/*---------------------------------------------------------
    ROLES
---------------------------------------------------------*/
$stmtRoles = $conn->prepare(
    'SELECT t.id_tipo, t.nombre
     FROM usuario_tipo ut
     INNER JOIN tipos t ON ut.id_tipo = t.id_tipo
     WHERE ut.id_usuario = ?
     AND ut.activo = 1'
);
$idUsuario = (int) $datos['id_usuario'];
$stmtRoles->bind_param('i', $idUsuario);
$stmtRoles->execute();
$resRoles = $stmtRoles->get_result();

$roles = [];
$idsTipos = [];

while ($fila = $resRoles->fetch_assoc()) {
    $roles[] = $fila['nombre'];
    $idsTipos[] = (int) $fila['id_tipo'];
}

if (empty($roles)) {
    login_error('No tiene permisos para acceder.');
}

/*---------------------------------------------------------
    SESIÓN NUEVA (anti fijación de sesión)
---------------------------------------------------------*/
session_regenerate_id(true);

$_SESSION = [];
$_SESSION['id_usuario'] = $idUsuario;
$_SESSION['usuario']    = $datos['usuario'];
$_SESSION['roles']      = $roles;

/* Con un solo rol se guarda directamente su id_tipo (DIRECTIVO = 2) */
if (count($idsTipos) === 1) {
    $_SESSION['id_tipo'] = $idsTipos[0];
}

$_SESSION['permisos']         = sist_cargar_permisos($conn, $idUsuario, $roles, $idsTipos);
$_SESSION['ultima_actividad'] = time();
$_SESSION['autenticado']      = true;

/* Contraseña asignada por el administrador: debe cambiarla antes de usar el portal */
if (sist_pw_debe_cambiar($conn, $idUsuario)) {
    $_SESSION['debe_cambiar_password'] = true;
}

sist_login_limpiar($conn, $usuario);

/*---------------------------------------------------------
    REDIRECCIÓN SEGÚN ROL
---------------------------------------------------------*/
$destino = sist_destino_por_rol($roles);

if ($destino !== null) {
    header('Location: ' . (!empty($_SESSION['debe_cambiar_password']) ? 'cambiar_password.php' : $destino));
    exit();
}

sist_cerrar_sesion();
sist_iniciar_sesion();
login_error('No tiene permisos para acceder.');
