<?php
/*=========================================================
    CANALIZACIÓN PSICOLÓGICA · piezas de pantalla compartidas
    (reutiliza as_cabecera / as_mensajes / as_paginacion de asesorias_vista.php)

    Las funciones ps_* que imprimen datos SENSIBLES (ps_motivos) solo se llaman desde las
    pantallas del tutor, el psicólogo y el profesor que avisó. Las pantallas públicas
    (alumno, jefe de carrera, administrador) solo usan ps_datos_publicos() y ps_historial().
=========================================================*/

require_once __DIR__ . '/asesorias_vista.php';

/** Datos que puede ver cualquier perfil autorizado (sin motivos ni notas) */
function ps_datos_publicos(array $c): void
{
    ?>
    <div class="as-datos">
        <div><b>Alumno:</b> <?= e($c['alumno']) ?> (<?= e($c['matricula']) ?>)</div>
        <div><b>Carrera / grupo:</b> <?= e($c['carrera']) ?> · <?= e($c['grupo']) ?></div>
        <div><b>Tutor:</b> <?= e($c['tutor'] ?: '—') ?></div>
        <div><b>Psicólogo:</b> <?= e($c['psicologo'] ?: '—') ?></div>
        <?php if (!empty($c['fecha_canalizacion'])): ?>
            <div><b>Canalizada el:</b> <?= e(sist_fecha_corta($c['fecha_canalizacion'])) ?></div>
        <?php endif; ?>
        <?php if (!empty($c['fecha_cierre']) && $c['estado'] === 'CONCLUIDA'): ?>
            <div><b>Concluida el:</b> <?= e(sist_fecha_corta($c['fecha_cierre'])) ?></div>
        <?php endif; ?>
    </div>
    <?php
}

/** Historial de seguimiento: fecha, horario, lugar, estado y observación general (nunca notas privadas) */
function ps_historial(mysqli $conn, int $idPsico): void
{
    $h = sist_psico_historial($conn, $idPsico);
    $textos = ['PROGRAMADA' => 'Programada', 'REALIZADA' => 'Realizada', 'NO_ASISTIO' => 'No asistió', 'CANCELADA' => 'Cancelada'];
    ?>
    <details class="as-notas" <?= $h ? 'open' : '' ?>>
        <summary>Historial de seguimiento (<?= count($h) ?>)</summary>
        <?php if (!$h): ?>
            <div class="as-nota"><small>Aún no hay sesiones programadas.</small></div>
        <?php endif; ?>
        <?php foreach ($h as $s): ?>
            <div class="as-nota">
                <small><?= e(sist_fecha_corta($s['fecha'])) ?> · <?= e(sist_hora_corta($s['hora_inicio'])) ?>–<?= e(sist_hora_corta($s['hora_fin'])) ?>
                    · <?= e($s['lugar']) ?> · <?= e($textos[$s['estado']] ?? $s['estado']) ?>
                    · <?= e($s['psicologo'] ?: 'Psicólogo') ?></small>
                <?= e($s['observacion'] ?? '') ?>
            </div>
        <?php endforeach; ?>
    </details>
    <?php
}

/** Encabezado de una tarjeta */
function ps_titulo(array $c): void
{
    ?>
    <header>
        <h3>Folio <?= (int) $c['id_psico'] ?> · <?= e($c['alumno']) ?></h3>
        <span class="as-estado <?= e($c['estado']) ?>"><?= e(sist_psico_estado_texto($c['estado'])) ?></span>
    </header>
    <?php
}

function ps_no_instalado(): void
{
    ?>
    <div class="as-msg error">El módulo aún no está instalado: falta aplicar las migraciones
        <code>002_asesorias_notificaciones.sql</code> y <code>003_canalizacion_psicologica.sql</code>
        (carpeta <code>database/migraciones</code>).</div>
    <?php
}

/** Lista de solo lectura (alumno, jefe de carrera, administrador): sin datos sensibles */
function ps_lista_publica(mysqli $conn, array $filtros, string $textoVacio, ?int $destacar = null, array $conservar = []): void
{
    $filtros['solo_canalizadas'] = true;
    $total = sist_psico_listar($conn, 'publico', $filtros, 1, 0)['total'];
    $p     = sist_paginacion($total, 10);
    $lista = sist_psico_listar($conn, 'publico', $filtros, $p['limite'], $p['offset']);

    if (!$lista['filas']) {
        echo '<div class="as-card"><div class="as-vacio">' . e($textoVacio) . '</div></div>';
    }
    foreach ($lista['filas'] as $c): ?>
        <div class="as-item <?= $destacar === (int) $c['id_psico'] ? 'destacada' : '' ?>">
            <?php ps_titulo($c); ?>
            <?php ps_datos_publicos($c); ?>
            <?php ps_historial($conn, (int) $c['id_psico']); ?>
        </div>
    <?php endforeach;
    as_paginacion($p, $conservar);
}
