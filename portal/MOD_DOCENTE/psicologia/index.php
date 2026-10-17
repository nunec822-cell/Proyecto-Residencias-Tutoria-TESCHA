<?php
/*=========================================================
    PROFESOR · AVISAR AL TUTOR (atención psicológica)
    El profesor NO canaliza: avisa al tutor del alumno, que decide y canaliza.
    Aquí solo ve el estado de sus avisos; nunca el proceso psicológico.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('psicologia.avisar');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/psico_vista.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$listo     = sist_psico_listo($conn);
$idFiltro  = (int) ($_GET['id'] ?? 0);

$alumnos = [];
$lista   = ['total' => 0, 'filas' => []];
$p       = ['pagina' => 1, 'paginas' => 1];

if ($listo) {
    $alumnos = sist_psico_alumnos_de_profesor($conn, $idUsuario);
    $filtros = ['id_profesor_aviso' => $idUsuario];
    if ($idFiltro > 0) {
        $filtros['id'] = $idFiltro;
    }
    $total = sist_psico_listar($conn, 'profesor', $filtros, 1, 0)['total'];
    $p     = sist_paginacion($total, 10);
    $lista = sist_psico_listar($conn, 'profesor', $filtros, $p['limite'], $p['offset']);
}

function ps_texto_para_profesor(string $estado): string
{
    return [
        'AVISO'       => 'Enviado. El tutor aún no lo revisa.',
        'CANALIZADA'  => 'El tutor lo canalizó a atención psicológica.',
        'EN_ATENCION' => 'El tutor lo canalizó a atención psicológica.',
        'CONCLUIDA'   => 'El tutor lo canalizó a atención psicológica.',
        'DESCARTADA'  => 'El tutor lo revisó y por ahora no lo canalizó.',
    ][$estado] ?? '';
}

as_cabecera('Avisar al tutor');
include __DIR__ . '/../../includes_pit/sidebar_docente.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Avisar al tutor de un alumno</h1>
    <p class="as-sub">
        Si notas que un alumno de tus grupos podría necesitar atención psicológica, avisa a su tutor.
        El tutor decide y hace la canalización; cuando la haga, recibirás una notificación.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ps_no_instalado(); else: ?>

    <div class="as-card">
        <h2 style="margin-top:0">Nuevo aviso</h2>
        <div class="as-aviso-conf">🔒 Lo que escribas solo lo verán el tutor del alumno y, si se canaliza, el psicólogo.
            No se envía por correo ni se muestra a nadie más.</div>
        <?php if (!$alumnos): ?>
            <div class="as-vacio">No encontré alumnos en tus grupos (revisa que el administrador te haya asignado grupos).</div>
        <?php else: ?>
        <form class="as-form" method="POST" action="acciones/avisar.php">
            <?= sist_csrf_campo() ?>
            <div class="as-ancho">
                <label>Alumno *</label>
                <select name="id_tutorado" required>
                    <option value="">Selecciona...</option>
                    <?php foreach ($alumnos as $a): ?>
                        <option value="<?= (int) $a['id_tutorado'] ?>">
                            <?= e($a['grupo']) ?> · <?= e($a['alumno']) ?> (<?= e($a['matricula']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="as-ancho">
                <label>¿Qué observaste? *</label>
                <textarea name="motivo" maxlength="1500" required
                          placeholder="Cambios de conducta, ausencias, algo que el alumno te comentó..."></textarea>
            </div>
            <div class="as-ancho"><button type="submit" class="as-btn">Avisar al tutor</button></div>
        </form>
        <?php endif; ?>
    </div>

    <h2>Mis avisos</h2>
    <?php if (!$lista['filas']): ?>
        <div class="as-card"><div class="as-vacio">Aún no has enviado avisos.</div></div>
    <?php endif; ?>
    <?php foreach ($lista['filas'] as $c): ?>
        <div class="as-item <?= $idFiltro === (int) $c['id_psico'] ? 'destacada' : '' ?>">
            <header>
                <h3>Folio <?= (int) $c['id_psico'] ?> · <?= e($c['alumno']) ?></h3>
                <span class="as-estado <?= e($c['estado']) ?>"><?= e(ps_texto_para_profesor($c['estado'])) ?></span>
            </header>
            <div class="as-datos">
                <div><b>Grupo:</b> <?= e($c['grupo']) ?></div>
                <div><b>Avisado el:</b> <?= e(sist_fecha_corta($c['fecha_aviso'])) ?></div>
            </div>
            <div class="as-motivo"><b>Tu aviso:</b> <?= e($c['motivo_aviso']) ?></div>
        </div>
    <?php endforeach; ?>
    <?php as_paginacion($p); ?>

    <?php endif; ?>
</div>
<?php as_pie(); ?>
