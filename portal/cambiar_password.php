<?php
/*=========================================================
    PORTAL INSTITUCIONAL DE TUTORÍAS (PIT)
    Archivo: cambiar_password.php

    El usuario cambia su contraseña. Es OBLIGATORIO en el primer inicio de sesión cuando
    el administrador le asignó la contraseña (el portal no deja usar nada más hasta cambiarla),
    y cualquier usuario puede entrar a esta página cuando quiera.
=========================================================*/

require_once __DIR__ . '/../core/bootstrap.php';
requerir_login();
include __DIR__ . '/base_pit/conect_pit.php';

$idUsuario = (int) $_SESSION['id_usuario'];
$obligado  = !empty($_SESSION['debe_cambiar_password']);
$destino   = sist_destino_por_rol(sist_roles()) ?? 'login.php';
$error     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    sist_requerir_csrf();

    $r = sist_pw_cambiar(
        $conn,
        $idUsuario,
        (string) ($_POST['actual'] ?? ''),
        (string) ($_POST['nueva'] ?? ''),
        (string) ($_POST['confirmar'] ?? '')
    );

    if ($r['ok']) {
        unset($_SESSION['debe_cambiar_password']);
        session_regenerate_id(true);
        header('Location: ' . $destino);
        exit();
    }
    $error = $r['error'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña | PIT</title>
    <style>
        :root { --azul:#1b396a; --gris:#f2f4f8; --borde:#c9d1de; --rojo:#b3261e; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
               background:var(--gris); font-family:Arial,Helvetica,sans-serif; color:#1d2433; padding:16px; }
        .tarjeta { background:#fff; width:100%; max-width:440px; border-radius:12px; padding:28px;
                   box-shadow:0 6px 24px rgba(20,40,80,.12); }
        h1 { margin:0 0 6px; font-size:1.35rem; color:var(--azul); }
        p.sub { margin:0 0 18px; color:#4a5568; font-size:.95rem; line-height:1.4; }
        label { display:block; font-weight:bold; font-size:.88rem; margin:14px 0 5px; }
        input[type=password], input[type=text] { width:100%; padding:11px 12px; border:1px solid var(--borde);
               border-radius:8px; font-size:1rem; }
        input:focus { outline:2px solid var(--azul); border-color:var(--azul); }
        .reglas { font-size:.82rem; color:#5a6578; margin:6px 0 0; }
        .err { background:#fdecea; color:var(--rojo); border:1px solid #f3b9b4; padding:10px 12px;
               border-radius:8px; margin-bottom:6px; font-size:.92rem; }
        .aviso { background:#fff7e0; border:1px solid #f0d58a; padding:10px 12px; border-radius:8px;
                 margin-bottom:6px; font-size:.92rem; }
        button { margin-top:20px; width:100%; padding:12px; border:0; border-radius:8px; background:var(--azul);
                 color:#fff; font-size:1rem; font-weight:bold; cursor:pointer; }
        button:hover { filter:brightness(1.12); }
        .pie { margin-top:14px; text-align:center; font-size:.9rem; }
        .pie a { color:var(--azul); }
        .ver { font-size:.85rem; margin-top:10px; display:flex; gap:6px; align-items:center; }
    </style>
</head>
<body>
<main class="tarjeta">
    <h1>🔐 Cambiar contraseña</h1>
    <?php if ($obligado): ?>
        <p class="sub">Por seguridad, la contraseña que te asignó el administrador es temporal.
            Elige una contraseña propia para continuar.</p>
        <div class="aviso">No podrás usar el portal hasta cambiarla.</div>
    <?php else: ?>
        <p class="sub">Escribe tu contraseña actual y elige una nueva.</p>
    <?php endif; ?>

    <?php if ($error !== ''): ?><div class="err" role="alert"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" action="cambiar_password.php" autocomplete="off">
        <?= sist_csrf_campo() ?>

        <label for="actual"><?= $obligado ? 'Contraseña temporal (la que te dieron)' : 'Contraseña actual' ?></label>
        <input type="password" id="actual" name="actual" autocomplete="current-password" required maxlength="100">

        <label for="nueva">Contraseña nueva</label>
        <input type="password" id="nueva" name="nueva" autocomplete="new-password" required minlength="8" maxlength="72">
        <p class="reglas">Mínimo 8 caracteres, con letras y números. Debe ser distinta de la actual.</p>

        <label for="confirmar">Confirma la contraseña nueva</label>
        <input type="password" id="confirmar" name="confirmar" autocomplete="new-password" required maxlength="72">

        <label class="ver"><input type="checkbox" id="ver"> Mostrar contraseñas</label>

        <button type="submit">Guardar contraseña</button>
    </form>

    <div class="pie">
        <?php if ($obligado): ?>
            <a href="cerrar_sesion.php">Cerrar sesión</a>
        <?php else: ?>
            <a href="<?= e($destino) ?>">Volver al portal</a>
        <?php endif; ?>
    </div>
</main>
<script>
document.getElementById('ver').addEventListener('change', function () {
    ['actual', 'nueva', 'confirmar'].forEach(function (id) {
        document.getElementById(id).type = document.getElementById('ver').checked ? 'text' : 'password';
    });
});
</script>
</body>
</html>
