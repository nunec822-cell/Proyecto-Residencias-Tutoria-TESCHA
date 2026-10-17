<?php
/*=========================================================
    PSICÓLOGO · SEGUIMIENTO DE CANALIZACIONES
    1) Casos por tomar (el primero que toma el caso queda como responsable).
    2) Mis casos: programar sesiones (fecha, horario y lugar), cerrarlas y concluir el proceso.
    Aquí (y solo aquí, con el tutor) se ven los motivos; las notas privadas solo las lee quien las escribió.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('psicologia.atender');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/psico_vista.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$listo     = sist_psico_listo($conn);
$idFiltro  = (int) ($_GET['id'] ?? 0);

$pool = [];
$mios = ['total' => 0, 'filas' => []];
$p    = ['pagina' => 1, 'paginas' => 1];

if ($listo) {
    $pool = sist_psico_listar($conn, 'psicologo', ['estado' => 'CANALIZADA', 'sin_psicologo' => 1], 50, 0)['filas'];
    $filtros = ['id_psicologo' => $idUsuario];
    if ($idFiltro > 0) {
        $filtros['id'] = $idFiltro;
    }
    $total = sist_psico_listar($conn, 'psicologo', $filtros, 1, 0)['total'];
    $p     = sist_paginacion($total, 10);
    $mios  = sist_psico_listar($conn, 'psicologo', $filtros, $p['limite'], $p['offset']);
}

as_cabecera('Seguimiento psicológico');
include __DIR__ . '/../../includes_pit/sidebar_psicologos.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Seguimiento de canalizaciones</h1>
    <p class="as-sub">
        Toma los casos que canalizan los tutores, programa cada sesión indicando fecha, horario y lugar
        (se avisa al alumno y a su tutor) y registra cómo fue.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ps_no_instalado(); else: ?>

    <div class="as-aviso-conf">🔒 La <b>observación</b> de cada sesión la ven el alumno, su tutor, el jefe de carrera y el administrador:
        no escribas ahí datos sensibles. Lo clínico va en la <b>nota privada</b>, que solo tú puedes leer.</div>

    <h2>Casos por tomar (<?= count($pool) ?>)</h2>
    <?php if (!$pool): ?>
        <div class="as-card"><div class="as-vacio">No hay casos esperando psicólogo.</div></div>
    <?php endif; ?>
    <?php foreach ($pool as $c): ?>
        <div class="as-item <?= $idFiltro === (int) $c['id_psico'] ? 'destacada' : '' ?>">
            <?php ps_titulo($c); ?>
            <?php ps_datos_publicos($c); ?>
            <div class="as-motivo"><b>Motivo del tutor:</b> <?= e($c['motivo']) ?></div>
            <?php if (!empty($c['motivo_aviso'])): ?>
                <div class="as-motivo"><b>Lo que observó el profesor:</b> <?= e($c['motivo_aviso']) ?></div>
            <?php endif; ?>
            <form method="POST" action="acciones/tomar.php" style="margin-top:10px">
                <?= sist_csrf_campo() ?>
                <input type="hidden" name="id_psico" value="<?= (int) $c['id_psico'] ?>">
                <button type="submit" class="as-btn verde">Tomar este caso</button>
            </form>
        </div>
    <?php endforeach; ?>

    <h2>Mis casos</h2>
    <?php if (!$mios['filas']): ?>
        <div class="as-card"><div class="as-vacio">Aún no has tomado ningún caso.</div></div>
    <?php endif; ?>
    <?php foreach ($mios['filas'] as $c):
        $idc = (int) $c['id_psico'];
        $historial = sist_psico_historial($conn, $idc);
        $notas     = sist_psico_notas_privadas($conn, $idc, $idUsuario);
        $abierto   = $c['estado'] === 'EN_ATENCION'; ?>
        <div class="as-item <?= $idFiltro === $idc ? 'destacada' : '' ?>">
            <?php ps_titulo($c); ?>
            <?php ps_datos_publicos($c); ?>
            <div class="as-motivo"><b>Motivo del tutor:</b> <?= e($c['motivo']) ?></div>
            <?php if (!empty($c['motivo_aviso'])): ?>
                <div class="as-motivo"><b>Lo que observó el profesor:</b> <?= e($c['motivo_aviso']) ?></div>
            <?php endif; ?>

            <h3 style="margin:14px 0 6px">Sesiones (<?= count($historial) ?>)</h3>
            <?php if (!$historial): ?><div class="as-vacio">Aún no programas ninguna sesión.</div><?php endif; ?>
            <?php foreach ($historial as $s): ?>
                <div class="as-nota">
                    <small><b><?= e(sist_fecha_corta($s['fecha'])) ?></b> · <?= e(sist_hora_corta($s['hora_inicio'])) ?>–<?= e(sist_hora_corta($s['hora_fin'])) ?>
                        · <?= e($s['lugar']) ?> · <?= e($s['estado']) ?></small>
                    <?php if ($s['observacion'] !== null && $s['observacion'] !== ''): ?>
                        <div><b>Observación:</b> <?= e($s['observacion']) ?></div>
                    <?php endif; ?>
                    <?php if (isset($notas[(int) $s['id_sesion']])): ?>
                        <div class="as-motivo"><b>🔒 Nota privada:</b> <?= nl2br(e($notas[(int) $s['id_sesion']])) ?></div>
                    <?php endif; ?>

                    <?php if ($s['estado'] === 'PROGRAMADA'): ?>
                        <form class="as-form dos" method="POST" action="acciones/cerrar_sesion.php" style="margin-top:8px">
                            <?= sist_csrf_campo() ?>
                            <input type="hidden" name="id_sesion" value="<?= (int) $s['id_sesion'] ?>">
                            <div>
                                <label>¿Cómo terminó? *</label>
                                <select name="estado" required>
                                    <option value="REALIZADA">Realizada</option>
                                    <option value="NO_ASISTIO">El alumno no asistió</option>
                                    <option value="CANCELADA">Cancelada</option>
                                </select>
                            </div>
                            <div class="as-ancho">
                                <label>Observación general * (visible para alumno, tutor, jefe y administrador)</label>
                                <input type="text" name="observacion" maxlength="500" required placeholder="Ej. Asistió; continúa el proceso.">
                            </div>
                            <div class="as-ancho">
                                <label>Nota privada (solo tú; solo si se realizó)</label>
                                <textarea name="nota_privada" maxlength="4000"></textarea>
                            </div>
                            <div class="as-ancho"><button type="submit" class="as-btn chico">Guardar cierre de la sesión</button></div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if ($abierto): ?>
                <h3 style="margin:14px 0 6px">Programar una sesión</h3>
                <form class="as-form dos" method="POST" action="acciones/programar_sesion.php">
                    <?= sist_csrf_campo() ?>
                    <input type="hidden" name="id_psico" value="<?= $idc ?>">
                    <div><label>Fecha *</label><input type="date" name="fecha" min="<?= e(date('Y-m-d')) ?>" required></div>
                    <div><label>Lugar *</label><input type="text" name="lugar" maxlength="150" required placeholder="Ej. Cubículo de psicología"></div>
                    <div><label>Hora de inicio *</label><input type="time" name="hora_inicio" required></div>
                    <div><label>Hora de fin *</label><input type="time" name="hora_fin" required></div>
                    <div class="as-ancho"><button type="submit" class="as-btn verde">Programar y avisar al alumno y al tutor</button></div>
                </form>

                <form method="POST" action="acciones/concluir.php" style="margin-top:12px"
                      onsubmit="return confirm('¿Concluir el proceso de atención de este alumno?');">
                    <?= sist_csrf_campo() ?>
                    <input type="hidden" name="id_psico" value="<?= $idc ?>">
                    <button type="submit" class="as-btn sec chico">Concluir el proceso</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?php as_paginacion($p); ?>

    <?php endif; ?>
</div>
<?php as_pie(); ?>
