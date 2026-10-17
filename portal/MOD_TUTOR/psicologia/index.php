<?php
/*=========================================================
    TUTOR · ATENCIÓN PSICOLÓGICA
    1) Avisos de profesores sobre sus tutorados: canalizar o descartar.
    2) Canalizar directamente a un tutorado suyo.
    3) Ver el estado e historial de seguimiento de lo que ya canalizó (nunca las notas del psicólogo).
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('psicologia.canalizar');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/psico_vista.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$listo     = sist_psico_listo($conn);
$idFiltro  = (int) ($_GET['id'] ?? 0);

$avisos = [];
$mias   = ['total' => 0, 'filas' => []];
$p      = ['pagina' => 1, 'paginas' => 1];
$alumnos = [];

if ($listo) {
    $alumnos    = sist_psico_alumnos_de_tutor($conn, $idUsuario);
    $avisos     = sist_psico_listar($conn, 'tutor', ['id_tutor' => $idUsuario, 'estado' => 'AVISO'], 50, 0)['filas'];

    $filtros = ['id_tutor' => $idUsuario];
    if ($idFiltro > 0) {
        $filtros['id'] = $idFiltro;
    }
    $total = sist_psico_listar($conn, 'tutor', $filtros, 1, 0)['total'];
    $p     = sist_paginacion($total, 10);
    $mias  = sist_psico_listar($conn, 'tutor', $filtros, $p['limite'], $p['offset']);
}

as_cabecera('Atención psicológica');
include __DIR__ . '/../../includes_pit/sidebar_docente.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Atención psicológica</h1>
    <p class="as-sub">
        Revisa los avisos de los profesores sobre tus tutorados y canaliza al psicólogo cuando proceda.
        Al canalizar se avisa al alumno, a todos los psicólogos (el primero que tome el caso será el responsable), al administrador, al jefe de carrera y al profesor que avisó.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ps_no_instalado(); else: ?>

    <div class="as-aviso-conf">🔒 El motivo que escribas lo leen solo el psicólogo y tú. No se envía por correo ni se muestra
        al jefe de carrera, al administrador ni al alumno.</div>

    <h2>Avisos de profesores (<?= count($avisos) ?>)</h2>
    <?php if (!$avisos): ?>
        <div class="as-card"><div class="as-vacio">No tienes avisos pendientes.</div></div>
    <?php endif; ?>
    <?php foreach ($avisos as $c): ?>
        <div class="as-item <?= $idFiltro === (int) $c['id_psico'] ? 'destacada' : '' ?>">
            <?php ps_titulo($c); ?>
            <div class="as-datos">
                <div><b>Alumno:</b> <?= e($c['alumno']) ?> (<?= e($c['matricula']) ?>)</div>
                <div><b>Grupo:</b> <?= e($c['grupo']) ?></div>
                <div><b>Avisó:</b> <?= e($c['profesor_aviso'] ?: 'Profesor') ?></div>
                <div><b>Fecha del aviso:</b> <?= e(sist_fecha_corta($c['fecha_aviso'])) ?></div>
            </div>
            <div class="as-motivo"><b>Lo que observó el profesor:</b> <?= e($c['motivo_aviso']) ?></div>

            <form class="as-form" method="POST" action="acciones/canalizar.php" style="margin-top:12px">
                <?= sist_csrf_campo() ?>
                <input type="hidden" name="id_psico" value="<?= (int) $c['id_psico'] ?>">
                <div class="as-ancho">
                    <label>Motivo para el psicólogo *</label>
                    <textarea name="motivo" maxlength="1500" required placeholder="Qué sabes de la situación y por qué lo canalizas..."></textarea>
                </div>
                <div class="as-ancho"><button type="submit" class="as-btn verde">Canalizar y avisar</button></div>
            </form>

            <form class="as-form" method="POST" action="acciones/descartar.php" style="margin-top:8px"
                  onsubmit="return confirm('¿No canalizar este aviso por ahora?');">
                <?= sist_csrf_campo() ?>
                <input type="hidden" name="id_psico" value="<?= (int) $c['id_psico'] ?>">
                <div class="as-ancho">
                    <label>Si no vas a canalizar, anota por qué (solo lo ves tú)</label>
                    <input type="text" name="motivo_descarte" maxlength="300">
                </div>
                <div class="as-ancho"><button type="submit" class="as-btn sec chico">No canalizar por ahora</button></div>
            </form>
        </div>
    <?php endforeach; ?>

    <div class="as-card">
        <h2 style="margin-top:0">Canalizar directamente a un tutorado</h2>
        <?php if (!$alumnos): ?>
            <div class="as-vacio">No tienes tutorados activos.</div>
        <?php else: ?>
        <form class="as-form dos" method="POST" action="acciones/canalizar.php">
            <?= sist_csrf_campo() ?>
            <div>
                <label>Alumno *</label>
                <select name="id_tutorado" required>
                    <option value="">Selecciona...</option>
                    <?php foreach ($alumnos as $a): ?>
                        <option value="<?= (int) $a['id_tutorado'] ?>"><?= e($a['grupo']) ?> · <?= e($a['alumno']) ?> (<?= e($a['matricula']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="as-ancho">
                <label>Motivo para el psicólogo *</label>
                <textarea name="motivo" maxlength="1500" required></textarea>
            </div>
            <div class="as-ancho"><button type="submit" class="as-btn verde">Canalizar y avisar</button></div>
        </form>
        <?php endif; ?>
    </div>

    <h2>Mis canalizaciones</h2>
    <?php if (!$mias['filas']): ?>
        <div class="as-card"><div class="as-vacio">Aún no hay canalizaciones.</div></div>
    <?php endif; ?>
    <?php foreach ($mias['filas'] as $c):
        if ($c['estado'] === 'AVISO') { continue; } /* ya aparecen arriba */ ?>
        <div class="as-item <?= $idFiltro === (int) $c['id_psico'] ? 'destacada' : '' ?>">
            <?php ps_titulo($c); ?>
            <?php if ($c['estado'] !== 'DESCARTADA'): ps_datos_publicos($c); ?>
                <div class="as-motivo"><b>Tu motivo:</b> <?= e($c['motivo']) ?></div>
                <?php ps_historial($conn, (int) $c['id_psico']); ?>
            <?php else: ?>
                <div class="as-datos"><div><b>Alumno:</b> <?= e($c['alumno']) ?> (<?= e($c['matricula']) ?>)</div></div>
                <?php if (!empty($c['motivo_descarte'])): ?>
                    <div class="as-motivo"><b>Por qué no se canalizó:</b> <?= e($c['motivo_descarte']) ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    <?php as_paginacion($p); ?>

    <?php endif; ?>
</div>
<?php as_pie(); ?>
