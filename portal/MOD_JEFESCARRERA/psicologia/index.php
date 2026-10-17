<?php
/*=========================================================
    JEFE DE CARRERA · SEGUIMIENTO PSICOLÓGICO (solo lectura)
    Ve las canalizaciones de los alumnos de SU carrera: estado, psicólogo, fechas e historial
    de observaciones. Nunca el motivo ni las notas del psicólogo.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('psicologia.ver_seguimiento');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/psico_vista.php';

$listo     = sist_psico_listo($conn);
$idUsuario = (int) $_SESSION['id_usuario'];

$filtros = [];
$sinCarrera = false;
if (tiene_rol('JEFE_CARRERA')) {
    $carrera = (string) (sist_db_valor($conn, "SELECT carrera FROM personal_academico WHERE id_usuario = ? AND activo = 1", 'i', [$idUsuario]) ?? '');
    if ($carrera === '') {
        $sinCarrera = true;
    } else {
        $filtros['carrera'] = $carrera;
    }
}
$vista = ($_GET['ver'] ?? 'abiertas') === 'todas' ? 'todas' : 'abiertas';
if ($vista === 'abiertas') {
    $filtros['abiertas'] = true;
}
if (!empty($_GET['id'])) {
    $filtros['id'] = (int) $_GET['id'];
    unset($filtros['abiertas']);
}

as_cabecera('Seguimiento psicológico');
include __DIR__ . '/../../includes_pit/sidebar_jefescarrera.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Seguimiento psicológico</h1>
    <p class="as-sub">
        Alumnos de <b><?= e($filtros['carrera'] ?? 'todas las carreras') ?></b> canalizados a atención psicológica.
        Aquí confirmas que el proceso se esté llevando a cabo; por confidencialidad no se muestran los motivos
        ni el contenido de las sesiones.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ps_no_instalado();
    elseif ($sinCarrera): ?>
        <div class="as-msg error">Tu usuario no tiene carrera registrada; pide al administrador que la capture.</div>
    <?php else: ?>
        <div class="as-filtros">
            <a href="?ver=abiertas" class="<?= $vista === 'abiertas' && empty($_GET['id']) ? 'activo' : '' ?>">En proceso</a>
            <a href="?ver=todas" class="<?= $vista === 'todas' && empty($_GET['id']) ? 'activo' : '' ?>">Todas</a>
        </div>
        <?php ps_lista_publica($conn, $filtros, 'No hay canalizaciones psicológicas en esta vista.', (int) ($_GET['id'] ?? 0) ?: null, ['ver' => $vista]); ?>
    <?php endif; ?>
</div>
<?php as_pie(); ?>
