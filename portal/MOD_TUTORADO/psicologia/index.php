<?php
/*=========================================================
    ALUMNO · MI SEGUIMIENTO PSICOLÓGICO
    Solo ve su propio historial (fechas y observaciones). Nunca motivos ni notas del psicólogo.
=========================================================*/

require_once __DIR__ . '/../../../core/bootstrap.php';
require_once __DIR__ . '/../../sesion.php';
requerir_permiso('psicologia.mi_seguimiento');
include __DIR__ . '/../../base_pit/conect_pit.php';
require_once __DIR__ . '/../../includes_pit/psico_vista.php';

$listo = sist_psico_listo($conn);

as_cabecera('Mi seguimiento psicológico');
include __DIR__ . '/../../includes_pit/sidebar_tutorado.php';
?>
<div class="separador"></div>

<div class="as-contenedor">
    <h1>Mi seguimiento psicológico</h1>
    <p class="as-sub">
        Aquí puedes ver cómo va tu proceso de atención psicológica. Solo se muestran las fechas de tus sesiones
        y las observaciones generales; lo que platicas con el psicólogo es confidencial.
    </p>

    <?php as_mensajes(); ?>

    <?php if (!$listo): ps_no_instalado(); else:
        ps_lista_publica(
            $conn,
            ['id_usuario_alumno' => (int) $_SESSION['id_usuario']],
            'No tienes ninguna canalización psicológica registrada.',
            (int) ($_GET['id'] ?? 0) ?: null
        );
    endif; ?>
</div>
<?php as_pie(); ?>
