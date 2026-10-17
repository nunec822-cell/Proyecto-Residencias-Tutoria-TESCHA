<?php
/*=========================================================
    ADMINISTRADOR / DIRECTIVOS · ASESORÍAS (solo consulta)
    Ve todas las canalizaciones a asesoría con su estado, profesor,
    horario y seguimiento. No modifica nada.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('asesorias.ver_todas');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/asesorias_vista.php';

$listo    = sist_asesorias_listo($conn);
$estado   = (string) ($_GET['estado'] ?? '');
$estado   = in_array($estado, ['PENDIENTE', 'ASIGNADA', 'EN_SEGUIMIENTO', 'FINALIZADA'], true) ? $estado : '';
$idFiltro = (int) ($_GET['id'] ?? 0);

$lista = ['total' => 0, 'filas' => []];
$p = ['pagina' => 1, 'paginas' => 1];

if ($listo) {
    $filtros = [];
    if ($idFiltro > 0) {
        $filtros['id'] = $idFiltro;
    } elseif ($estado !== '') {
        $filtros['estado'] = $estado;
    }
    $total = sist_asesorias_listar($conn, $filtros, 1, 0)['total'];
    $p     = sist_paginacion($total, 10);
    $lista = sist_asesorias_listar($conn, $filtros, $p['limite'], $p['offset']);
}

as_cabecera('Asesorías');
include __DIR__ . '/../../includes_pit/' . (tiene_rol('ADMIN') ? 'sidebar_admin.php' : 'sidebar_directivos.php');
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Asesorías académicas</h1>
    <p class="as-sub">Consulta de todas las canalizaciones a asesoría: quién las canalizó, a quién se asignaron y cómo va el seguimiento.</p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ?>
        <div class="as-msg error">El módulo de asesorías aún no está instalado: falta aplicar la migración
            <code>database/migraciones/002_asesorias_notificaciones.sql</code>.</div>
    <?php else: ?>

    <div class="as-filtros">
        <a href="?" class="<?= $estado === '' && !$idFiltro ? 'activo' : '' ?>">Todas</a>
        <?php foreach (['PENDIENTE', 'ASIGNADA', 'EN_SEGUIMIENTO', 'FINALIZADA'] as $est): ?>
            <a href="?estado=<?= e($est) ?>" class="<?= $estado === $est && !$idFiltro ? 'activo' : '' ?>"><?= e(as_estado_texto($est)) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (!$lista['filas']): ?>
        <div class="as-card"><div class="as-vacio">No hay asesorías con ese filtro.</div></div>
    <?php endif; ?>

    <?php foreach ($lista['filas'] as $a): ?>
        <div class="as-item <?= $idFiltro === (int) $a['id_asesoria'] ? 'destacada' : '' ?>">
            <header>
                <h3>Folio <?= (int) $a['id_asesoria'] ?> · <?= e($a['nombre_materia']) ?></h3>
                <span class="as-estado <?= e($a['estado']) ?>"><?= e(as_estado_texto($a['estado'])) ?></span>
            </header>
            <?php as_datos($a); ?>
            <?php as_notas($conn, (int) $a['id_asesoria']); ?>
        </div>
    <?php endforeach; ?>

    <?php as_paginacion($p, $estado !== '' ? ['estado' => $estado] : []); ?>

    <?php endif; ?>
</div>
<?php as_pie(); ?>
