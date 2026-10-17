<?php
/*=========================================================
    PLANTILLA DE MÓDULO · pantalla principal (listado + formulario)

    Cópiala a:  portal/MOD_<ROL>/<nombre_modulo>/
    (las rutas de abajo asumen esa profundidad)
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('plantilla.ver');                 // 1) QUIÉN puede entrar
include __DIR__ . '/../../base_pit/conect_pit.php';  // define $conn

/* 2) DATOS: siempre consultas preparadas y siempre paginadas */
$total = (int) $conn->query("SELECT COUNT(*) FROM plantilla_registros")->fetch_row()[0];
$p     = sist_paginacion($total, 20);

$stmt = $conn->prepare(
    "SELECT id, titulo, estado, creado_en
     FROM plantilla_registros
     ORDER BY id DESC
     LIMIT ? OFFSET ?"
);
$stmt->bind_param('ii', $p['limite'], $p['offset']);
$stmt->execute();
$registros = $stmt->get_result();

include __DIR__ . '/../../includes_pit/sidebar_admin.php';   // usa el sidebar de TU rol
?>
<link rel="stylesheet" href="assets/modulo.css">

<div class="contenedor-modulo">
    <h1>Título del módulo</h1>

    <?php if (isset($_GET['ok'])): ?><div class="mensaje ok">✓ Guardado correctamente.</div><?php endif; ?>
    <?php if (isset($_GET['error'])): ?><div class="mensaje error">✗ <?= e($_GET['error']) ?></div><?php endif; ?>

    <?php if (tiene_permiso('plantilla.crear')): ?>    <!-- 3) botones solo para quien puede usarlos -->
    <form method="POST" action="acciones/guardar.php" class="card">
        <?= sist_csrf_campo() ?>                       <!-- 4) token CSRF en TODO formulario POST -->
        <label>Título *</label>
        <input type="text" name="titulo" maxlength="150" required>
        <button type="submit">Guardar</button>
    </form>
    <?php endif; ?>

    <table class="tabla">
        <thead><tr><th>#</th><th>Título</th><th>Estado</th><th>Fecha</th></tr></thead>
        <tbody>
        <?php while ($r = $registros->fetch_assoc()): ?>
            <tr>
                <td><?= (int) $r['id'] ?></td>
                <td><?= e($r['titulo']) ?></td>      <!-- 5) e(): todo dato impreso se escapa -->
                <td><?= e($r['estado']) ?></td>
                <td><?= e($r['creado_en']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="paginacion">
        <?php for ($i = 1; $i <= $p['paginas']; $i++): ?>
            <a href="?pagina=<?= $i ?>" class="<?= $i === $p['pagina'] ? 'activa' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>
<script src="assets/modulo.js"></script>
