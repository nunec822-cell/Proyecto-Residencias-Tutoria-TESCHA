<?php
/*=========================================================
    PSICÓLOGO · NUEVA CANALIZACIÓN
    El psicólogo registra a un alumno (por matrícula). El caso queda
    "En atención" a su nombre y aparece en Inicio y en Canalizaciones.
=========================================================*/

require_once __DIR__ . '/../../core/bootstrap.php';
require_once __DIR__ . '/../sesion.php';
requerir_permiso('psicologia.atender');
include __DIR__ . '/../base_pit/conect_pit.php';
require_once __DIR__ . '/../includes_pit/psico_vista.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$listo     = sist_psico_listo($conn) && sist_psico_datos_extra_listos($conn);
$error     = '';
$previo    = [];

if ($listo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    sist_requerir_csrf();
    $previo = $_POST;
    $r = sist_psico_registrar_por_psicologo($conn, $idUsuario, $_POST);

    if ($r['ok']) {
        sist_correos_procesar($conn);   /* después del commit */
        header('Location: nueva_canalizacion.php?ok=' . urlencode(
            'Canalización registrada (folio ' . $r['id'] . ' · ' . $r['alumno'] . '). Ya aparece en tu inicio y en Canalizaciones.'
        ));
        exit();
    }
    $error = $r['error'];
}

$alumnos = $listo ? sist_psico_alumnos_activos($conn) : [];
$mios    = $listo ? sist_psico_listar($conn, 'psicologo', ['id_psicologo' => $idUsuario], 15, 0) : ['total' => 0, 'filas' => []];

$v = fn(string $k) => e($previo[$k] ?? '');

