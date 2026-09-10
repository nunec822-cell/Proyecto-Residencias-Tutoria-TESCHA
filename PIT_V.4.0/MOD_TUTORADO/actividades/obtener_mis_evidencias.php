<?php

require_once("../../sesion.php");

if(
    !in_array(
        "TUTORADO",
        $_SESSION["roles"]
    )
){
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
OBTENER TUTORADO LOGUEADO
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

$sqlTutorado = mysqli_query(
    $conn,
    "
    SELECT
        id_tutorado
    FROM tutorados
    WHERE id_usuario='$id_usuario'
    LIMIT 1
    "
);

if(mysqli_num_rows($sqlTutorado)==0){

    echo "
    <div class='mensaje-vacio'>
        No fue posible identificar al tutorado.
    </div>
    ";

    exit();

}

$tutorado = mysqli_fetch_assoc($sqlTutorado);

$id_tutorado = $tutorado['id_tutorado'];

/*=========================================
MIS EVIDENCIAS
=========================================*/

$sql = mysqli_query(
    $conn,
    "
    SELECT

        ea.id_evidencia,
        ea.pdf,
        ea.fecha_entrega,

        at.titulo,
        at.grupo

    FROM evidencias_actividades ea

    INNER JOIN actividades_tutor at
        ON ea.id_actividad =
        at.id_actividad

    WHERE

        ea.id_tutorado='$id_tutorado'

    ORDER BY

        ea.fecha_entrega DESC
    "
);

if(mysqli_num_rows($sql)==0){

    echo "

    <div class='mensaje-vacio'>

        Aún no has enviado
        evidencias.

    </div>

    ";

    exit();

}

while(
    $fila =
    mysqli_fetch_assoc($sql)
):

?>

<div class="card-evidencia">

    <div class="cabecera-evidencia">

        <div>

            <h2>

                <?= strtoupper(
                    htmlspecialchars(
                        $fila['titulo']
                    )
                ); ?>

            </h2>

            <p>

                <strong>Grupo:</strong>

                <?= $fila['grupo']; ?>

            </p>

            <p>

                <strong>Fecha de entrega:</strong>

                <?= $fila['fecha_entrega']; ?>

            </p>

        </div>

    </div>

    <div class="archivo">

        <i class="fa-solid fa-file-pdf"></i>

        <a
            href="uploads/evidencias/<?= htmlspecialchars($fila['pdf']); ?>"
            target="_blank"
        >

            Ver mi evidencia

        </a>

    </div>

</div>

<?php endwhile; ?>