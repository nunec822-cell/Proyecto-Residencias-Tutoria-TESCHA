<?php

require_once("../../sesion.php");

if(
    !in_array(
        "ADMIN",
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

    SELECT

        a.*,

        CONCAT(

            p.nombre,' ',

            p.apellido_p,' ',

            p.apellido_m

        ) AS tutor

    FROM actividades_tutor a

    INNER JOIN personal_academico p

        ON

        a.id_tutor=p.id_personal

    WHERE

        a.id_actividad='$id_actividad'

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
ALUMNOS
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

        ON

        t.id_tutorado=e.id_tutorado

        AND

        e.id_actividad='$id_actividad'

    WHERE

        t.grupo='$grupo'

        AND

        t.activo='1'

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

    $fila = mysqli_fetch_assoc($sql)

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

    <div class="cabecera-alumno">

        <div>

            <h3>

                <?= strtoupper(htmlspecialchars($fila['alumno'])); ?>

            </h3>

            <p>

                <strong>Matrícula:</strong>

                <?= $fila['matricula']; ?>

            </p>

        </div>

        <?php if(empty($fila['id_evidencia'])): ?>

            <span class="estado pendiente">

                ❌ Pendiente

            </span>

        <?php else: ?>

            <span class="estado entregado">

                ✅ Entregado

            </span>

        <?php endif; ?>

    </div>

    <?php if(!empty($fila['id_evidencia'])): ?>

        <p>

            <strong>Fecha de entrega:</strong>

            <?= $fila['fecha_entrega']; ?>

        </p>

        <a

            class="btn-pdf"

            href="../../MOD_TUTORADO/actividades/uploads/evidencias/<?= htmlspecialchars($fila['pdf']); ?>"

            target="_blank"

        >

            <i class="fa-solid fa-file-pdf"></i>

            Ver evidencia PDF

        </a>

    <?php endif; ?>

</div>

<?php

endwhile;

$contenido = ob_get_clean();

?>

<div class="resumen-entregas">

    <div class="dato">

        <h2>

            <?= $total; ?>

        </h2>

        <span>

            Total de alumnos

        </span>

    </div>

    <div class="dato">

        <h2>

            <?= $entregadas; ?>

        </h2>

        <span>

            Evidencias entregadas

        </span>

    </div>

    <div class="dato">

        <h2>

            <?= $pendientes; ?>

        </h2>

        <span>

            Pendientes

        </span>

    </div>

</div>

<div class="card-actividad-admin">

    <h2>

        <?= strtoupper(htmlspecialchars($actividad['titulo'])); ?>

    </h2>

    <p>

        <strong>Tutor:</strong>

        <?= strtoupper(htmlspecialchars($actividad['tutor'])); ?>

    </p>

    <p>

        <strong>Grupo:</strong>

        <?= htmlspecialchars($actividad['grupo']); ?>

    </p>

    <p>

        <strong>Fecha:</strong>

        <?= $actividad['fecha_publicacion']; ?>

    </p>

    <hr>

    <p>

        <?= nl2br(htmlspecialchars($actividad['descripcion'])); ?>

    </p>

</div>

<div class="lista-entregas">

    <?= $contenido; ?>

</div>