<?php

require_once("../../sesion.php");

if(
    !in_array(
        "TUTOR",
        $_SESSION["roles"]
    )
){
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
ACTIVIDAD
=========================================*/

$id_actividad = intval(
    $_GET['id_actividad'] ?? 0
);

if($id_actividad<=0){

    echo "

    <div class='mensaje-vacio'>

        Actividad no válida.

    </div>

    ";

    exit();

}

/*=========================================
OBTENER ACTIVIDAD
=========================================*/

$sqlActividad = mysqli_query(

    $conn,

    "

    SELECT *

    FROM actividades_tutor

    WHERE id_actividad='$id_actividad'

    LIMIT 1

    "

);

if(
    mysqli_num_rows($sqlActividad)==0
){

    echo "

    <div class='mensaje-vacio'>

        La actividad no existe.

    </div>

    ";

    exit();

}

$actividad = mysqli_fetch_assoc(
    $sqlActividad
);

$grupo = $actividad['grupo'];

/*=========================================
ALUMNOS + EVIDENCIAS
=========================================*/

$sql = mysqli_query(

    $conn,

    "

    SELECT

        t.id_tutorado,

        t.matricula,

        CONCAT(

            t.apellido_p,' ',

            t.apellido_m,' ',

            t.nombre

        ) AS alumno,

        e.id_evidencia,

        e.pdf,

        e.fecha_entrega

    FROM tutorados t

    LEFT JOIN evidencias_actividades e

        ON t.id_tutorado=e.id_tutorado

        AND e.id_actividad='$id_actividad'

    WHERE

        t.grupo='$grupo'

        AND t.activo=1

    ORDER BY

        t.apellido_p,

        t.apellido_m,

        t.nombre

    "

);

$total = 0;
$entregadas = 0;
$pendientes = 0;

ob_start();

while(
    $fila =
    mysqli_fetch_assoc($sql)
):

$total++;

if(
    empty($fila['id_evidencia'])
){

    $pendientes++;

}else{

    $entregadas++;

}

?>

<div class="card-entrega">

    <div class="info-alumno">

        <h3>

            <?= strtoupper(htmlspecialchars($fila['alumno'])); ?>

        </h3>

        <p>

            <strong>Matrícula:</strong>

            <?= htmlspecialchars($fila['matricula']); ?>

        </p>

    </div>

    <?php if(empty($fila['id_evidencia'])): ?>

        <div class="estado pendiente">

            <i class="fa-solid fa-circle-xmark"></i>

            No ha entregado evidencia.

        </div>

    <?php else: ?>

        <div class="estado entregado">

            <i class="fa-solid fa-circle-check"></i>

            Evidencia entregada.

        </div>

        <p>

            <strong>Fecha:</strong>

            <?= $fila['fecha_entrega']; ?>

        </p>

        <a
            class="btn-descargar"
            href="../../MOD_TUTORADO/actividades/uploads/evidencias/<?= urlencode($fila['pdf']); ?>"
            target="_blank"
        >

            <i class="fa-solid fa-file-pdf"></i>

            Descargar evidencia

        </a>

    <?php endif; ?>

</div>

<?php

endwhile;

$contenido = ob_get_clean();

?>

<!--=====================================
DATOS PARA JAVASCRIPT
======================================-->

<div
    id="estadisticas"
    data-total="<?= $total; ?>"
    data-entregadas="<?= $entregadas; ?>"
    data-pendientes="<?= $pendientes; ?>"
    style="display:none;"
></div>

<div class="lista-entregas">

    <?= $contenido; ?>

</div>