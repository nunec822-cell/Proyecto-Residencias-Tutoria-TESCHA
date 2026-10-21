<?php
require_once __DIR__ . '/../../core/bootstrap.php';

/*=========================================================
    PANEL DEL DEPARTAMENTO DE PSICOLOGÍA
    TESCHA
=========================================================*/

require_once("../sesion.php");

requerir_rol('PSICOLOGO');

require_once("../base_pit/conect_pit.php");

$base_url = "http://" . $_SERVER['HTTP_HOST'] . (PORTAL_URL . "/");


/*=========================================================
    OBTENER INFORMACIÓN DEL PSICÓLOGO
=========================================================*/

$id_usuario = $_SESSION["id_usuario"];

$sqlPsicologo = "

SELECT *

FROM administradores_psicologos

WHERE id_usuario = ?

LIMIT 1

";

$stmtPsicologo = $conn->prepare($sqlPsicologo);

$stmtPsicologo->bind_param("i",$id_usuario);

$stmtPsicologo->execute();

$resultadoPsicologo = $stmtPsicologo->get_result();

$psicologo = $resultadoPsicologo->fetch_assoc();

$id_psicologo = $psicologo["id_registro"];

$nombre_psicologo = $psicologo["nombre"];


/*=========================================================
    OBTENER AVISOS DEL PSICÓLOGO
=========================================================*/

$sqlAvisos = "

SELECT *

FROM avisos_psicologia

WHERE id_psicologo = ?

ORDER BY fecha_publicacion DESC

";

$stmtAvisos = $conn->prepare($sqlAvisos);

$stmtAvisos->bind_param("i",$id_psicologo);

$stmtAvisos->execute();

$avisos = $stmtAvisos->get_result();

/*=========================================================
    OBTENER AVISOS INSTITUCIONALES PARA PSICOLOGÍA
=========================================================*/

$sqlAvisosInstitucionales = "

SELECT 
    a.id_aviso,
    a.titulo,
    a.descripcion,
    a.link,
    a.imagen,
    a.pdf,
    a.estado,
    a.fecha_publicacion,
    a.fecha_expiracion,
    a.prioridad

FROM avisos a

INNER JOIN aviso_tipos at
    ON a.id_aviso = at.id_aviso

INNER JOIN tipos t
    ON at.id_tipo = t.id_tipo

WHERE t.id_tipo = 7

AND a.estado = 'ACTIVO'

AND (
    a.fecha_expiracion IS NULL
    OR a.fecha_expiracion >= NOW()
)

ORDER BY
    CASE a.prioridad
        WHEN 'ALTA' THEN 1
        WHEN 'MEDIA' THEN 2
        WHEN 'BAJA' THEN 3
        ELSE 4
    END,

    a.fecha_publicacion DESC

";

$stmtAvisosInstitucionales = $conn->prepare(
    $sqlAvisosInstitucionales
);

$stmtAvisosInstitucionales->execute();

$avisosInstitucionales =
    $stmtAvisosInstitucionales->get_result();

/*=========================================================
    CANALIZACIONES (resumen, gráficas y casos recientes)
=========================================================*/

$psicoListo   = sist_psico_listo($conn);
$resumenPsico = null;
$recientes    = [];

