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

include(
    "../../base_pit/conect_pit.php"
);


/*=========================================
FILTROS
=========================================*/

$carrera =
$_GET['carrera']
?? '';

$grupo =
$_GET['grupo']
?? '';


if(
    empty($carrera)
    ||
    empty($grupo)
){

    exit();

}


/*=========================================
MATERIAS DE LA CARRERA
=========================================*/

$materias = [];


$sql_materias =
mysqli_query(
    $conn,
    "
    SELECT

        id_materia,
        nombre_materia

    FROM materias

    WHERE

        carrera='$carrera'

        AND

        estado='ACTIVA'

    ORDER BY
        nombre_materia
    "
);


while(
    $materia =
    mysqli_fetch_assoc(
        $sql_materias
    )
){

    $materias[] =
    $materia;

}


/*=========================================
ALUMNOS
=========================================*/

$sql_alumnos =
mysqli_query(
    $conn,
    "
    SELECT *

    FROM tutorados

    WHERE

        grupo='$grupo'

        AND

        activo=1

    ORDER BY

        apellido_p,
        apellido_m,
        nombre
    "
);


if(
    mysqli_num_rows(
        $sql_alumnos
    ) == 0
){

    echo "

        <div class='mensaje-vacio'>

            No existen alumnos.

        </div>

    ";

    exit();

}

?>

<h2>

    Grupo:
    <?= htmlspecialchars($grupo); ?>

</h2>


<table class="tabla-anexo">

    <thead>

        <tr>

            <th>
                Matrícula
            </th>

            <th>
                Alumno
            </th>

            <th>
                Semáforo
            </th>


            <?php foreach(
                $materias
                as
                $materia
            ): ?>

                <th>

                    <?= strtoupper(
                        htmlspecialchars(
                            $materia[
                                'nombre_materia'
                            ]
                        )
                    ); ?>

                </th>

            <?php endforeach; ?>

        </tr>

    </thead>


    <tbody>


        <?php while(
            $alumno =
            mysqli_fetch_assoc(
                $sql_alumnos
            )
        ): ?>


        <?php

        $id_tutorado =
        $alumno[
            'id_tutorado'
        ];


/*=========================================
OBTENER ÚLTIMO CIERRE
=========================================*/

$ultima =
mysqli_query(
    $conn,
    "
    SELECT

        fecha_cierre

    FROM canalizaciones

    WHERE

        id_tutorado='$id_tutorado'

        AND

        estado='CERRADO'

    ORDER BY

        id_canalizacion DESC

    LIMIT 1
    "
);


$datos_fecha =
mysqli_fetch_assoc(
    $ultima
);


/*=========================================
CALCULAR RIESGO
=========================================*/

if(
    !empty(
        $datos_fecha[
            'fecha_cierre'
        ]
    )
){

    $consulta =
    mysqli_query(
        $conn,
        "
        SELECT

            COUNT(
                DISTINCT unidad
            ) AS total

        FROM anexo14_reportes

        WHERE

            id_tutorado='$id_tutorado'

            AND

            fecha_reporte >

            '".$datos_fecha[
                'fecha_cierre'
            ]."'
        "
    );

}
else{

    $consulta =
    mysqli_query(
        $conn,
        "
        SELECT

            COUNT(
                DISTINCT unidad
            ) AS total

        FROM anexo14_reportes

        WHERE

            id_tutorado='$id_tutorado'
        "
    );

}


$resultado =
mysqli_fetch_assoc(
    $consulta
);


$total =
$resultado['total'];


/*=========================================
DETERMINAR SEMÁFORO
=========================================*/

if(
    $total == 0
){

    $riesgo =
    "🟢";

}
elseif(
    $total == 1
){

    $riesgo =
    "🟡";

}
elseif(
    $total == 2
){

    $riesgo =
    "🟠";

}
else{

    $riesgo =
    "🔴";

}


/*=========================================
OBTENER ÚLTIMA CANALIZACIÓN
=========================================*/

$consulta_estado =
mysqli_query(
    $conn,
    "
    SELECT

        id_canalizacion,
        estado,
        acciones,
        nuevos_reportes

    FROM canalizaciones

    WHERE

        id_tutorado='$id_tutorado'

    ORDER BY

        id_canalizacion DESC

    LIMIT 1
    "
);


/*=========================================
VALIDAR CONSULTA
=========================================*/

if(
    $consulta_estado === false
){

    echo "

        <div class='mensaje-vacio'>

            Error al consultar la canalización.

        </div>

    ";

    exit();

}


$estado =
"";

$acciones =
"";

$nuevos_reportes =
0;


if(
    mysqli_num_rows(
        $consulta_estado
    ) > 0
){

    $fila_estado =
    mysqli_fetch_assoc(
        $consulta_estado
    );


    $estado =
    $fila_estado[
        'estado'
    ];


    $acciones =
    $fila_estado[
        'acciones'
    ];


    $nuevos_reportes =
    $fila_estado[
        'nuevos_reportes'
    ];

}

?>

