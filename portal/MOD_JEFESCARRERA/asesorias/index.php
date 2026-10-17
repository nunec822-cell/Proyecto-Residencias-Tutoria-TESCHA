<?php
/*=========================================================
    JEFE DE CARRERA · ASESORÍAS POR ASIGNAR
    Ve las canalizaciones a asesoría de los alumnos de SU carrera y asigna
    profesor, fecha, hora y lugar. Al confirmar se avisa a alumno,
    administrador, tutor y al profesor asignado.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('asesorias.asignar');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/asesorias_vista.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$listo     = sist_asesorias_listo($conn);

/* Un jefe de carrera solo ve su carrera; el administrador (sin ese rol) ve todas */
$carrera = null;
if (tiene_rol('JEFE_CARRERA')) {
    $carrera = sist_db_valor($conn, "SELECT carrera FROM personal_academico WHERE id_usuario = ? AND activo = 1", 'i', [$idUsuario]);
    $carrera = $carrera !== null ? (string) $carrera : '';
}

$vista    = ($_GET['ver'] ?? 'pendientes') === 'todas' ? 'todas' : 'pendientes';
$idFiltro = (int) ($_GET['id'] ?? 0);

$lista = ['total' => 0, 'filas' => []];
$p = ['pagina' => 1, 'paginas' => 1];
$docentes = [];

if ($listo && $carrera !== '') {
    $filtros = [];
    if ($carrera !== null) {
        $filtros['carrera'] = $carrera;
    }
    if ($idFiltro > 0) {
        $filtros['id'] = $idFiltro;
    } elseif ($vista === 'pendientes') {
        $filtros['estado'] = 'PENDIENTE';
    }
    $total = sist_asesorias_listar($conn, $filtros, 1, 0)['total'];
    $p     = sist_paginacion($total, 10);
    $lista = sist_asesorias_listar($conn, $filtros, $p['limite'], $p['offset']);

    /* Docentes activos; primero los de la carrera del jefe */
    $docentes = sist_db_filas(
        $conn,
        "SELECT pa.id_usuario, pa.carrera, " . sist_sql_nombre('pa') . " AS nombre
         FROM personal_academico pa
         JOIN usuarios u      ON u.id_usuario = pa.id_usuario AND u.activo = 1
         JOIN usuario_tipo ut ON ut.id_usuario = pa.id_usuario AND ut.activo = 1
         JOIN tipos t         ON t.id_tipo = ut.id_tipo AND t.nombre = 'DOCENTE'
         WHERE pa.activo = 1
         GROUP BY pa.id_usuario, pa.carrera, pa.nombre, pa.apellido_p, pa.apellido_m
         ORDER BY (pa.carrera = ?) DESC, pa.apellido_p, pa.apellido_m, pa.nombre",
        's',
        [(string) $carrera]
    );
}

as_cabecera('Asesorías por asignar');
include __DIR__ . '/../../includes_pit/sidebar_jefescarrera.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Asesorías por asignar</h1>
    <p class="as-sub">
        Canalizaciones a asesoría académica de los alumnos de
        <b><?= e($carrera ?? 'todas las carreras') ?></b>. Asigna al profesor, la fecha, la hora y el lugar;
        al confirmar se avisa al alumno, al administrador, al tutor y al profesor.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ?>
        <div class="as-msg error">El módulo de asesorías aún no está instalado: falta aplicar la migración
            <code>database/migraciones/002_asesorias_notificaciones.sql</code>.</div>
    <?php elseif ($carrera === ''): ?>
        <div class="as-msg error">Tu usuario no tiene una carrera registrada como personal académico; pide al administrador que la complete.</div>
    <?php else: ?>

    <div class="as-filtros">
        <a href="?ver=pendientes" class="<?= $vista === 'pendientes' && !$idFiltro ? 'activo' : '' ?>">Pendientes</a>
        <a href="?ver=todas" class="<?= $vista === 'todas' && !$idFiltro ? 'activo' : '' ?>">Todas</a>
    </div>

    <?php if (!$lista['filas']): ?>
        <div class="as-card"><div class="as-vacio">No hay asesorías <?= $vista === 'pendientes' ? 'pendientes de asignar' : 'registradas' ?>.</div></div>
    <?php endif; ?>

    <?php foreach ($lista['filas'] as $a): ?>
        <div class="as-item <?= $idFiltro === (int) $a['id_asesoria'] ? 'destacada' : '' ?>">
            <header>
                <h3>Folio <?= (int) $a['id_asesoria'] ?> · <?= e($a['nombre_materia']) ?></h3>
                <span class="as-estado <?= e($a['estado']) ?>"><?= e(as_estado_texto($a['estado'])) ?></span>
            </header>
            <?php as_datos($a); ?>

            <?php if ($a['estado'] === 'PENDIENTE'): ?>
            <form class="as-form" method="POST" action="acciones/asignar.php">
                <?= sist_csrf_campo() ?>
                <input type="hidden" name="id_asesoria" value="<?= (int) $a['id_asesoria'] ?>">
                <div class="as-ancho">
                    <label>Profesor que dará la asesoría *</label>
                    <select name="id_profesor" required>
                        <option value="">Selecciona...</option>
                        <?php foreach ($docentes as $d): ?>
                            <option value="<?= (int) $d['id_usuario'] ?>">
                                <?= e($d['nombre']) ?><?= sist_carrera_norm($d['carrera']) === sist_carrera_norm((string) $carrera) ? '' : ' — ' . e($d['carrera']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Fecha *</label>
                    <input type="date" name="fecha" min="<?= e(date('Y-m-d')) ?>" required>
                </div>
                <div>
                    <label>Hora de inicio *</label>
                    <input type="time" name="hora_inicio" required>
                </div>
                <div>
                    <label>Hora de fin *</label>
                    <input type="time" name="hora_fin" required>
                </div>
                <div>
                    <label>Lugar (aula / enlace)</label>
                    <input type="text" name="lugar" maxlength="150" placeholder="Ej.: Aula 3, Edificio B">
                </div>
                <div class="as-ancho">
                    <button type="submit" class="as-btn verde">Confirmar asignación y avisar</button>
                </div>
            </form>
            <?php endif; ?>

            <?php as_notas($conn, (int) $a['id_asesoria']); ?>
        </div>
    <?php endforeach; ?>

    <?php as_paginacion($p, ['ver' => $vista]); ?>

    <?php endif; ?>
</div>
<?php as_pie(); ?>