if ($psicoListo) {
    $resumenPsico = sist_psico_resumen_psicologo($conn, (int) $id_usuario);
    $recientes    = sist_psico_listar(
        $conn,
        'psicologo',
        ['id_psicologo' => (int) $id_usuario, 'abiertas' => 1],
        5,
        0
    )['filas'];
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Departamento de Psicología

</title>

<link
rel="stylesheet"
href="css/estilo.css">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- INICIO BLOQUE DE FUERZA VINO -->
<style>
    /* Forzando el degradado vino en la tarjeta principal */
    .hero {
        background: linear-gradient(135deg, #5E1914, #8A2E2E, #9B2C2C, #6E1E1E) !important;
        box-shadow: 0 30px 70px rgba(94, 25, 20, .25) !important;
    }
    
    /* Forzando los botones azules a vino */
    .btn-nuevo-aviso, .btn-publicar {
        background: linear-gradient(135deg, #8A2E2E, #5E1914) !important;
        box-shadow: 0 15px 30px rgba(94, 25, 20, .25) !important;
    }

    /* Ajustes de colores de textos para que combinen */
    .card-aviso h2, 
    .gestion-header h2, 
    .sin-avisos h3,
    .grupo label {
        color: #5E1914 !important;
    }

    /* ---------- Sección de canalizaciones ---------- */
    .canalizaciones-panel {
        margin-top: 60px;
        background: #fff;
        border-radius: 30px;
        padding: 45px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, .08);
    }
    .cp-header { display: flex; justify-content: space-between; align-items: center; gap: 30px; flex-wrap: wrap; margin-bottom: 30px; }
    .cp-header h2 { font-size: 38px; color: #5E1914; margin-bottom: 10px; }
    .cp-header p { color: #666; }
    .cp-acciones { display: flex; gap: 12px; flex-wrap: wrap; }
    .cp-acciones .btn-nuevo-aviso { text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
    .cp-btn-sec {
        display: inline-flex; align-items: center; gap: 8px; padding: 18px 28px; border-radius: 50px;
        border: 2px solid #8A2E2E; color: #8A2E2E; font-weight: 700; text-decoration: none;
    }
    .cp-btn-sec:hover { background: #fdf2f2; }
    .cp-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 16px; margin-bottom: 30px; }
    .cp-stat {
        background: #fdf2f2; border-radius: 20px; padding: 22px; display: flex; flex-direction: column; gap: 6px;
        color: #5E1914; text-decoration: none; font-weight: 600;
    }
    .cp-num { font-size: 36px; font-weight: 800; }
    .cp-graficas { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .cp-grafica, .cp-recientes { border: 1px solid #f1e3e3; border-radius: 20px; padding: 22px; }
    .cp-grafica h3, .cp-recientes h3 { color: #5E1914; margin-bottom: 16px; font-size: 18px; }
    .cp-ancha { grid-column: 1 / -1; }
    .cp-lienzo { position: relative; height: 260px; }
    .cp-ancha .cp-lienzo { height: 280px; }
    .cp-vacio { color: #888; font-size: 14px; }
    .cp-vacio a, .cp-tabla a { color: #8A2E2E; font-weight: 600; }
    .cp-tabla-wrap { overflow-x: auto; }
    .cp-tabla { width: 100%; border-collapse: collapse; font-size: 14px; }
    .cp-tabla th { text-align: left; color: #5E1914; border-bottom: 2px solid #f1e3e3; padding: 10px 8px; }
    .cp-tabla td { border-bottom: 1px solid #f6eded; padding: 10px 8px; vertical-align: top; }
    .cp-tabla small { color: #888; }
    @media (max-width: 700px) {
        .canalizaciones-panel { padding: 24px 16px; border-radius: 20px; }
        .cp-header h2 { font-size: 28px; }
        .cp-graficas { grid-template-columns: 1fr; }
    }
</style>
<!-- FIN BLOQUE DE FUERZA VINO -->

</head>

<body>

<?php include("../includes_pit/sidebar_psicologos.php"); ?>


<div class="separador"></div>


<div class="contenedor-principal">

<!--==================================================
                        HERO
===================================================-->

<section class="hero">

    <div class="hero-texto">

        <span class="badge">

            Departamento de Psicología

        </span>

        <h1>

            <span id="saludo"></span>

            <?php echo htmlspecialchars($nombre_psicologo); ?> 👋

        </h1>

        <p>

            Bienvenido al panel del Departamento de Psicología.

            Desde este espacio podrás administrar tus publicaciones,
            compartir información con la comunidad estudiantil,
            organizar tus actividades y próximamente administrar
            tus horarios de atención psicológica.

        </p>

        <div class="fecha">

            <span id="fecha"></span>

            <span id="hora"></span>

        </div>

    </div>

    <div class="hero-imagen">

        <img
        src="img/bienvenida.jpg"
        alt="Departamento de Psicología">

    </div>

</section>


<!--==================================================
                CANALIZACIONES
===================================================-->

<?php if ($psicoListo && $resumenPsico !== null) { ?>

<section class="canalizaciones-panel">

    <div class="cp-header">

        <div>

            <span class="gestion-etiqueta">🧠 Atención psicológica</span>

            <h2>Canalizaciones</h2>

            <p>Resumen de los casos canalizados y de los que tienes en atención.</p>

        </div>

        <div class="cp-acciones">

            <a href="nueva_canalizacion.php" class="btn-nuevo-aviso">
                <i class="fa-solid fa-plus"></i> Nueva canalización
            </a>

            <a href="seguimiento/index.php" class="cp-btn-sec">
                <i class="fa-solid fa-clipboard-list"></i> Ver todas
            </a>

        </div>

    </div>

    <div class="cp-stats">

        <a href="seguimiento/index.php" class="cp-stat">
            <span class="cp-num"><?php echo $resumenPsico['por_tomar']; ?></span>
            <span>Por tomar</span>
        </a>

        <a href="seguimiento/index.php" class="cp-stat">
            <span class="cp-num"><?php echo $resumenPsico['mis_activos']; ?></span>
            <span>Mis casos en atención</span>
        </a>

        <div class="cp-stat">
            <span class="cp-num"><?php echo $resumenPsico['mis_concluidos']; ?></span>
            <span>Mis casos concluidos</span>
        </div>

        <div class="cp-stat">
            <span class="cp-num"><?php echo $resumenPsico['total']; ?></span>
            <span>Canalizaciones totales</span>
        </div>

    </div>

    <?php
    /* id => [título, tipo de gráfica, datos] */
    $graficas = [
        'g-motivo'   => ['Por motivo',            'doughnut', $resumenPsico['por_motivo']],
        'g-estado'   => ['Estado de los casos',   'doughnut', $resumenPsico['por_estado']],
        'g-carrera'  => ['Por carrera',           'barh',     $resumenPsico['por_carrera']],
        'g-genero'   => ['Por género',            'pie',      $resumenPsico['por_genero']],
        'g-semestre' => ['Por semestre',          'bar',      $resumenPsico['por_semestre']],
        'g-edad'     => ['Por edad',              'bar',      $resumenPsico['por_edad']],
        'g-apoyos'   => ['Apoyo que requieren',   'bar',      $resumenPsico['apoyos']],
    ];
    ?>

    <div class="cp-graficas">

        <?php foreach ($graficas as $idGrafica => [$tituloGrafica, $tipoGrafica, $datosGrafica]) { ?>

        <div class="cp-grafica <?php echo $tipoGrafica === 'barh' ? 'cp-ancha' : ''; ?>">

            <h3><?php echo e($tituloGrafica); ?></h3>

            <?php if (!$datosGrafica) { ?>

                <p class="cp-vacio">Sin datos todavía.</p>

            <?php } else { ?>

                <div class="cp-lienzo">
                    <canvas id="<?php echo $idGrafica; ?>"></canvas>
                </div>

            <?php } ?>

        </div>

        <?php } ?>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
    (function () {
        if (typeof Chart === 'undefined') return;

        const graficas = <?php echo json_encode(
            array_map(fn($g) => ['tipo' => $g[1], 'etiquetas' => array_keys($g[2]), 'valores' => array_values($g[2])], $graficas),
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
        ); ?>;

        const paleta = ['#5E1914', '#8A2E2E', '#B84A4A', '#D97B6C', '#E8A99B', '#C9A227', '#7A6A5A', '#3F3F46'];

        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
        Chart.defaults.color = '#555';

        Object.entries(graficas).forEach(([id, g]) => {
            const lienzo = document.getElementById(id);
            if (!lienzo || !g.valores.length) return;

            const circular = g.tipo === 'doughnut' || g.tipo === 'pie';
            const horizontal = g.tipo === 'barh';

            new Chart(lienzo, {
                type: horizontal ? 'bar' : g.tipo,
                data: {
                    labels: g.etiquetas,
                    datasets: [{
                        label: 'Canalizaciones',
                        data: g.valores,
                        backgroundColor: circular ? paleta : '#8A2E2E',
                        hoverBackgroundColor: circular ? paleta : '#5E1914',
                        borderColor: '#fff',
                        borderWidth: circular ? 2 : 0,
                        borderRadius: circular ? 0 : 8,
                        maxBarThickness: 38
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: horizontal ? 'y' : 'x',
                    plugins: {
                        legend: { display: circular, position: 'bottom', labels: { boxWidth: 12, padding: 14 } }
                    },
                    scales: circular ? {} : {
                        x: { beginAtZero: true, ticks: { precision: 0 }, grid: { display: horizontal } },
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { display: !horizontal } }
                    }
                }
            });
        });
    })();
    </script>

    <div class="cp-recientes">

        <h3>Mis casos abiertos</h3>

        <?php if (!$recientes) { ?>

            <p class="cp-vacio">
                No tienes casos abiertos. Registra uno en
                <a href="nueva_canalizacion.php">Nueva canalización</a>
                o toma uno en <a href="seguimiento/index.php">Canalizaciones</a>.
            </p>

        <?php } else { ?>

            <div class="cp-tabla-wrap">
            <table class="cp-tabla">
                <thead>
                    <tr><th>Folio</th><th>Alumno</th><th>Carrera</th><th>Estado</th><th>Desde</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($recientes as $c) { ?>
                    <tr>
                        <td><?php echo (int) $c['id_psico']; ?></td>
                        <td><?php echo e($c['alumno']); ?><br><small><?php echo e($c['matricula']); ?></small></td>
                        <td><?php echo e($c['carrera']); ?></td>
                        <td><?php echo e(sist_psico_estado_texto($c['estado'])); ?></td>
                        <td><?php echo e(sist_fecha_corta($c['fecha_toma'] ?: $c['fecha_creacion'])); ?></td>
                        <td><a href="seguimiento/index.php?id=<?php echo (int) $c['id_psico']; ?>">Abrir →</a></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>

        <?php } ?>

    </div>

</section>

<?php } ?>


<!--==================================================
                GESTIÓN DE AVISOS
===================================================-->

<section class="gestion-avisos">

    <div class="gestion-header">

        <div>

            <span class="gestion-etiqueta">

                📢 Comunicación Institucional

            </span>

            <h2>

                Gestión de Avisos

            </h2>

            <p>

                Los avisos publicados desde este panel aparecerán
                automáticamente en la página institucional
                <strong>SIST Contacta a tu Psicólogo</strong>,
                permitiendo mantener informados a los estudiantes
                sobre campañas, talleres, conferencias,
                actividades y comunicados oficiales.

            </p>

        </div>

        <button
        class="btn-nuevo-aviso"
        id="abrirModal">

            <i class="fa-solid fa-plus"></i>

            Nuevo Aviso

        </button>

    </div>


    <!--==================================================
                GRID DE AVISOS
    ===================================================-->

    <div
    id="contenedorAvisos"
    class="contenedor-avisos">
    <?php

if($avisos->num_rows > 0){

    while($aviso = $avisos->fetch_assoc()){

?>

<div class="aviso-card">

    <?php if(!empty($aviso["imagen"])){ ?>

        <div class="aviso-imagen">

            <img
            src="uploads/imagenes/<?php echo htmlspecialchars($aviso["imagen"]); ?>"
            alt="Imagen del aviso">

        </div>

    <?php } ?>

    <div class="aviso-cuerpo">

        <!--=====================================
                    CABECERA
        ======================================-->

        <div class="aviso-top">

            <span class="badge-categoria">

                <?php echo htmlspecialchars($aviso["categoria"]); ?>

            </span>

            <span class="badge-estado">

                <?php echo htmlspecialchars($aviso["estado"]); ?>

            </span>

        </div>

        <!--=====================================
                    TÍTULO
        ======================================-->

        <h3>

            <?php echo htmlspecialchars($aviso["titulo"]); ?>

        </h3>

        <!--=====================================
                DESCRIPCIÓN
        ======================================-->

        <p>

            <?php echo nl2br(htmlspecialchars($aviso["descripcion"])); ?>

        </p>

        <!--=====================================
                    ARCHIVOS
        ======================================-->

        <?php

        if(
            !empty($aviso["pdf"]) ||
            !empty($aviso["enlace"])
        ){

        ?>

        <div class="aviso-recursos">

            <?php

            if(!empty($aviso["pdf"])){

            ?>

            <a
            href="uploads/imagenes/pdf/<?php echo urlencode($aviso["pdf"]); ?>"
            target="_blank"
            class="btn-pdf">

                <i class="fa-solid fa-file-pdf"></i>

                PDF

            </a>

            <?php

            }

            ?>


            <?php

            if(!empty($aviso["enlace"])){

            ?>

            <a
            href="<?php echo htmlspecialchars($aviso["enlace"]); ?>"
            target="_blank"
            class="btn-link">

                <i class="fa-solid fa-link"></i>

                Enlace

            </a>

            <?php

            }

            ?>

        </div>

        <?php } ?>

        <!--=====================================
                    PIE
        ======================================-->

        <div class="aviso-footer">

            <div class="fecha-aviso">

                <i class="fa-solid fa-calendar-days"></i>

                <?php

                echo date(
                    "d/m/Y H:i",
                    strtotime($aviso["fecha_publicacion"])
                );

                ?>

            </div>

            <div class="acciones-aviso">

                <a
                href="editar_aviso.php?id=<?php echo $aviso["id_aviso"]; ?>"
                class="btn-icon editar"
                title="Editar">

                    <i class="fa-solid fa-pen"></i>

                </a>

                <a
                href="<?= e(sist_csrf_url('eliminar_aviso.php?id=' . (int) $aviso['id_aviso'])) ?>"
                class="btn-icon eliminar"
                title="Eliminar"
                onclick="return confirm('¿Deseas eliminar este aviso?');">

                    <i class="fa-solid fa-trash"></i>

                </a>

            </div>

        </div>

    </div>

</div>

<?php

    }

}else{

?>

<div class="sin-avisos">

    <i class="fa-regular fa-folder-open"></i>

    <h3>

        No existen avisos publicados

    </h3>

    <p>

        Cuando publiques un aviso aparecerá aquí.
        Puedes comenzar haciendo clic en
        <strong>Nuevo Aviso</strong>.

    </p>

</div>

<?php

}

?>

</div>

</section>
<!--==================================================
                PANEL INFORMATIVO
===================================================-->

<section class="informacion-panel">

    <div class="info-card">

        <div class="info-icono">

            <i class="fa-solid fa-bullhorn"></i>

        </div>

        <div>

            <h3>

                Comunicación con estudiantes

            </h3>

            <p>

                Publica avisos, campañas, talleres y comunicados para
                que aparezcan automáticamente en la vista
                <strong>SIST Contacta a tu Psicólogo</strong>.

            </p>

        </div>

    </div>

    <div class="info-card">

        <div class="info-icono">

            <i class="fa-solid fa-calendar-check"></i>

        </div>

        <div>

            <h3>

                Próximamente

            </h3>

            <p>

                Muy pronto podrás administrar tus horarios disponibles
                para que los estudiantes puedan agendar citas desde
                el sistema.

            </p>

        </div>

    </div>

    <div class="info-card">

        <div class="info-icono">

            <i class="fa-solid fa-heart"></i>

        </div>

        <div>

            <h3>

                Bienestar emocional

            </h3>

            <p>

                El Departamento de Psicología busca brindar atención,
                orientación y acompañamiento a toda la comunidad
                estudiantil.

            </p>

        </div>

    </div>

</section>

<!--==================================================
        AVISOS DE DESARROLLO ACADÉMICO
===================================================-->

<section class="avisos-institucionales">

    <div class="institucional-header">

        <div class="institucional-titulo">

            <span class="institucional-etiqueta">

                <i class="fa-solid fa-building-columns"></i>

                Comunicación Institucional

            </span>

            <h2>

                Avisos de Desarrollo Académico

            </h2>

            <p>

                Comunicados enviados por el Departamento de
                Desarrollo Académico dirigidos al
                Departamento de Psicología.

            </p>

        </div>

        <div class="institucional-icono">

            <i class="fa-solid fa-bullhorn"></i>

        </div>

    </div>


    <div class="institucional-grid">

        <?php if($avisosInstitucionales->num_rows > 0): ?>

            <?php while($avisoInst = $avisosInstitucionales->fetch_assoc()): ?>

                <article class="institucional-card">

                    <!-- PRIORIDAD -->

                    <div class="institucional-card-top">

                        <span
                        class="prioridad prioridad-<?php echo strtolower($avisoInst["prioridad"]); ?>">

                            <?php if($avisoInst["prioridad"] === "ALTA"): ?>

                                <i class="fa-solid fa-circle-exclamation"></i>

                            <?php elseif($avisoInst["prioridad"] === "MEDIA"): ?>

                                <i class="fa-solid fa-circle-info"></i>

                            <?php else: ?>

                                <i class="fa-solid fa-circle"></i>

                            <?php endif; ?>

                            Prioridad
                            <?php echo htmlspecialchars($avisoInst["prioridad"]); ?>

                        </span>

                        <span class="institucional-fecha">

                            <i class="fa-regular fa-calendar"></i>

                            <?php

                            echo date(
                                "d/m/Y",
                                strtotime($avisoInst["fecha_publicacion"])
                            );

                            ?>

                        </span>

                    </div>


                    <!-- IMAGEN -->

                    <?php if(!empty($avisoInst["imagen"])): ?>

                        <div class="institucional-imagen">

                            <img
                            src="<?php echo htmlspecialchars($base_url); ?>uploads/imagenes/<?php echo htmlspecialchars($avisoInst["imagen"]); ?>"
                            alt="Imagen del aviso">

                        </div>

                    <?php endif; ?>


                    <!-- CONTENIDO -->

                    <div class="institucional-contenido">

                        <div class="institucional-badge">

                            <i class="fa-solid fa-building"></i>

                            Desarrollo Académico

                        </div>


                        <h3>

                            <?php echo htmlspecialchars($avisoInst["titulo"]); ?>

                        </h3>


                        <?php if(!empty($avisoInst["descripcion"])): ?>

                            <p>

                                <?php

                                echo nl2br(
                                    htmlspecialchars(
                                        $avisoInst["descripcion"]
                                    )
                                );

                                ?>

                            </p>

                        <?php endif; ?>


                        <!-- RECURSOS -->

                        <?php

                        if(
                            !empty($avisoInst["pdf"]) ||
                            !empty($avisoInst["link"])
                        ):

                        ?>

                            <div class="institucional-recursos">


                                <?php if(!empty($avisoInst["pdf"])): ?>

                                    <a
                                    href="<?php echo htmlspecialchars($base_url); ?>uploads/avisos/<?php echo urlencode($avisoInst["pdf"]); ?>"
                                    target="_blank"
                                    class="recurso-pdf">

                                        <i class="fa-solid fa-file-pdf"></i>

                                        Ver PDF

                                    </a>

                                <?php endif; ?>


                                <?php if(!empty($avisoInst["link"])): ?>

                                    <a
                                    href="<?php echo htmlspecialchars($avisoInst["link"]); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="recurso-link">

                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>

                                        Ver enlace

                                    </a>

                                <?php endif; ?>


                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- PIE -->

                    <div class="institucional-footer">

                        <span>

                            <i class="fa-solid fa-clock"></i>

                            Publicado el

                            <?php

                            echo date(
                                "d/m/Y H:i",
                                strtotime($avisoInst["fecha_publicacion"])
                            );

                            ?>

                        </span>

                    </div>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="sin-avisos-institucionales">

                <div class="sin-avisos-icono">

                    <i class="fa-regular fa-bell-slash"></i>

                </div>

                <h3>

                    No hay avisos institucionales

                </h3>

                <p>

                    Actualmente no existen comunicados de
                    Desarrollo Académico dirigidos al
                    Departamento de Psicología.

                </p>

            </div>

        <?php endif; ?>

    </div>

</section>
<!--==================================================
                    FRASE
===================================================-->

<section class="frase">

    "La orientación oportuna puede cambiar el rumbo de una vida."

</section>

</div>

<!--==================================================
                MODAL NUEVO AVISO
===================================================-->

<div
class="modal-aviso"
id="modalAviso">

<div class="modal-contenido">

<div class="modal-header">

<div>

<h2>

📢 Crear Nuevo Aviso

</h2>

<p>

Comparte información importante con la comunidad estudiantil.

</p>

</div>

<button
type="button"
class="cerrar-modal"
id="cerrarModal">

<i class="fa-solid fa-xmark"></i>

</button>

</div>

<form

id="formAviso"

action="guardar_aviso.php"

method="POST"

enctype="multipart/form-data"

autocomplete="off">

<div class="grupo">

<label>

Título <span>*</span>

</label>

<input

type="text"

name="titulo"

maxlength="200"

placeholder="Ej. Taller de manejo del estrés"

required>

</div>

<div class="grupo">

<label>

Descripción <span>*</span>

</label>

<textarea

name="descripcion"

rows="6"

placeholder="Escribe aquí el contenido del aviso..."

required></textarea>

</div>

<div class="fila">

<div class="grupo">

<label>

Categoría

</label>

<select name="categoria">

<option value="AVISO">

Aviso

</option>

<option value="TALLER">

Taller

</option>

<option value="CURSO">

Curso

</option>

<option value="CONFERENCIA">

Conferencia

</option>

<option value="CAMPAÑA">

Campaña

</option>

<option value="ACTIVIDAD">

Actividad

</option>

</select>

</div>

<div class="grupo">

<label>

Estado

</label>

<select name="estado">

<option value="PUBLICADO">

Publicado

</option>

<option value="BORRADOR">

Borrador

</option>

</select>

</div>

</div>

<div class="fila">

<div class="grupo">

<label>

Imagen (Opcional)

</label>

<input

type="file"

id="imagen"

name="imagen"

accept="image/png,image/jpeg,image/jpg,image/webp">

</div>

<div class="grupo">

<label>

PDF (Opcional)

</label>

<input

type="file"

id="pdf"

name="pdf"

accept=".pdf">

</div>

</div>

<div class="grupo">

<label>

Enlace (Opcional)

</label>

<input

type="url"

name="enlace"

placeholder="https://">

</div>

<div class="preview-imagen">

<img

id="preview"

src=""

alt="Vista previa"

style="display:none;">

<div id="textoPreview">

<i class="fa-regular fa-image"></i>

<p>

La vista previa de la imagen aparecerá aquí.

</p>

</div>

</div>

<div id="mensajeAviso"></div>

<div class="botones-modal">
                    <button
                type="button"
                class="btn-cancelar"
                id="cancelarModal">

                    Cancelar

                </button>

                <button
                type="submit"
                class="btn-publicar"
                id="btnGuardarAviso">

                    <i class="fa-solid fa-paper-plane"></i>

                    Publicar Aviso

                </button>

            </div>

        </form>

    </div>

</div>

<!--==================================================
                FOOTER
===================================================-->

<?php include("../../includes/footer.php"); ?>

<!--==================================================
                JAVASCRIPT
===================================================-->

<script src="js/animaciones.js"></script>

</body>

</html>