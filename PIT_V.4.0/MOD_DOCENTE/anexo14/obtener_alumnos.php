<?php

require_once("../../sesion.php");

if (!in_array("DOCENTE", $_SESSION["roles"])) {
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
FILTROS
=========================================*/

$grupo   = $_GET['grupo'] ?? '';
$materia = $_GET['materia'] ?? '';

if(
    empty($grupo) ||
    empty($materia)
){
    exit();
}

/*=========================================
ALUMNOS
=========================================*/

$alumnos = mysqli_query(
    $conn,
    "
    SELECT *
    FROM tutorados
    WHERE grupo='$grupo'
    AND activo=1
    ORDER BY
        apellido_p,
        apellido_m,
        nombre
    "
);

?>

<div class="mensaje-importante">

    <i class="fa-solid fa-circle-exclamation"></i>

    <div>

        <strong>Importante:</strong>

        Verifique que la materia seleccionada
        corresponda a la que usted imparte en el
        grupo <b><?= $grupo; ?></b>.

        <br><br>

        La información registrada será utilizada
        para el seguimiento académico institucional
        de los alumnos.

    </div>

</div>

<table class="tabla-alumnos">

    <thead>

        <tr>

            <th>Matrícula</th>
            <th>Alumno</th>
            <th>Acción</th>

        </tr>

    </thead>

    <tbody>

        <?php while(
            $alumno =
            mysqli_fetch_assoc(
                $alumnos
            )
        ): ?>

        <tr>

            <td>

                <?= $alumno['matricula']; ?>

            </td>

            <td>

                <?= strtoupper(

                    $alumno['apellido_p'].' '.
                    $alumno['apellido_m'].' '.
                    $alumno['nombre']

                ); ?>

            </td>

            <td>

                <button
                    class="btn-reportar"
                    data-id="<?= $alumno['id_tutorado']; ?>"
                >

                    Reportar

                </button>

            </td>

        </tr>

        <?php endwhile; ?>

    </tbody>

</table>