<?php

require_once("../../sesion.php");

if (!in_array("DOCENTE", $_SESSION["roles"])) {
    exit();
}

include("../../base_pit/conect_pit.php");

$id_tutorado = $_GET['id_tutorado'] ?? 0;
$id_materia  = $_GET['id_materia'] ?? 0;
$grupo       = $_GET['grupo'] ?? '';

$consulta = mysqli_query($conn,"
    SELECT *
    FROM tutorados
    WHERE id_tutorado = '$id_tutorado'
    LIMIT 1
");

$alumno = mysqli_fetch_assoc($consulta);

?>

<div class="modal-contenido">

    <h2>Reporte Anexo 14</h2>

    <form
        id="formReporte"
        action="guardar_reporte.php"
        method="POST"
    >

        <input
            type="hidden"
            name="id_tutorado"
            value="<?= $id_tutorado ?>"
        >

        <input
            type="hidden"
            name="id_materia"
            value="<?= $id_materia ?>"
        >

        <input
            type="hidden"
            name="grupo"
            value="<?= $grupo ?>"
        >

        <div class="alumno-info">

            <strong>Alumno:</strong>

            <?= strtoupper(
                $alumno['apellido_p']." ".
                $alumno['apellido_m']." ".
                $alumno['nombre']
            ); ?>

        </div>

        <div class="campo">

            <label>Unidad</label>

            <select name="unidad" required>

                <option value="">Seleccione</option>

                <option value="1">Unidad 1</option>
                <option value="2">Unidad 2</option>
                <option value="3">Unidad 3</option>
                <option value="4">Unidad 4</option>
                <option value="5">Unidad 5</option>
                <option value="6">Unidad 6</option>

            </select>

        </div>

        <div class="problemas">

            <h4>Problemas Detectados</h4>

            <label>
                <input type="checkbox"
                       name="competencia_no_alcanzada">
                Competencia no alcanzada
            </label>

            <label>
                <input type="checkbox"
                       name="inasistencias">
                Inasistencias
            </label>

            <label>
                <input type="checkbox"
                       name="indisciplina">
                Indisciplina
            </label>

            <label>
                <input type="checkbox"
                       name="no_entrega_trabajos">
                No entrega trabajos
            </label>

            <label>
                <input type="checkbox"
                       name="apoyo_psicologico">
                Apoyo psicológico
            </label>

            <label>
                <input type="checkbox"
                       name="apoyo_economico">
                Apoyo económico
            </label>

        </div>

        <div class="campo">

            <label>Otro</label>

            <input
                type="text"
                name="otro"
                maxlength="255"
            >

        </div>

        <div class="campo">

            <label>Observaciones</label>

            <textarea
                name="observaciones"
                rows="4"
            ></textarea>

        </div>

        <div class="acciones-modal">

            <button
                type="submit"
                class="btn-guardar-reporte"
            >
                Guardar Reporte
            </button>

        </div>

    </form>

</div>