<tr>

    <!--=================================
    MATRÍCULA
    =================================-->

    <td>

        <?= htmlspecialchars(
            $alumno[
                'matricula'
            ]
        ); ?>

    </td>


    <!--=================================
    ALUMNO
    =================================-->

    <td>

        <?= strtoupper(

            htmlspecialchars(

                $alumno[
                    'apellido_p'
                ]

                .' '.

                $alumno[
                    'apellido_m'
                ]

                .' '.

                $alumno[
                    'nombre'
                ]

            )

        ); ?>

    </td>


    <!--=================================
    SEMÁFORO
    =================================-->

    <td>

        <div class="contenedor-semaforo">


            <!--=============================
            ICONO SEMÁFORO
            ==============================-->

            <div class="icono-semaforo">

                <?= $riesgo; ?>

            </div>


            <?php

            /*=================================
            PENDIENTE
            =================================*/

            if(
                $estado ==
                "PENDIENTE"
            ){

            ?>

                <span
                    class="texto-monitoreo"
                >

                    🟡 PENDIENTE

                </span>


            <?php

            }


            /*=================================
            EN PROCESO
            =================================*/

            elseif(
                $estado ==
                "EN PROCESO"
            ){

            ?>

                <span
                    class="texto-monitoreo"
                >

                    🟠 EN PROCESO

                </span>


                <?php

                /*=============================
                ACCIONES DEL TUTOR
                ==============================*/

                if(
                    !empty(
                        $acciones
                    )
                ){

                ?>

                    <div
                        class="acciones-tutor-admin"
                    >

                        <strong>

                            Acciones del tutor:

                        </strong>


                        <p>

                            <?= nl2br(
                                htmlspecialchars(
                                    $acciones
                                )
                            ); ?>

                        </p>

                    </div>

                <?php

                }


                /*=============================
                NUEVOS REPORTES
                ==============================*/

                if(
                    $nuevos_reportes > 0
                ){

                ?>

                    <br>

                    <span
                        class="nuevos-reportes"
                    >

                        +<?= $nuevos_reportes; ?>

                        nuevos reportes

                    </span>

                <?php

                }

            }


            /*=================================
            ATENDIDO
            =================================*/

            elseif(
                $estado ==
                "ATENDIDO"
            ){

            ?>

                <span
                    class="texto-monitoreo"
                >

                    🟢 ATENDIDO

                </span>


                <?php

                /*=============================
                ACCIONES DEL TUTOR
                ==============================*/

                if(
                    !empty(
                        $acciones
                    )
                ){

                ?>

                    <div
                        class="acciones-tutor-admin"
                    >

                        <strong>

                            Acciones del tutor:

                        </strong>


                        <p>

                            <?= nl2br(
                                htmlspecialchars(
                                    $acciones
                                )
                            ); ?>

                        </p>

                    </div>

                <?php

                }

                ?>


                <br><br>


                <a

                    href="../canalizar_anexo14/cerrar.php?id=<?= $id_tutorado; ?>"

                    class="btn-canalizar"

                >

                    Cerrar Caso

                </a>


            <?php

            }


            /*=================================
            CERRADO
            =================================*/

            elseif(
                $estado ==
                "CERRADO"
            ){

                if(
                    $total >= 2
                ){

                ?>

                    <a

                        href="../canalizar_anexo14/index.php?id=<?= $id_tutorado; ?>"

                        class="btn-canalizar"

                    >

                        <?= (
                            $total >= 3
                        )

                            ? "Canalizar Urgente"

                            : "Canalizar";

                    ?>

                    </a>

                <?php

                }
                else{

                ?>

                    <span
                        class="texto-monitoreo"
                    >

                        ✅ CERRADO

                    </span>

                <?php

                }

            }


            /*=================================
            SIN CANALIZACIÓN
            =================================*/

            else{

                if(
                    $total == 1
                ){

                ?>

                    <span
                        class="texto-monitoreo"
                    >

                        Monitoreo

                    </span>

                <?php

                }


                if(
                    $total >= 2
                ){

                ?>

                    <a

                        href="../canalizar_anexo14/index.php?id=<?= $id_tutorado; ?>"

                        class="btn-canalizar"

                    >

                        <?= (
                            $total >= 3
                        )

                            ? "Canalizar Urgente"

                            : "Canalizar";

                    ?>

                    </a>

                <?php

                }

            }

            ?>

        </div>

    </td>


    <!--=================================
    MATERIAS
    =================================-->

    <?php foreach(
        $materias
        as
        $materia
    ): ?>


        <?php

        $id_materia =
        $materia[
            'id_materia'
        ];


        $sql_unidades =
        mysqli_query(
            $conn,
            "
            SELECT

                DISTINCT unidad

            FROM anexo14_reportes

            WHERE

                id_tutorado=
                '$id_tutorado'

                AND

                id_materia=
                '$id_materia'

            ORDER BY
                unidad
            "
        );


        $unidades =
        [];


        while(
            $u =
            mysqli_fetch_assoc(
                $sql_unidades
            )
        ){

            $unidades[] =
            "U".$u[
                'unidad'
            ];

        }

        ?>


        <td>

            <?php

            if(
                empty(
                    $unidades
                )
            ){

                echo "-";

            }
            else{

                echo implode(
                    ", ",
                    $unidades
                );

            }

            ?>

        </td>


    <?php endforeach; ?>


</tr>


<?php endwhile; ?>


    </tbody>

</table>