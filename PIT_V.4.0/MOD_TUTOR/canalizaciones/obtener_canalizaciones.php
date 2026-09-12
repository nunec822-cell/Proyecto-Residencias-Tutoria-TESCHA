
<?php

require_once("../../sesion.php");

/*=========================================
    VALIDAR ROL TUTOR
=========================================*/

if (
    !in_array(
        "TUTOR",
        $_SESSION["roles"]
    )
) {
    exit();
}

include("../../base_pit/conect_pit.php");


/*=========================================
    OBTENER TUTOR LOGUEADO
=========================================*/

$id_usuario = $_SESSION['id_usuario'];


/*=========================================
    OBTENER ID DEL TUTOR
=========================================*/

$sql_tutor = mysqli_query(
    $conn,
    "
    SELECT
        id_personal
    FROM personal_academico
    WHERE
        id_usuario = '$id_usuario'
    LIMIT 1
    "
);


/*=========================================
    VALIDAR CONSULTA TUTOR
=========================================*/

if (!$sql_tutor) {

    die(
        "<div class='mensaje-vacio'>
            Error al obtener el tutor:
            " . htmlspecialchars(mysqli_error($conn)) . "
        </div>"
    );

}


$tutor = mysqli_fetch_assoc(
    $sql_tutor
);


/*=========================================
    VALIDAR TUTOR
=========================================*/

if (!$tutor) {

    echo "

    <div class='mensaje-vacio'>

        Tutor no encontrado.

    </div>

    ";

    exit();

}


$id_tutor = $tutor['id_personal'];


/*=========================================
    OBTENER CANALIZACIONES
=========================================*/

$sql = mysqli_query(
    $conn,
    "
    SELECT

        c.id_canalizacion,

        c.estado,

        c.observaciones,

        c.acciones,

        c.fecha_canalizacion,

        c.fecha_cierre,

        t.id_tutorado,

        t.matricula,

        t.grupo,

        CONCAT(

            t.apellido_p,
            ' ',

            t.apellido_m,
            ' ',

            t.nombre

        ) AS alumno

    FROM canalizaciones c

    INNER JOIN tutorados t

        ON c.id_tutorado =
        t.id_tutorado

    WHERE

        c.id_tutor = '$id_tutor'

        AND

        c.estado != 'CERRADO'

    ORDER BY

        c.fecha_canalizacion DESC

    "
);


/*=========================================
    VALIDAR CONSULTA CANALIZACIONES
=========================================*/

if (!$sql) {

    die(
        "<div class='mensaje-vacio'>

            Error al obtener las canalizaciones:

            <br><br>

            " .
            htmlspecialchars(
                mysqli_error($conn)
            )
            .

        "</div>"
    );

}


/*=========================================
    SIN CANALIZACIONES
=========================================*/

if (
    mysqli_num_rows($sql) == 0
) {

    echo "

    <div class='mensaje-vacio'>

        No tienes canalizaciones asignadas.

    </div>

    ";

    exit();

}


/*=========================================
    MOSTRAR CANALIZACIONES
=========================================*/

while (
    $fila = mysqli_fetch_assoc($sql)
):


/*=========================================
    CLASE ESTADO
=========================================*/

$clase_estado = "";

switch (
    $fila['estado']
) {

    case 'PENDIENTE':

        $clase_estado =
            "estado-pendiente";

    break;


    case 'EN PROCESO':

        $clase_estado =
            "estado-proceso";

    break;


    case 'ATENDIDO':

        $clase_estado =
            "estado-atendida";

    break;

}


/*=========================================
    HISTORIAL ANEXO 14
=========================================*/

$sql_historial = mysqli_query(
    $conn,
    "
    SELECT

        ar.*,

        m.nombre_materia

    FROM anexo14_reportes ar

    INNER JOIN materias m

        ON m.id_materia =
        ar.id_materia

    WHERE

        ar.id_canalizacion =
        '" . $fila['id_canalizacion'] . "'

    ORDER BY

        ar.unidad ASC

    "
);


/*=========================================
    VALIDAR HISTORIAL
=========================================*/

if (!$sql_historial) {

    die(
        "<div style='
            background:#ffe5e5;
            color:#a00000;
            padding:20px;
            margin:20px;
            border:1px solid #ff0000;
            border-radius:10px;
            font-family:Arial;
        '>

            <strong>
                Error al consultar el historial Anexo 14:
            </strong>

            <br><br>

            " .
            htmlspecialchars(
                mysqli_error($conn)
            )
            .

        "</div>"
    );

}

?>


<!--=====================================
    TARJETA DE CANALIZACIÓN
======================================-->

<div
    class="card-canalizacion
    <?= htmlspecialchars($clase_estado); ?>"
