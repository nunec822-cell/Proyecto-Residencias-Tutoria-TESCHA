<?php
/*=========================================================
    ADMINISTRADOR · SEGUIMIENTO PSICOLÓGICO (solo lectura)
    Todas las carreras. Igual que el jefe de carrera: estado, psicólogo, fechas e historial de
    observaciones; nunca el motivo ni las notas privadas del psicólogo.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_rol('ADMIN');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/psico_vista.php';

$listo = sist_psico_listo($conn);
$vista = ($_GET['ver'] ?? 'abiertas') === 'todas' ? 'todas' : 'abiertas';
$filtros = [];
if ($vista === 'abiertas') {
    $filtros['abiertas'] = true;
}
if (!empty($_GET['id'])) {
    $filtros['id'] = (int) $_GET['id'];
    unset($filtros['abiertas']);
}

as_cabecera('Seguimiento psicológico');
include __DIR__ . '/../../includes_pit/sidebar_admin.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Seguimiento psicológico</h1>
    <p class="as-sub">
        Canalizaciones psicológicas de todas las carreras. Por confidencialidad solo se muestran fechas,
        estado y observaciones de seguimiento; no los motivos ni las notas del psicólogo.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ps_no_instalado(); else: ?>
        <div class="as-filtros">
            <a href="?ver=abiertas" class="<?= $vista === 'abiertas' && empty($_GET['id']) ? 'activo' : '' ?>">En proceso</a>
            <a href="?ver=todas" class="<?= $vista === 'todas' && empty($_GET['id']) ? 'activo' : '' ?>">Todas</a>
        </div>
        <?php ps_lista_publica($conn, $filtros, 'No hay canalizaciones psicológicas en esta vista.', (int) ($_GET['id'] ?? 0) ?: null, ['ver' => $vista]); ?>
    <?php endif; ?>
</div>
<?php as_pie(); ?>
