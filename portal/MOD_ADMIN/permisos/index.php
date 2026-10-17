<?php
/*=========================================================
    ADMINISTRACIÓN DE PERMISOS
    - Permisos por ROL (tabla tipos)
    - Permisos individuales por USUARIO
    Requiere el permiso: permisos.gestionar
    Los cambios se aplican la próxima vez que el usuario inicie sesión.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once("../../sesion.php");
requerir_permiso('permisos.gestionar');
include("../../base_pit/conect_pit.php");
include("../../includes_pit/sidebar_admin.php");

/* ¿Existen ya las tablas de la migración 001? */
$tablas_ok = true;
try {
    $conn->query("SELECT 1 FROM permisos LIMIT 1");
    $conn->query("SELECT 1 FROM rol_permiso LIMIT 1");
    $conn->query("SELECT 1 FROM usuario_permiso LIMIT 1");
} catch (Throwable $ex) {
    $tablas_ok = false;
}

$roles = [];
$catalogo = [];
$id_tipo = (int) ($_GET['id_tipo'] ?? 0);
$buscar_usuario = trim($_GET['usuario'] ?? '');
$rol_marcados = [];
$usuario_enc = null;
$usuario_roles = [];
$usuario_marcados = [];

if ($tablas_ok) {
    $res = $conn->query("SELECT id_tipo, nombre FROM tipos ORDER BY nombre");
    while ($f = $res->fetch_assoc()) {
        $roles[] = $f;
    }

    $res = $conn->query("SELECT id_permiso, clave, descripcion, modulo FROM permisos ORDER BY modulo, clave");
    while ($f = $res->fetch_assoc()) {
        $catalogo[$f['modulo']][] = $f;
    }

    if ($id_tipo > 0) {
        $stmt = $conn->prepare("SELECT id_permiso FROM rol_permiso WHERE id_tipo = ?");
        $stmt->bind_param('i', $id_tipo);
        $stmt->execute();
        $r = $stmt->get_result();
        while ($f = $r->fetch_assoc()) {
            $rol_marcados[(int) $f['id_permiso']] = true;
        }
    }

    if ($buscar_usuario !== '') {
        $nombre = mb_strtoupper($buscar_usuario, 'UTF-8');
        $stmt = $conn->prepare("SELECT id_usuario, usuario FROM usuarios WHERE usuario = ? LIMIT 1");
        $stmt->bind_param('s', $nombre);
        $stmt->execute();
        $usuario_enc = $stmt->get_result()->fetch_assoc();

        if ($usuario_enc) {
            $uid = (int) $usuario_enc['id_usuario'];

            $stmt = $conn->prepare(
                "SELECT t.nombre FROM usuario_tipo ut
                 INNER JOIN tipos t ON t.id_tipo = ut.id_tipo
                 WHERE ut.id_usuario = ? AND ut.activo = 1"
            );
            $stmt->bind_param('i', $uid);
            $stmt->execute();
            $r = $stmt->get_result();
            while ($f = $r->fetch_assoc()) {
                $usuario_roles[] = $f['nombre'];
            }

            $stmt = $conn->prepare("SELECT id_permiso FROM usuario_permiso WHERE id_usuario = ?");
            $stmt->bind_param('i', $uid);
            $stmt->execute();
            $r = $stmt->get_result();
            while ($f = $r->fetch_assoc()) {
                $usuario_marcados[(int) $f['id_permiso']] = true;
            }
        }
    }
}
?>
<div class="separador"></div>
<div class="separador"></div>
<link rel="stylesheet" href="permisos.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<div class="contenedor-permisos">

    <div class="encabezado">
        <h1><i class="fas fa-user-shield"></i> Permisos</h1>
        <p>Define qué puede hacer cada rol y, si hace falta, cada persona. El rol ADMIN siempre tiene todos los permisos.</p>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="mensaje ok">✓ Permisos guardados. Se aplican la próxima vez que la persona inicie sesión.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="mensaje error">✗ No se pudieron guardar los cambios.</div>
    <?php endif; ?>

    <?php if (!$tablas_ok): ?>
        <div class="mensaje error">
            Las tablas de permisos aún no existen. Ejecuta el archivo
            <strong>database/migraciones/001_permisos.sql</strong> en la base PIT_V4
            y recarga esta página. Mientras tanto el sistema usa los permisos por defecto
            de <strong>config/permisos.php</strong>.
        </div>
    <?php else: ?>

    <!-- ===================== POR ROL ===================== -->
    <div class="card">
        <h2>Permisos por rol</h2>

        <form method="GET" class="fila">
            <label for="id_tipo">Rol</label>
            <select name="id_tipo" id="id_tipo" onchange="this.form.submit()">
                <option value="0">Selecciona un rol…</option>
                <?php foreach ($roles as $rol): ?>
                    <option value="<?= (int) $rol['id_tipo'] ?>" <?= $id_tipo === (int) $rol['id_tipo'] ? 'selected' : '' ?>>
                        <?= e($rol['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($id_tipo > 0): ?>
        <form method="POST" action="guardar.php">
            <?= sist_csrf_campo() ?>
            <input type="hidden" name="accion" value="rol">
            <input type="hidden" name="id_tipo" value="<?= $id_tipo ?>">

            <?php foreach ($catalogo as $modulo => $lista): ?>
                <h3><?= e($modulo) ?></h3>
                <div class="lista">
                <?php foreach ($lista as $p): ?>
                    <label class="permiso">
                        <input type="checkbox" name="permisos[]" value="<?= (int) $p['id_permiso'] ?>"
                            <?= isset($rol_marcados[(int) $p['id_permiso']]) ? 'checked' : '' ?>>
                        <span>
                            <strong><?= e($p['clave']) ?></strong>
                            <small><?= e($p['descripcion']) ?></small>
                        </span>
                    </label>
                <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-guardar"><i class="fas fa-save"></i> Guardar permisos del rol</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- ===================== POR USUARIO ===================== -->
    <div class="card">
        <h2>Permisos adicionales por usuario</h2>
        <p class="nota">Se suman a los permisos de sus roles.</p>

        <form method="GET" class="fila">
            <label for="usuario">Usuario</label>
            <input type="text" name="usuario" id="usuario" value="<?= e($buscar_usuario) ?>" placeholder="Nombre de usuario">
            <button type="submit" class="btn-buscar"><i class="fas fa-search"></i> Buscar</button>
        </form>

        <?php if ($buscar_usuario !== '' && !$usuario_enc): ?>
            <div class="mensaje error">No se encontró ese usuario.</div>
        <?php endif; ?>

        <?php if ($usuario_enc): ?>
        <p class="nota">
            <strong><?= e($usuario_enc['usuario']) ?></strong> ·
            Roles: <?= e(implode(', ', $usuario_roles) ?: 'ninguno') ?>
        </p>
        <form method="POST" action="guardar.php">
            <?= sist_csrf_campo() ?>
            <input type="hidden" name="accion" value="usuario">
            <input type="hidden" name="id_usuario" value="<?= (int) $usuario_enc['id_usuario'] ?>">
            <input type="hidden" name="usuario" value="<?= e($usuario_enc['usuario']) ?>">

            <?php foreach ($catalogo as $modulo => $lista): ?>
                <h3><?= e($modulo) ?></h3>
                <div class="lista">
                <?php foreach ($lista as $p): ?>
                    <label class="permiso">
                        <input type="checkbox" name="permisos[]" value="<?= (int) $p['id_permiso'] ?>"
                            <?= isset($usuario_marcados[(int) $p['id_permiso']]) ? 'checked' : '' ?>>
                        <span>
                            <strong><?= e($p['clave']) ?></strong>
                            <small><?= e($p['descripcion']) ?></small>
                        </span>
                    </label>
                <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn-guardar"><i class="fas fa-save"></i> Guardar permisos del usuario</button>
        </form>
        <?php endif; ?>
    </div>

    <?php endif; ?>
</div>
