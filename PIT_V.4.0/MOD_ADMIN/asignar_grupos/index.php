<?php

require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}

include("../../base_pit/conect_pit.php");
include("../../includes_pit/sidebar_admin.php"); 

$carreras = mysqli_query($conn,"
    SELECT DISTINCT carrera
    FROM personal_academico
    WHERE activo = 1
    ORDER BY carrera
");





/*=========================================
ASIGNACIONES
=========================================*/

$asignaciones = mysqli_query($conn, "
    SELECT

        dg.id_asignacion,
        dg.grupo,
        dg.periodo,

        CONCAT(
            pa.apellido_p,' ',
            pa.apellido_m,' ',
            pa.nombre
        ) AS docente

    FROM docente_grupos dg

    INNER JOIN personal_academico pa
        ON dg.id_personal = pa.id_personal

    ORDER BY docente
");

?>

<div class="separador"></div>
<div class="separador"></div>
<link rel="stylesheet" href="asignar_grupos.css">
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<div class="contenedor-asignacion">

    <div class="encabezado">

        <h1>Asignación de Grupos</h1>

        <p>
            Asigne grupos a los docentes para el módulo
            Anexo 14.
        </p>

    </div>

    <form action="guardar_asignacion.php" method="POST">

        <div class="card">

            <div class="campo">

    <label>Carrera</label>

    <select id="carrera">

        <option value="">
            Seleccione una carrera
        </option>

        <?php while($carrera = mysqli_fetch_assoc($carreras)): ?>

            <option value="<?= $carrera['carrera']; ?>">

                <?= strtoupper($carrera['carrera']); ?>

            </option>

        <?php endwhile; ?>

    </select>

</div>

<div class="campo">

    <label>Docente</label>

    <select
        name="id_personal"
        id="docente"
        required
    >

        <option value="">
            Seleccione primero una carrera
        </option>

    </select>

</div>

            <div class="campo">

                <label>Periodo</label>

                <input
                    type="text"
                    name="periodo"
                    value="2026-2"
                    required
                >

            </div>

        </div>

        <div class="card">

            <h3>Grupos Disponibles</h3>

            <div
    class="grid-grupos"
    id="contenedor-grupos"
>

    <p>
        Seleccione primero una carrera
    </p>

</div>

        </div>

        <button
            type="submit"
            class="btn-guardar"
        >
            Guardar Asignación
        </button>

    </form>

    <div class="card tabla-card">

        <h3>Asignaciones Actuales</h3>

        <table class="tabla-asignaciones">

            <thead>

                <tr>

                    <th>#</th>
                    <th>Docente</th>
                    <th>Grupo</th>
                    <th>Periodo</th>
                    <th>Acción</th>

                </tr>

            </thead>

            <tbody>

                <?php

                $contador = 1;

                while($fila = mysqli_fetch_assoc($asignaciones)):

                ?>

                <tr>

                    <td><?= $contador++; ?></td>

                    <td><?= strtoupper($fila['docente']); ?></td>

                    <td><?= $fila['grupo']; ?></td>

                    <td><?= $fila['periodo']; ?></td>

                    <td>

                        <a
                            href="eliminar_asignacion.php?id=<?= $fila['id_asignacion']; ?>"
                            class="btn-eliminar"
                            onclick="return confirm('¿Desea eliminar esta asignación?')"
                        >
                            Eliminar
                        </a>

                    </td>

                </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>
<script>


document.addEventListener("DOMContentLoaded", function(){

    const carrera = document.getElementById("carrera");
    const docente = document.getElementById("docente");
    const grupos = document.getElementById("contenedor-grupos");

    carrera.addEventListener("change", function(){

        let valor = this.value;

        if(valor === ""){

            docente.innerHTML =
            '<option value="">Seleccione primero una carrera</option>';

            grupos.innerHTML =
            '<p>Seleccione primero una carrera</p>';

            return;
        }

        /* DOCENTES */

        fetch(
            'obtener_docentes.php?carrera=' +
            encodeURIComponent(valor)
        )
        .then(response => response.text())
        .then(data => {

            docente.innerHTML = data;

        });

        /* GRUPOS */

        fetch(
            'obtener_grupos.php?carrera=' +
            encodeURIComponent(valor)
        )
        .then(response => response.text())
        .then(data => {

            grupos.innerHTML = data;

        });

    });

});

</script>

<?php include("../../../includes/footer.php"); ?>