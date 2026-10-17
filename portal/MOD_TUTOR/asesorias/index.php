<?php
/*=========================================================
    TUTOR · CANALIZAR A ASESORÍA ACADÉMICA
    El tutor canaliza a un alumno suyo a asesoría en una materia.
    Avisa a: jefe de carrera, alumno y administradores.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('asesorias.canalizar');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/asesorias_vista.php';

$idTutor = (int) $_SESSION['id_usuario'];
$listo   = sist_asesorias_listo($conn);

$alumnos = [];
$materiasPorAlumno = [];
$lista = ['total' => 0, 'filas' => []];
$p = ['pagina' => 1, 'paginas' => 1];
$idFiltro = (int) ($_GET['id'] ?? 0);

if ($listo) {
    $alumnos = sist_db_filas(
        $conn,
        "SELECT id_tutorado, matricula, carrera, grupo, " . sist_sql_nombre('tutorados') . " AS nombre
         FROM tutorados WHERE id_tutor = ? AND activo = 1 ORDER BY apellido_p, apellido_m, nombre",
        'i',
        [$idTutor]
    );

    $cache = [];
    foreach ($alumnos as $al) {
        $k = sist_carrera_norm($al['carrera']);
        if (!isset($cache[$k])) {
            $cache[$k] = array_map(
                fn($m) => ['id' => (int) $m['id_materia'], 'nombre' => $m['nombre_materia']],
                sist_materias_de_carrera($conn, $al['carrera'])
            );
        }
        $materiasPorAlumno[(int) $al['id_tutorado']] = $cache[$k];
    }

    $filtros = ['id_tutor' => $idTutor];
    if ($idFiltro > 0) {
        $filtros['id'] = $idFiltro;
    }
    $total = sist_asesorias_listar($conn, $filtros, 1, 0)['total'];
    $p     = sist_paginacion($total, 10);
    $lista = sist_asesorias_listar($conn, $filtros, $p['limite'], $p['offset']);
}

as_cabecera('Canalizar a asesoría');
include __DIR__ . '/../../includes_pit/sidebar_docente.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Canalizar a asesoría académica</h1>
    <p class="as-sub">
        Si un alumno tutorado necesita apoyo en una materia, regístralo aquí. Se avisará al
        jefe de carrera, al alumno y al administrador; el jefe de carrera asignará al profesor,
        la fecha y la hora, y tú recibirás el aviso cuando quede asignada.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ?>
        <div class="as-msg error">El módulo de asesorías aún no está instalado: falta aplicar la migración
            <code>database/migraciones/002_asesorias_notificaciones.sql</code>.</div>
    <?php else: ?>

    <div class="as-card">
        <h2>Nueva canalización</h2>
        <?php if (!$alumnos): ?>
            <div class="as-vacio">No tienes alumnos tutorados activos asignados.</div>
        <?php else: ?>
        <form class="as-form dos" method="POST" action="acciones/guardar.php" id="formAsesoria">
            <?= sist_csrf_campo() ?>
            <div>
                <label for="id_tutorado">Alumno *</label>
                <select name="id_tutorado" id="id_tutorado" required>
                    <option value="">Selecciona...</option>
                    <?php foreach ($alumnos as $al): ?>
                        <option value="<?= (int) $al['id_tutorado'] ?>">
                            <?= e($al['nombre']) ?> — <?= e($al['matricula']) ?> (<?= e($al['grupo']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="id_materia">Materia *</label>
                <select name="id_materia" id="id_materia" required disabled>
                    <option value="">Primero elige al alumno</option>
                </select>
            </div>
            <div class="as-ancho">
                <label for="motivo">Motivo / observaciones (opcional)</label>
                <textarea name="motivo" id="motivo" maxlength="1000"
                          placeholder="Ej.: reprobó la unidad 2, no entiende subconsultas..."></textarea>
            </div>
            <div class="as-ancho">
                <button type="submit" class="as-btn">Canalizar a asesoría</button>
            </div>
        </form>
        <script>
            (function () {
                var materias = <?= json_encode($materiasPorAlumno, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
                var selA = document.getElementById('id_tutorado');
                var selM = document.getElementById('id_materia');
                selA.addEventListener('change', function () {
                    var lista = materias[selA.value] || [];
                    selM.innerHTML = '';
                    var op0 = document.createElement('option');
                    op0.value = '';
                    op0.textContent = lista.length ? 'Selecciona...' : 'No hay materias activas para su carrera';
                    selM.appendChild(op0);
                    lista.forEach(function (m) {
                        var op = document.createElement('option');
                        op.value = m.id;
                        op.textContent = m.nombre;
                        selM.appendChild(op);
                    });
                    selM.disabled = lista.length === 0;
                });
            })();
        </script>
        <?php endif; ?>
    </div>

    <div class="as-card">
        <h2>Mis canalizaciones a asesoría
            <?php if ($idFiltro): ?> <a class="as-btn sec chico" href="index.php">Ver todas</a><?php endif; ?></h2>

        <?php if (!$lista['filas']): ?>
            <div class="as-vacio">Aún no has canalizado alumnos a asesoría.</div>
        <?php endif; ?>

        <?php foreach ($lista['filas'] as $a): ?>
            <div class="as-item <?= $idFiltro === (int) $a['id_asesoria'] ? 'destacada' : '' ?>">
                <header>
                    <h3>Folio <?= (int) $a['id_asesoria'] ?> · <?= e($a['nombre_materia']) ?></h3>
                    <span class="as-estado <?= e($a['estado']) ?>"><?= e(as_estado_texto($a['estado'])) ?></span>
                </header>
                <?php as_datos($a); ?>
                <?php as_notas($conn, (int) $a['id_asesoria']); ?>
            </div>
        <?php endforeach; ?>

        <?php as_paginacion($p, $idFiltro ? ['id' => $idFiltro] : []); ?>
    </div>

    <?php endif; ?>
</div>
<?php as_pie(); ?>
