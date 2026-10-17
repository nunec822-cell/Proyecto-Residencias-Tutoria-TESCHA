<?php
/*=========================================================
    ADMINISTRADOR · CORREOS DE AVISOS
    Captura el correo de cada usuario (para recibir los avisos por
    correo) y muestra el estado de la cola de envío.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('usuarios.gestionar');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/asesorias_vista.php';

$listo = sist_asesorias_listo($conn);
$q     = trim((string) ($_GET['q'] ?? ''));
$solo  = ($_GET['ver'] ?? '') === 'sin_correo' ? 'sin_correo' : 'todos';

$filas = [];
$p     = ['pagina' => 1, 'paginas' => 1];
$cola  = ['PENDIENTE' => 0, 'ENVIADO' => 0, 'ERROR' => 0];

if ($listo) {
    $donde  = ' WHERE 1=1';
    $tipos  = '';
    $params = [];
    if ($q !== '') {
        $donde  .= " AND (u.usuario LIKE ? OR u.correo LIKE ?)";
        $tipos  .= 'ss';
        $like    = '%' . $q . '%';
        $params[] = $like;
        $params[] = $like;
    }
    if ($solo === 'sin_correo') {
        $donde .= " AND (u.correo IS NULL OR u.correo = '')";
    }

    $total = (int) sist_db_valor($conn, "SELECT COUNT(*) FROM usuarios u" . $donde, $tipos, $params);
    $p     = sist_paginacion($total, 25);
    $filas = sist_db_filas(
        $conn,
        "SELECT u.id_usuario, u.usuario, u.correo, u.activo,
                (SELECT GROUP_CONCAT(t.nombre ORDER BY t.id_tipo SEPARATOR ', ')
                 FROM usuario_tipo ut JOIN tipos t ON t.id_tipo = ut.id_tipo
                 WHERE ut.id_usuario = u.id_usuario) AS roles
         FROM usuarios u" . $donde . " ORDER BY u.usuario LIMIT ? OFFSET ?",
        $tipos . 'ii',
        array_merge($params, [$p['limite'], $p['offset']])
    );

    foreach (sist_db_filas($conn, "SELECT estado, COUNT(*) AS n FROM correos_pendientes GROUP BY estado") as $f) {
        $cola[$f['estado']] = (int) $f['n'];
    }
}

as_cabecera('Correos de avisos');
include __DIR__ . '/../../includes_pit/sidebar_admin.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Correos de avisos</h1>
    <p class="as-sub">
        Las notificaciones llegan siempre a la campanita del portal. Si el usuario tiene un correo aquí
        y el envío de correo está configurado, también le llega un correo.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ?>
        <div class="as-msg error">Falta aplicar la migración <code>database/migraciones/002_asesorias_notificaciones.sql</code>.</div>
    <?php else: ?>

    <div class="as-card">
        <h2>Estado del envío de correo</h2>
        <?php if (sist_correo_activo()): ?>
            <div class="as-msg ok" style="margin-bottom:10px">✓ Envío de correo configurado (servidor <?= e(sist_correo_config()['host']) ?>).</div>
        <?php else: ?>
            <div class="as-msg aviso" style="margin-bottom:10px">⚠ El envío de correo NO está configurado: los correos esperan en la cola
                y salen solos cuando se configure <code>config/correo.local.php</code>
                (hay un ejemplo en <code>config/correo.local.example.php</code>).</div>
        <?php endif; ?>
        <p style="margin:0 0 10px">En cola: <b><?= $cola['PENDIENTE'] ?></b> pendientes ·
            <b><?= $cola['ENVIADO'] ?></b> enviados · <b><?= $cola['ERROR'] ?></b> con error.</p>
        <?php if (sist_correo_activo() && $cola['PENDIENTE'] > 0): ?>
        <form method="POST" action="acciones/procesar_cola.php">
            <?= sist_csrf_campo() ?>
            <button class="as-btn sec chico" type="submit">Enviar pendientes ahora</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="as-card">
        <h2>Correo de cada usuario</h2>
        <form method="GET" class="as-filtros">
            <input type="text" name="q" value="<?= e($q) ?>" placeholder="Buscar usuario o correo"
                   style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;min-width:240px">
            <button class="as-btn chico" type="submit">Buscar</button>
            <a href="?ver=sin_correo" class="<?= $solo === 'sin_correo' ? 'activo' : '' ?>">Solo sin correo</a>
            <a href="?" class="<?= $solo === 'todos' && $q === '' ? 'activo' : '' ?>">Todos</a>
        </form>

        <table class="as-tabla">
            <thead><tr><th>Usuario</th><th>Rol(es)</th><th>Correo</th></tr></thead>
            <tbody>
            <?php foreach ($filas as $f): ?>
                <tr>
                    <td><?= e($f['usuario']) ?><?= $f['activo'] ? '' : ' <small>(inactivo)</small>' ?></td>
                    <td><?= e($f['roles'] ?? '') ?></td>
                    <td>
                        <form method="POST" action="acciones/guardar.php">
                            <?= sist_csrf_campo() ?>
                            <input type="hidden" name="id_usuario" value="<?= (int) $f['id_usuario'] ?>">
                            <input type="hidden" name="volver" value="<?= e(http_build_query(['q' => $q, 'ver' => $solo, 'pagina' => $p['pagina']])) ?>">
                            <input type="email" name="correo" value="<?= e($f['correo'] ?? '') ?>" maxlength="150" placeholder="correo@dominio.mx">
                            <button class="as-btn chico" type="submit">Guardar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$filas): ?><tr><td colspan="3" class="as-vacio">Sin resultados.</td></tr><?php endif; ?>
            </tbody>
        </table>

        <?php as_paginacion($p, ['q' => $q, 'ver' => $solo]); ?>
    </div>

    <?php endif; ?>
</div>
<?php as_pie(); ?>
