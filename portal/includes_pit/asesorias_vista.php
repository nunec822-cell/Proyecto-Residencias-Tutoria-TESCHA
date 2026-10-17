<?php
/*=========================================================
    ASESORÍAS · piezas de pantalla compartidas
    (tarjeta de una asesoría, notas, paginación, mensajes)
    Se incluye desde las pantallas de tutor, jefe, profesor y administrador.
=========================================================*/

function as_cabecera(string $titulo): void
{
    ?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulo) ?> · PIT</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="<?= e(PORTAL_URL) ?>/includes_pit/asesorias.css">
</head>
<body>
<?php
}

function as_pie(): void
{
    echo "\n</body>\n</html>\n";
}

/** Mensajes ?ok= / ?error= / ?aviso= de la pantalla */
function as_mensajes(): void
{
    if (isset($_GET['ok'])) {
        $txt = $_GET['ok'] !== '1' && $_GET['ok'] !== '' ? $_GET['ok'] : 'Guardado correctamente.';
        echo '<div class="as-msg ok">✓ ' . e($txt) . '</div>';
    }
    if (!empty($_GET['aviso'])) {
        echo '<div class="as-msg aviso">⚠ ' . e($_GET['aviso']) . '</div>';
    }
    if (!empty($_GET['error'])) {
        echo '<div class="as-msg error">✗ ' . e($_GET['error']) . '</div>';
    }
}

function as_paginacion(array $p, array $conservar = []): void
{
    if ($p['paginas'] <= 1) {
        return;
    }
    echo '<div class="as-pag">';
    for ($i = 1; $i <= $p['paginas']; $i++) {
        $q = http_build_query(array_merge($conservar, ['pagina' => $i]));
        echo '<a href="?' . e($q) . '" class="' . ($i === $p['pagina'] ? 'activa' : '') . '">' . $i . '</a>';
    }
    echo '</div>';
}

function as_estado_texto(string $estado): string
{
    return [
        'PENDIENTE'      => 'Pendiente de asignar',
        'ASIGNADA'       => 'Asignada',
        'EN_SEGUIMIENTO' => 'En seguimiento',
        'FINALIZADA'     => 'Finalizada',
    ][$estado] ?? $estado;
}

/** Datos comunes de una asesoría (alumno, materia, tutor, profesor, horario) */
function as_datos(array $a): void
{
    ?>
    <div class="as-datos">
        <div><b>Alumno:</b> <?= e($a['alumno']) ?> (<?= e($a['matricula']) ?>)</div>
        <div><b>Carrera / grupo:</b> <?= e($a['carrera']) ?> · <?= e($a['grupo']) ?></div>
        <div><b>Materia:</b> <?= e($a['nombre_materia']) ?></div>
        <div><b>Tutor:</b> <?= e($a['tutor'] ?: '—') ?></div>
        <div><b>Canalizada el:</b> <?= e(sist_fecha_corta($a['fecha_creacion'])) ?></div>
        <?php if (!empty($a['id_profesor'])): ?>
            <div><b>Profesor asesor:</b> <?= e($a['profesor'] ?: '—') ?></div>
            <div><b>Fecha:</b> <?= e(sist_fecha_corta($a['fecha_asesoria'])) ?>
                · <?= e(sist_hora_corta($a['hora_inicio'])) ?> a <?= e(sist_hora_corta($a['hora_fin'])) ?></div>
            <?php if (!empty($a['lugar'])): ?><div><b>Lugar:</b> <?= e($a['lugar']) ?></div><?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if (!empty($a['motivo'])): ?>
        <div class="as-motivo"><b>Motivo:</b> <?= e($a['motivo']) ?></div>
    <?php endif; ?>
    <?php
}

/** Historial de notas de seguimiento (desplegable) */
function as_notas(mysqli $conn, int $idAsesoria): void
{
    $notas = sist_asesoria_notas($conn, $idAsesoria);
    if (!$notas) {
        return;
    }
    ?>
    <details class="as-notas">
        <summary>Seguimiento del profesor (<?= count($notas) ?>)</summary>
        <?php foreach ($notas as $n): ?>
            <div class="as-nota">
                <small><?= e($n['autor'] ?: 'Profesor') ?> · <?= e(date('d/m/Y H:i', strtotime($n['fecha']))) ?>
                    · <?= e(as_estado_texto($n['estado'])) ?></small>
                <?= e($n['nota']) ?>
            </div>
        <?php endforeach; ?>
    </details>
    <?php
}
