<?php

require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    exit();
}

include("../../base_pit/conect_pit.php");

$id_tutorado = $_GET['id'] ?? 0;

if($id_tutorado == 0){
    exit();
}

/*=========================================
ALUMNO Y TUTOR
=========================================*/

$sql = mysqli_query($conn,"
    SELECT

        t.id_tutorado,
        t.matricula,

        CONCAT(
            t.apellido_p,' ',
            t.apellido_m,' ',
            t.nombre
        ) AS alumno,

        pa.id_personal,
        pa.id_usuario,

        CONCAT(
            pa.apellido_p,' ',
            pa.apellido_m,' ',
            pa.nombre
        ) AS tutor

    FROM tutorados t

    LEFT JOIN personal_academico pa
        ON t.id_tutor = pa.id_usuario

    WHERE t.id_tutorado='$id_tutorado'

    LIMIT 1
");

$datos = mysqli_fetch_assoc($sql);

if(!$datos){

    echo "
    <div class='mensaje-vacio'>
        Alumno no encontrado.
    </div>
    ";

    exit();

}

$id_tutor = $datos['id_personal'];

/*=========================================
SEMÁFORO
=========================================*/

$consulta = mysqli_query($conn,"
    SELECT COUNT(
        DISTINCT unidad
    ) AS total
    FROM anexo14_reportes
    WHERE id_tutorado='$id_tutorado'
");

$riesgo = mysqli_fetch_assoc($consulta);

$total = $riesgo['total'];

if($total == 0){

    $semaforo = "🟢 SIN RIESGO";

}elseif($total == 1){

    $semaforo = "🟡 RIESGO BAJO";

}elseif($total == 2){

    $semaforo = "🟠 RIESGO MEDIO";

}else{

    $semaforo = "🔴 RIESGO ALTO";

}

/*=========================================
CANALIZACIÓN ACTIVA
=========================================*/

$canalizacion =
mysqli_query(
    $conn,
    "
    SELECT *
    FROM canalizaciones
    WHERE id_tutorado =
    '$id_tutorado'
    AND estado IN(
        'PENDIENTE',
        'EN PROCESO'
    )
    "
);

$existe =
mysqli_num_rows(
    $canalizacion
) > 0;

/*=========================================
ÚLTIMA CANALIZACIÓN
=========================================*/

$canal =
mysqli_query(
    $conn,
    "
    SELECT

        id_canalizacion,
        estado

    FROM canalizaciones

    WHERE id_tutorado =
    '$id_tutorado'

    ORDER BY
        id_canalizacion DESC

    LIMIT 1
    "
);

$datos_canal =
mysqli_fetch_assoc(
    $canal
);

$id_canalizacion =
$datos_canal[
    'id_canalizacion'
] ?? 0;

$estado_canalizacion =
$datos_canal[
    'estado'
] ?? "";

/*=========================================
SI NUNCA HA SIDO CANALIZADO
MOSTRAR REPORTES NUEVOS
=========================================*/

if(
    $id_canalizacion == 0
){

    $mostrar_por =
    "ID_TUTORADO";

}else{

    $mostrar_por =
    "ID_CANALIZACION";

}

/*=========================================
REPORTES
=========================================*/

if(
    $mostrar_por ==
    "ID_TUTORADO"
){

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
            ar.id_tutorado =
            '$id_tutorado'

        AND
            ar.id_canalizacion
            IS NULL

        ORDER BY
            ar.unidad
        "
    );

}
else{

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
            '$id_canalizacion'

        ORDER BY
            ar.unidad
        "
    );

}

?>

<div class="card-info">

    <h2>

        <?= strtoupper(
            $datos['alumno']
        ); ?>

    </h2>

    <p>

        <strong>Matrícula:</strong>

        <?= $datos['matricula']; ?>

    </p>

    <p>

        <strong>Tutor:</strong>

        <?= !empty($datos['tutor'])
            ? strtoupper($datos['tutor'])
            : "SIN TUTOR ASIGNADO"; ?>

    </p>

    <p>

        <strong>Semáforo:</strong>

        <?= $semaforo; ?>

    </p>

</div>

<div class="card-historial">

    <h3>
        Historial Anexo 14   en cuanto oprimas el boton ( Canalizar ) te saldra su historial del alumno que acabas de canalizar a su docente 
    </h3>

    <?php while(
        $fila =
        mysqli_fetch_assoc(
            $reportes
        )
    ): ?>

        <div class="item-reporte">

            <h4>

                <?= strtoupper(
                    $fila[
                        'nombre_materia'
                    ]
                ); ?>

            </h4>

            <p>

                <strong>
                    Unidad
                    <?= $fila[
                        'unidad'
                    ]; ?>
                </strong>

            </p>

            <ul>

                <?php

                if(
                    $fila[
                        'competencia_no_alcanzada'
                    ]
                ){
                    echo "
                    <li>
                        Competencia no alcanzada
                    </li>";
                }

                if(
                    $fila[
                        'inasistencias'
                    ]
                ){
                    echo "
                    <li>
                        Inasistencias
                    </li>";
                }

                if(
                    $fila[
                        'indisciplina'
                    ]
                ){
                    echo "
                    <li>
                        Indisciplina
                    </li>";
                }

                if(
                    $fila[
                        'no_entrega_trabajos'
                    ]
                ){
                    echo "
                    <li>
                        No entrega de trabajos
                    </li>";
                }

                if(
                    $fila[
                        'apoyo_psicologico'
                    ]
                ){
                    echo "
                    <li>
                        Apoyo psicológico
                    </li>";
                }

                if(
                    $fila[
                        'apoyo_economico'
                    ]
                ){
                    echo "
                    <li>
                        Apoyo económico
                    </li>";
                }

                if(
                    !empty(
                        $fila['otro']
                    )
                ){
                    echo "
                    <li>
                        ".$fila['otro']."
                    </li>";
                }

                ?>

            </ul>

            <?php if(
                !empty(
                    $fila['observaciones']
                )
            ): ?>

                <p>

                    <strong>
                        Observaciones:
                    </strong>

                    <?= $fila[
                        'observaciones'
                    ]; ?>

                </p>

            <?php endif; ?>

        </div>

    <?php endwhile; ?>

</div>

<?php if(!$existe): ?>

    <form
        id="form-canalizacion"
    >

        <input
            type="hidden"
            name="id_tutorado"
            value="<?= $id_tutorado; ?>"
        >

        <input
            type="hidden"
            name="id_tutor"
            value="<?= $id_tutor; ?>"
        >

        <textarea
            name="observaciones"
            placeholder="Ingrese las observaciones de la canalización..."
            required
        ></textarea>

        <button
            type="submit"
            class="btn-canalizar"
        >
            Canalizar Alumno
        </button>

    </form>

<?php else: ?>

    <div
        class="mensaje-canalizado"
    >

        Este alumno ya cuenta con una canalización activa.

    </div>

<?php endif; ?>