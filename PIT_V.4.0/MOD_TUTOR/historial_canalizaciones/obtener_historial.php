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
ID TUTORADO
=========================================*/

$id_tutorado =
mysqli_real_escape_string(
    $conn,
    $_GET['id_tutorado'] ?? 0
);

if(
    empty(
        $id_tutorado
    )
){

    echo "
    <div class='mensaje-vacio'>
        Alumno no válido.
    </div>
    ";

    exit();

}

/*=========================================
OBTENER ALUMNO
=========================================*/

$sql_alumno =
mysqli_query(
    $conn,
    "
    SELECT

        matricula,

        CONCAT(
            apellido_p,' ',
            apellido_m,' ',
            nombre
        ) AS alumno

    FROM tutorados

    WHERE id_tutorado =
    '$id_tutorado'

    LIMIT 1
    "
);

$alumno =
mysqli_fetch_assoc(
    $sql_alumno
);

/*=========================================
CANALIZACIONES CERRADAS
=========================================*/

$sql =
mysqli_query(
    $conn,
    "
    SELECT

        id_canalizacion,
        fecha_canalizacion,
        fecha_cierre,
        observaciones

    FROM canalizaciones

    WHERE
        id_tutorado =
        '$id_tutorado'

        AND

        estado =
        'CERRADO'

    ORDER BY
        id_canalizacion DESC
    "
);

if(
    mysqli_num_rows(
        $sql
    ) == 0
){

    echo "
    <div class='mensaje-vacio'>
        Este alumno no cuenta con
        canalizaciones finalizadas.
    </div>
    ";

    exit();

}

?>

<div class="encabezado-historial">

    <h2>

        <?= strtoupper(
            $alumno['alumno']
        ); ?>

    </h2>

    <p>

        <strong>
            Matrícula:
        </strong>

        <?= $alumno['matricula']; ?>

    </p>

</div>

<?php

while(
    $canal =
    mysqli_fetch_assoc(
        $sql
    )
):

?>

<div class="card-historial-canalizacion">

    <h3>

        Canalización
        #<?= $canal['id_canalizacion']; ?>

    </h3>

    <p>

        <strong>
            Fecha de Canalización:
        </strong>

        <?= $canal['fecha_canalizacion']; ?>

    </p>

    <p>

        <strong>
            Fecha de Cierre:
        </strong>

        <?= $canal['fecha_cierre']; ?>

    </p>

    <p>

        <strong>
            Observaciones:
        </strong>

        <?= $canal['observaciones']; ?>

    </p>

    <hr>

    <h4>
        Historial Anexo 14
    </h4>

    <?php

    $reportes =
    mysqli_query(
        $conn,
        "
        SELECT

            ar.*,

            m.nombre_materia

        FROM anexo14_reportes ar

        INNER JOIN materias m
            ON ar.id_materia =
            m.id_materia

        WHERE
            ar.id_canalizacion =
            '".$canal['id_canalizacion']."'

        ORDER BY
            ar.unidad
        "
    );

    while(
        $fila =
        mysqli_fetch_assoc(
            $reportes
        )
    ):

    ?>

    <div class="historial-item">

        <strong>

            <?= strtoupper(
                $fila['nombre_materia']
            ); ?>

        </strong>

        <p>

            Unidad:
            <?= $fila['unidad']; ?>

        </p>

        <ul>

            <?php if(
                $fila[
                    'competencia_no_alcanzada'
                ]
            ): ?>

                <li>
                    Competencia no alcanzada
                </li>

            <?php endif; ?>

            <?php if(
                $fila[
                    'inasistencias'
                ]
            ): ?>

                <li>
                    Inasistencias
                </li>

            <?php endif; ?>

            <?php if(
                $fila[
                    'indisciplina'
                ]
            ): ?>

                <li>
                    Indisciplina
                </li>

            <?php endif; ?>

            <?php if(
                $fila[
                    'no_entrega_trabajos'
                ]
            ): ?>

                <li>
                    No entrega de trabajos
                </li>

            <?php endif; ?>

            <?php if(
                $fila[
                    'apoyo_psicologico'
                ]
            ): ?>

                <li>
                    Apoyo psicológico
                </li>

            <?php endif; ?>

            <?php if(
                $fila[
                    'apoyo_economico'
                ]
            ): ?>

                <li>
                    Apoyo económico
                </li>

            <?php endif; ?>

            <?php if(
                !empty(
                    $fila['otro']
                )
            ): ?>

                <li>

                    <?= $fila[
                        'otro'
                    ]; ?>

                </li>

            <?php endif; ?>

        </ul>

    </div>

    <?php endwhile; ?>

</div>

<?php endwhile; ?>