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

include(
    "../../base_pit/conect_pit.php"
);

/*=========================================
GRUPO
=========================================*/

$grupo =
mysqli_real_escape_string(
    $conn,
    $_GET['grupo'] ?? ''
);

if(
    empty(
        $grupo
    )
){

    echo "
    <div class='mensaje-vacio'>
        Seleccione un grupo.
    </div>
    ";

    exit();

}

/*=========================================
ALUMNOS DEL GRUPO
=========================================*/

$sql =
mysqli_query(
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

        COUNT(
            c.id_canalizacion
        ) AS total

    FROM tutorados t

    LEFT JOIN canalizaciones c
        ON t.id_tutorado =
        c.id_tutorado

        AND

        c.estado =
        'CERRADO'

    WHERE
        t.grupo =
        '$grupo'

    GROUP BY
        t.id_tutorado

    ORDER BY
        alumno
    "
);

if(
    mysqli_num_rows(
        $sql
    ) == 0
){

    echo "
    <div class='mensaje-vacio'>
        No existen alumnos registrados
        en este grupo.
    </div>
    ";

    exit();

}

while(
    $fila =
    mysqli_fetch_assoc(
        $sql
    )
):

?>

<div class="card-alumno">

    <h2>

        <?= strtoupper(
            $fila['alumno']
        ); ?>

    </h2>

    <p>

        <strong>
            Matrícula:
        </strong>

        <?= $fila['matricula']; ?>

    </p>

    <p>

        <strong>
            Canalizaciones Cerradas:
        </strong>

        <?= $fila['total']; ?>

    </p>

    <?php if(
        $fila['total'] > 0
    ): ?>

        <button
            class="btn-historial"
            data-id="<?= $fila['id_tutorado']; ?>"
        >

            Ver Historial

        </button>

    <?php else: ?>

        <div class="sin-historial">

            Este alumno aún no cuenta
            con canalizaciones finalizadas o nunca a tenido canalizaciones .

        </div>

    <?php endif; ?>

</div>

<?php endwhile; ?>