>


    <!--=====================================
        DATOS DEL ALUMNO
    ======================================-->

    <h2>

        <?= strtoupper(
            htmlspecialchars(
                $fila['alumno']
            )
        ); ?>

    </h2>


    <p>

        <strong>

            Matrícula:

        </strong>

        <?= htmlspecialchars(
            $fila['matricula']
        ); ?>

    </p>


    <p>

        <strong>

            Grupo:

        </strong>

        <?= htmlspecialchars(
            $fila['grupo']
        ); ?>

    </p>


    <!--=====================================
        HISTORIAL ANEXO 14
    ======================================-->

    <hr>


    <h3>

        Historial Anexo 14

    </h3>


    <?php

    if (
        mysqli_num_rows(
            $sql_historial
        ) == 0
    ):

    ?>

        <p class="historial-vacio">

            No existe historial académico.

        </p>


    <?php

    else:

        while (
            $historial =
            mysqli_fetch_assoc(
                $sql_historial
            )
        ):

    ?>


        <div class="historial-item">


            <!--=====================================
                MATERIA
            ======================================-->

            <strong>

                <?= strtoupper(
                    htmlspecialchars(
                        $historial[
                            'nombre_materia'
                        ]
                    )
                ); ?>

            </strong>


            <!--=====================================
                UNIDAD
            ======================================-->

            <p>

                Unidad:

                <?= htmlspecialchars(
                    $historial['unidad']
                ); ?>

            </p>


            <!--=====================================
                RIESGOS
            ======================================-->

            <ul>


                <?php if (
                    $historial[
                        'competencia_no_alcanzada'
                    ]
                ): ?>

                    <li>

                        Competencia no alcanzada

                    </li>

                <?php endif; ?>


                <?php if (
                    $historial[
                        'inasistencias'
                    ]
                ): ?>

                    <li>

                        Inasistencias

                    </li>

                <?php endif; ?>


                <?php if (
                    $historial[
                        'indisciplina'
                    ]
                ): ?>

                    <li>

                        Indisciplina

                    </li>

                <?php endif; ?>


                <?php if (
                    $historial[
                        'no_entrega_trabajos'
                    ]
                ): ?>

                    <li>

                        No entrega de trabajos

                    </li>

                <?php endif; ?>


                <?php if (
                    $historial[
                        'apoyo_psicologico'
                    ]
                ): ?>

                    <li>

                        Apoyo psicológico

                    </li>

                <?php endif; ?>


                <?php if (
                    $historial[
                        'apoyo_economico'
                    ]
                ): ?>

                    <li>

                        Apoyo económico

                    </li>

                <?php endif; ?>


                <?php if (
                    !empty(
                        $historial['otro']
                    )
                ): ?>

                    <li>

                        <?= nl2br(
                            htmlspecialchars(
                                $historial['otro']
                            )
                        ); ?>

                    </li>

                <?php endif; ?>


            </ul>


            <!--=====================================
                OBSERVACIONES DEL REPORTE
            ======================================-->

            <?php if (
                !empty(
                    $historial['observaciones']
                )
            ): ?>

                <div class="observaciones-reporte">

                    <strong>

                        Observaciones:

                    </strong>

                    <p>

                        <?= nl2br(
                            htmlspecialchars(
                                $historial[
                                    'observaciones'
                                ]
                            )
                        ); ?>

                    </p>

                </div>

            <?php endif; ?>


        </div>


    <?php

        endwhile;

    endif;

    ?>


    <hr>


    <!--=====================================
        FECHA DE CANALIZACIÓN
    ======================================-->

    <p>

        <strong>

            Fecha de canalización:

        </strong>

        <?= htmlspecialchars(
            $fila[
                'fecha_canalizacion'
            ]
        ); ?>

    </p>


    <!--=====================================
        OBSERVACIONES ADMINISTRADOR
    ======================================-->

    <div class="observaciones-admin">


        <strong>

            Observaciones del Administrador:

        </strong>


        <p>

            <?php

            if (
                !empty(
                    $fila['observaciones']
                )
            ) {

                echo nl2br(
                    htmlspecialchars(
                        $fila['observaciones']
                    )
                );

            }
            else {

                echo "Sin observaciones.";

            }

            ?>

        </p>


    </div>


    <!--=====================================
        ACCIONES DEL TUTOR
    ======================================-->

    <?php if (
        !empty(
            $fila['acciones']
        )
    ): ?>

        <div class="acciones-tutor">


            <h3>

                <i
                    class="fa-solid
                    fa-clipboard-check"
                ></i>

                Acciones que realizaré

            </h3>


            <p>

                <?= nl2br(
                    htmlspecialchars(
                        $fila['acciones']
                    )
                ); ?>

            </p>


        </div>

    <?php endif; ?>


    <!--=====================================
        ESTADO
    ======================================-->

    <p class="estado">


        <?php

        switch (
            $fila['estado']
        ) {


            case 'PENDIENTE':

                echo "🟡 PENDIENTE";

            break;


            case 'EN PROCESO':

                echo "🟠 EN PROCESO";

            break;


            case 'ATENDIDO':

                echo "🟢 ATENDIDO";

            break;


            case 'CERRADO':

                echo "⚪ CERRADO";

            break;


            default:

                echo htmlspecialchars(
                    $fila['estado']
                );

            break;

        }

        ?>


    </p>


    <!--=====================================
        TOMAR CANALIZACIÓN
    ======================================-->

    <?php if (
        $fila['estado']
        ==
        'PENDIENTE'
    ): ?>


        <button

            class="btn-tomar"

            data-id="<?= htmlspecialchars(
                $fila[
                    'id_canalizacion'
                ]
            ); ?>"

        >

            <i
                class="fa-solid
                fa-hand-pointer"
            ></i>

            Tomar Canalización

        </button>


    <?php endif; ?>


    <!--=====================================
        FINALIZAR ATENCIÓN
    ======================================-->

    <?php if (
        $fila['estado']
        ==
        'EN PROCESO'
    ): ?>


        <button

            class="btn-finalizar"

            data-id="<?= htmlspecialchars(
                $fila[
                    'id_canalizacion'
                ]
            ); ?>"

        >

            <i
                class="fa-solid
                fa-check"
            ></i>

            Finalizar Atención

        </button>


    <?php endif; ?>


    <!--=====================================
        ATENDIDO
    ======================================-->

    <?php if (
        $fila['estado']
        ==
        'ATENDIDO'
    ): ?>


        <div class="mensaje-ok">


            <i
                class="fa-solid
                fa-circle-check"
            ></i>


            Atención registrada correctamente.


            <br><br>


            Esperando que el Administrador
            cierre la canalización.


        </div>


    <?php endif; ?>


</div>


<?php

endwhile;

?>

