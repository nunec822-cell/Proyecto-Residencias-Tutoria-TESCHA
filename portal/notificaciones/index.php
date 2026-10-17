<?php
/*=========================================================
    BANDEJA DE NOTIFICACIONES (campanita)
    Cualquier persona con sesión ve SOLO sus propias notificaciones.
=========================================================*/

require_once __DIR__ . '/../../core/bootstrap.php';
require_once __DIR__ . '/../sesion.php';           // requiere inicio de sesión
include __DIR__ . '/../base_pit/conect_pit.php';
require_once __DIR__ . '/../includes_pit/asesorias_vista.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$listo     = sist_asesorias_listo($conn);

/* Cada rol conserva su propio menú */
$sidebar = 'sidebar_docente.php';
foreach ([
    'ADMIN'        => 'sidebar_admin.php',
    'JEFE_CARRERA' => 'sidebar_jefescarrera.php',
    'DIRECTIVO'    => 'sidebar_directivos.php',
    'PSICOLOGO'    => 'sidebar_psicologos.php',
    'TUTORADO'     => 'sidebar_tutorado.php',
    'DOCENTE'      => 'sidebar_docente.php',
    'TUTOR'        => 'sidebar_docente.php',
    'COORDINADOR'  => 'sidebar_docente.php',
] as $rol => $archivo) {
    if (tiene_rol($rol)) {
        $sidebar = $archivo;
        break;
    }
}

$solo   = ($_GET['ver'] ?? 'todas') === 'sin_leer' ? 'sin_leer' : 'todas';
$lista  = [];
$p      = ['pagina' => 1, 'paginas' => 1];
$sinLeer = 0;

if ($listo) {
    $sinLeer = sist_notificaciones_no_leidas($conn, $idUsuario);
    $donde   = $solo === 'sin_leer' ? ' AND leida = 0' : '';
    $total   = (int) sist_db_valor($conn, "SELECT COUNT(*) FROM notificaciones WHERE id_usuario = ?" . $donde, 'i', [$idUsuario]);
    $p       = sist_paginacion($total, 15);
    $lista   = sist_db_filas(
        $conn,
        "SELECT id_notificacion, titulo, mensaje, url, leida, fecha
         FROM notificaciones WHERE id_usuario = ?" . $donde . " ORDER BY id_notificacion DESC LIMIT ? OFFSET ?",
        'iii',
        [$idUsuario, $p['limite'], $p['offset']]
    );
}

as_cabecera('Notificaciones');
include __DIR__ . '/../includes_pit/' . $sidebar;
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Notificaciones</h1>
    <p class="as-sub">Avisos del sistema para ti. Tienes <b><?= (int) $sinLeer ?></b> sin leer.</p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ?>
        <div class="as-msg aviso">Las notificaciones aún no están instaladas (falta la migración 002).</div>
    <?php else: ?>

    <div class="as-filtros">
        <a href="?ver=todas" class="<?= $solo === 'todas' ? 'activo' : '' ?>">Todas</a>
        <a href="?ver=sin_leer" class="<?= $solo === 'sin_leer' ? 'activo' : '' ?>">Sin leer</a>
        <?php if ($sinLeer > 0): ?>
        <form method="POST" action="acciones/marcar.php" style="display:inline">
            <?= sist_csrf_campo() ?>
            <input type="hidden" name="todas" value="1">
            <button class="as-btn sec chico" type="submit">Marcar todas como leídas</button>
        </form>
        <?php endif; ?>
    </div>

    <?php if (!$lista): ?>
        <div class="as-card"><div class="as-vacio">No hay notificaciones.</div></div>
    <?php endif; ?>

    <?php foreach ($lista as $n): ?>
        <div class="as-not <?= $n['leida'] ? '' : 'nueva' ?>">
            <div>
                <h3><?= e($n['titulo']) ?></h3>
                <p><?= e($n['mensaje']) ?></p>
                <small><?= e(date('d/m/Y H:i', strtotime($n['fecha']))) ?></small>
            </div>
            <div class="acciones">
                <?php if ($n['url'] !== '' && $n['url'] !== 'notificaciones/index.php'): ?>
                    <a class="as-btn chico" href="<?= e(PORTAL_URL . '/' . ltrim($n['url'], '/')) ?>">Ver</a>
                <?php endif; ?>
                <?php if (!$n['leida']): ?>
                <form method="POST" action="acciones/marcar.php">
                    <?= sist_csrf_campo() ?>
                    <input type="hidden" name="id" value="<?= (int) $n['id_notificacion'] ?>">
                    <button class="as-btn sec chico" type="submit">Marcar leída</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php as_paginacion($p, ['ver' => $solo]); ?>

    <?php endif; ?>
</div>
<?php as_pie(); ?>
