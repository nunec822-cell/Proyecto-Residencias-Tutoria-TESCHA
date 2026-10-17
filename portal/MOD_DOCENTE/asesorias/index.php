<?php
/*=========================================================
    PROFESOR · MIS ASESORÍAS
    Asesorías que el jefe de carrera le asignó. Aquí registra el
    seguimiento (notas) y la marca como finalizada.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('asesorias.seguimiento');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/asesorias_vista.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$listo     = sist_asesorias_listo($conn);

$vista    = ($_GET['ver'] ?? 'abiertas') === 'todas' ? 'todas' : 'abiertas';
$idFiltro = (int) ($_GET['id'] ?? 0);

$lista = ['total' => 0, 'filas' => []];
$p = ['pagina' => 1, 'paginas' => 1];

if ($listo) {
    $filtros = ['id_profesor' => $idUsuario];
    if ($idFiltro > 0) {
        $filtros['id'] = $idFiltro;
    } elseif ($vista === 'abiertas') {
        $filtros['abiertas'] = true;
    }
    $total = sist_asesorias_listar($conn, $filtros, 1, 0)['total'];
    $p     = sist_paginacion($total, 10);
    $lista = sist_asesorias_listar($conn, $filtros, $p['limite'], $p['offset']);
}

as_cabecera('Mis asesorías');
include __DIR__ . '/../../includes_pit/sidebar_docente.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Mis asesorías</h1>
    <p class="as-sub">
        Asesorías académicas que te asignó el jefe de carrera. Registra aquí el seguimiento de cada sesión
        y márcala como finalizada cuando el alumno ya no la necesite.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ?>
        <div class="as-msg error">El módulo de asesorías aún no está instalado: falta aplicar la migración
            <code>database/migraciones/002_asesorias_notificaciones.sql</code>.</div>
    <?php else: ?>

    <div class="as-filtros">
        <a href="?ver=abiertas" class="<?= $vista === 'abiertas' && !$idFiltro ? 'activo' : '' ?>">Abiertas</a>
        <a href="?ver=todas" class="<?= $vista === 'todas' && !$idFiltro ? 'activo' : '' ?>">Todas</a>
    </div>

    <?php if (!$lista['filas']): ?>
        <div class="as-card"><div class="as-vacio">No tienes asesorías <?= $vista === 'abiertas' ? 'abiertas' : 'asignadas' ?>.</div></div>
    <?php endif; ?>

    <?php foreach ($lista['filas'] as $a): ?>
        <div class="as-item <?= $idFiltro === (int) $a['id_asesoria'] ? 'destacada' : '' ?>">
            <header>
                <h3>Folio <?= (int) $a['id_asesoria'] ?> · <?= e($a['nombre_materia']) ?></h3>
                <span class="as-estado <?= e($a['estado']) ?>"><?= e(as_estado_texto($a['estado'])) ?></span>
            </header>
            <?php as_datos($a); ?>
            <?php as_notas($conn, (int) $a['id_asesoria']); ?>

            <?php if (in_array($a['estado'], ['ASIGNADA', 'EN_SEGUIMIENTO'], true)): ?>
            <form class="as-form" method="POST" action="acciones/seguimiento.php" style="margin-top:12px">
                <?= sist_csrf_campo() ?>
                <input type="hidden" name="id_asesoria" value="<?= (int) $a['id_asesoria'] ?>">
                <div class="as-ancho">
                    <label>Nota de seguimiento *</label>
                    <textarea name="nota" maxlength="2000" required
                              placeholder="Qué se vio en la sesión, avances, tareas, si el alumno asistió..."></textarea>
                </div>
                <div class="as-ancho">
                    <label style="display:flex;gap:8px;align-items:center;font-weight:600">
                        <input type="checkbox" name="finalizar" value="1" style="width:auto">
                        Marcar la asesoría como finalizada
                    </label>
                </div>
                <div class="as-ancho">
                    <button type="submit" class="as-btn">Guardar seguimiento</button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php as_paginacion($p, ['ver' => $vista]); ?>

    <?php endif; ?>
</div>
<?php as_pie(); ?>