as_cabecera('Nueva canalización');
include __DIR__ . '/../includes_pit/sidebar_psicologos.php';
?>
<style>
    .nc-apoyos { display:flex; flex-wrap:wrap; gap:18px; align-items:center; font-size:14px; }
    .nc-apoyos label { display:flex; align-items:center; gap:6px; font-weight:500; margin:0; }
    .nc-apoyos input { width:auto !important; }
    .nc-alumno { font-size:13px; color:#5E1914; margin-top:4px; min-height:18px; }
    .as-btn.vino { background:linear-gradient(135deg,#8A2E2E,#5E1914); }
</style>

<div class="separador"></div>

<div class="as-contenedor">
    <h1>Nueva canalización</h1>
    <p class="as-sub">
        Registra a un estudiante por su matrícula. El caso queda <b>en atención a tu nombre</b>, se avisa al alumno
        y a su tutor, y aparece en tu <a href="inicio.php">inicio</a> y en
        <a href="seguimiento/index.php">Canalizaciones</a>, donde programas sus sesiones.
    </p>

    <?php as_mensajes(); ?>
    <?php if ($error !== ''): ?><div class="as-msg error">✗ <?= e($error) ?></div><?php endif; ?>

    <?php if (!$listo): ?>
        <div class="as-msg error">Falta aplicar las migraciones <code>003_canalizacion_psicologica.sql</code> y
            <code>005_canalizacion_datos_psicologo.sql</code> (carpeta <code>database/migraciones</code>).</div>
    <?php else: ?>

    <div class="as-card">
        <h2>Datos del estudiante</h2>
        <form class="as-form dos" method="POST" autocomplete="off">
            <?= sist_csrf_campo() ?>

            <div>
                <label for="matricula">Matrícula *</label>
                <input type="text" id="matricula" name="matricula" list="lista-alumnos" required value="<?= $v('matricula') ?>">
                <datalist id="lista-alumnos">
                    <?php foreach ($alumnos as $a): ?>
                        <option value="<?= e($a['matricula']) ?>"><?= e($a['alumno'] . ' · ' . $a['carrera'] . ' · ' . $a['grupo']) ?></option>
                    <?php endforeach; ?>
                </datalist>
                <div class="nc-alumno" id="nc-alumno"></div>
            </div>

            <div>
                <label for="categoria_motivo">Motivo *</label>
                <select id="categoria_motivo" name="categoria_motivo" required>
                    <option value="">-- Selecciona --</option>
                    <?php foreach (SIST_PSICO_CATEGORIAS as $cat): ?>
                        <option <?= ($previo['categoria_motivo'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="generacion">Generación</label>
                <input type="text" id="generacion" name="generacion" placeholder="2024-2028" maxlength="20" value="<?= $v('generacion') ?>">
            </div>

            <div>
                <label for="semestre">Semestre</label>
                <input type="number" id="semestre" name="semestre" min="1" max="14" value="<?= $v('semestre') ?>">
            </div>

            <div>
                <label for="genero">Género</label>
                <select id="genero" name="genero">
                    <option value="">-- Sin especificar --</option>
                    <?php foreach (['M' => 'Masculino', 'F' => 'Femenino', 'Otro' => 'Otro'] as $val => $txt): ?>
                        <option value="<?= $val ?>" <?= ($previo['genero'] ?? '') === $val ? 'selected' : '' ?>><?= $txt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="edad">Edad</label>
                <input type="number" id="edad" name="edad" min="14" max="99" value="<?= $v('edad') ?>">
            </div>

            <div class="as-ancho">
                <label>Apoyo que requiere</label>
                <div class="nc-apoyos">
                    <label><input type="checkbox" name="requiere_familiar" <?= !empty($previo['requiere_familiar']) ? 'checked' : '' ?>> Familiar</label>
                    <label><input type="checkbox" name="requiere_depresion" <?= !empty($previo['requiere_depresion']) ? 'checked' : '' ?>> Depresión</label>
                    <label><input type="checkbox" name="requiere_psiquiatrico" <?= !empty($previo['requiere_psiquiatrico']) ? 'checked' : '' ?>> Psiquiátrico</label>
                </div>
            </div>

            <div class="as-ancho">
                <label for="observaciones">🔒 Observaciones (confidencial: solo las ves tú; el tutor solo ve el motivo general)</label>
                <textarea id="observaciones" name="observaciones" maxlength="1500" rows="3"><?= $v('observaciones') ?></textarea>
            </div>

            <div class="as-ancho">
                <button type="submit" class="as-btn vino">Guardar canalización</button>
            </div>
        </form>
    </div>

    <h2>Mis canalizaciones (<?= (int) $mios['total'] ?>)</h2>
    <div class="as-card">
        <?php if (!$mios['filas']): ?>
            <div class="as-vacio">Aún no tienes canalizaciones.</div>
        <?php else: ?>
            <table class="as-tabla">
                <thead>
                    <tr><th>Folio</th><th>Alumno</th><th>Carrera</th><th>Estado</th><th>Fecha</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($mios['filas'] as $c): ?>
                    <tr>
                        <td><?= (int) $c['id_psico'] ?></td>
                        <td><?= e($c['alumno']) ?><br><small><?= e($c['matricula']) ?></small></td>
                        <td><?= e($c['carrera']) ?></td>
                        <td><span class="as-estado <?= e($c['estado']) ?>"><?= e(sist_psico_estado_texto($c['estado'])) ?></span></td>
                        <td><?= e(sist_fecha_corta($c['fecha_toma'] ?: $c['fecha_creacion'])) ?></td>
                        <td><a class="as-btn chico sec" href="seguimiento/index.php?id=<?= (int) $c['id_psico'] ?>">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($mios['total'] > count($mios['filas'])): ?>
                <p style="margin-top:10px"><a href="seguimiento/index.php">Ver todas en Canalizaciones →</a></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php endif; ?>
</div>

<script>
/* Muestra el nombre del alumno al escribir una matrícula conocida (lee la misma lista del datalist) */
(function () {
    const input = document.getElementById('matricula');
    const info  = document.getElementById('nc-alumno');
    if (!input || !info) return;
    const alumnos = new Map(
        Array.from(document.querySelectorAll('#lista-alumnos option'), o => [o.value, o.textContent])
    );
    const pintar = () => {
        const m = input.value.trim();
        info.textContent = alumnos.has(m) ? '✓ ' + alumnos.get(m) : (m ? 'Matrícula no encontrada' : '');
    };
    input.addEventListener('input', pintar);
    pintar();
})();
</script>
<?php as_pie(); ?>
