<?php

require_once("../../sesion.php");

if (!in_array("DOCENTE", $_SESSION["roles"])) {
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
DOCENTE
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

$sql_docente = mysqli_query($conn,"
    SELECT id_personal
    FROM personal_academico
    WHERE id_usuario='$id_usuario'
    LIMIT 1
");

$docente = mysqli_fetch_assoc($sql_docente);

$id_docente = $docente['id_personal'];

/*=========================================
FILTROS
=========================================*/

$grupo = $_GET['grupo'] ?? '';
$unidad = $_GET['unidad'] ?? 'TODAS';

if(empty($grupo)){
    exit();
}

/*=========================================
FUNCIÓN
=========================================*/

function obtenerProblemas(
    $conn,
    $id_tutorado,
    $id_materia,
    $unidad
){

    $sql = mysqli_query(
        $conn,
        "
        SELECT *
        FROM anexo14_reportes
        WHERE id_tutorado='$id_tutorado'
        AND id_materia='$id_materia'
        AND unidad='$unidad'
        "
    );

    $problemas = [];

    while($fila = mysqli_fetch_assoc($sql)){

        if($fila['competencia_no_alcanzada']){
            $problemas[] =
            "Competencia";
        }

        if($fila['inasistencias']){
            $problemas[] =
            "Inasistencias";
        }

        if($fila['indisciplina']){
            $problemas[] =
            "Indisciplina";
        }

        if($fila['no_entrega_trabajos']){
            $problemas[] =
            "No entrega";
        }

        if($fila['apoyo_psicologico']){
            $problemas[] =
            "Psicológico";
        }

        if($fila['apoyo_economico']){
            $problemas[] =
            "Económico";
        }

        if(!empty($fila['otro'])){
            $problemas[] =
            $fila['otro'];
        }
    }

    if(empty($problemas)){

        return "-";

    }

    return implode(
        "<br>",
        array_unique(
            $problemas
        )
    );

}

/*=========================================
CONSULTA PRINCIPAL
=========================================*/

$sql = "
SELECT DISTINCT

    t.id_tutorado,
    t.matricula,

    CONCAT(
        t.apellido_p,' ',
        t.apellido_m,' ',
        t.nombre
    ) AS alumno,

    m.id_materia,
    m.nombre_materia

FROM anexo14_reportes ar

INNER JOIN tutorados t
    ON ar.id_tutorado=t.id_tutorado

INNER JOIN materias m
    ON ar.id_materia=m.id_materia

WHERE ar.id_docente='$id_docente'
AND ar.grupo='$grupo'

ORDER BY alumno
";

$resultado = mysqli_query(
    $conn,
    $sql
);

if(mysqli_num_rows($resultado)==0){

    echo "
    <div class='mensaje-vacio'>
        No existen reportes para este grupo.
    </div>
    ";

    exit();
}

?>

<table class="tabla-reportes">

    <thead>

        <tr>

            <th>Matrícula</th>
            <th>Alumno</th>
            <th>Materia</th>

            <?php if($unidad=="TODAS"): ?>

                <th>U1</th>
                <th>U2</th>
                <th>U3</th>
                <th>U4</th>
                <th>U5</th>
                <th>U6</th>

            <?php else: ?>

                <th>
                    Unidad <?= $unidad; ?>
                </th>

            <?php endif; ?>

        </tr>

    </thead>

    <tbody>

        <?php while(
            $fila =
            mysqli_fetch_assoc(
                $resultado
            )
        ): ?>

        <tr>

            <td>

                <?= $fila['matricula']; ?>

            </td>

            <td>

                <?= strtoupper(
                    $fila['alumno']
                ); ?>

            </td>

            <td>

                <?= strtoupper(
                    $fila['nombre_materia']
                ); ?>

            </td>

            <?php if($unidad=="TODAS"): ?>

                <?php for($u=1;$u<=6;$u++): ?>

                    <td>

                        <?= obtenerProblemas(
                            $conn,
                            $fila['id_tutorado'],
                            $fila['id_materia'],
                            $u
                        ); ?>

                    </td>

                <?php endfor; ?>

            <?php else: ?>

                <td>

                    <?= obtenerProblemas(
                        $conn,
                        $fila['id_tutorado'],
                        $fila['id_materia'],
                        $unidad
                    ); ?>

                </td>

            <?php endif; ?>

        </tr>

        <?php endwhile; ?>

    </tbody>

</table>