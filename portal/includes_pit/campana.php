<?php
/*=========================================================
    CAMPANITA DE NOTIFICACIONES (se incluye dentro de cada sidebar)
    Muestra cuántas notificaciones sin leer tiene la persona y
    enlaza a la bandeja: portal/notificaciones/index.php
    Si la migración 002 aún no está aplicada, no muestra número.
=========================================================*/

require_once __DIR__ . '/../../core/bootstrap.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    require_once __DIR__ . '/../base_pit/conect_pit.php';
}

$__campana_n = isset($_SESSION['id_usuario'])
    ? sist_notificaciones_no_leidas($conn, (int) $_SESSION['id_usuario'])
    : 0;
?>
<a class="campana-pit"
   href="<?= e(PORTAL_URL) ?>/notificaciones/index.php"
   title="Notificaciones"
   aria-label="Notificaciones (<?= (int) $__campana_n ?> sin leer)">
    <i class="fas fa-bell"></i>
    <?php if ($__campana_n > 0): ?>
        <span class="campana-pit__n"><?= $__campana_n > 99 ? '99+' : (int) $__campana_n ?></span>
    <?php endif; ?>
</a>
<style>
    .campana-pit { position: relative; display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; border-radius: 50%; color: #334155; text-decoration: none; font-size: 19px; }
    .campana-pit:hover { background: rgba(0,0,0,.07); }
    .campana-pit__n { position: absolute; top: 1px; right: 0; min-width: 18px; height: 18px; padding: 0 4px;
        box-sizing: border-box; border-radius: 9px; background: #dc2626; color: #fff; font: 700 11px/18px Arial, sans-serif;
        text-align: center; }
</style>
