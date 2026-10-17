<?php
/*=========================================================
    SIST - PERMISOS

    tiene_permiso('clave')        -> bool
    requerir_permiso('clave')     -> corta con 401/403 si no

    Los permisos se cargan UNA vez al iniciar sesión y viven en
    $_SESSION['permisos'] (sin consultas extra por petición).
    ADMIN siempre tiene todos los permisos.
=========================================================*/

function sist_catalogo_permisos(): array
{
    static $catalogo = null;

    if ($catalogo === null) {
        $catalogo = require SIST_ROOT . '/config/permisos.php';
    }

    return $catalogo;
}

/* Permisos por defecto según los nombres de rol (config/permisos.php) */
function sist_permisos_por_defecto(array $roles): array
{
    $mapa     = sist_catalogo_permisos()['roles'];
    $permisos = [];

    foreach ($roles as $rol) {
        foreach ($mapa[$rol] ?? [] as $clave) {
            $permisos[$clave] = true;
        }
    }

    return array_keys($permisos);
}

/**
 * Calcula los permisos de un usuario al iniciar sesión.
 * Si las tablas de permisos existen, la base de datos manda
 * (permisos del rol + permisos individuales). Si no existen
 * todavía, se usa el catálogo por defecto.
 */
function sist_cargar_permisos(mysqli $conn, int $idUsuario, array $roles, array $idsTipos): array
{
    try {
        $ids = array_values(array_unique(array_map('intval', $idsTipos)));
        $permisos = [];

        if (!empty($ids)) {
            $lista = implode(',', $ids);
            $res = $conn->query(
                "SELECT DISTINCT p.clave
                 FROM rol_permiso rp
                 INNER JOIN permisos p ON p.id_permiso = rp.id_permiso
                 WHERE rp.id_tipo IN ($lista)"
            );
            while ($f = $res->fetch_assoc()) {
                $permisos[$f['clave']] = true;
            }
        }

        $stmt = $conn->prepare(
            "SELECT p.clave
             FROM usuario_permiso up
             INNER JOIN permisos p ON p.id_permiso = up.id_permiso
             WHERE up.id_usuario = ?"
        );
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($f = $res->fetch_assoc()) {
            $permisos[$f['clave']] = true;
        }

        return array_keys($permisos);
    } catch (Throwable $e) {
        /* Tablas aún no creadas: se usa el catálogo por defecto */
        return sist_permisos_por_defecto($roles);
    }
}

function sist_permisos_sesion(): array
{
    sist_iniciar_sesion();

    if (!isset($_SESSION['permisos']) || !is_array($_SESSION['permisos'])) {
        $_SESSION['permisos'] = sist_permisos_por_defecto(sist_roles());
    }

    return $_SESSION['permisos'];
}

function tiene_permiso(string $clave): bool
{
    if (tiene_rol('ADMIN')) {
        return true;
    }

    return in_array($clave, sist_permisos_sesion(), true);
}

function requerir_permiso(string $clave): void
{
    requerir_login();

    if (!tiene_permiso($clave)) {
        sist_log('Acceso denegado a "' . $clave . '" (usuario ' . ($_SESSION['id_usuario'] ?? '?') . ')');
        sist_denegar(403);
    }
}
