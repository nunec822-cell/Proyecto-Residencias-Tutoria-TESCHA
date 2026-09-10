<?php

require_once("../../sesion.php");

if (!in_array("DOCENTE", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}

include("../../base_pit/conect_pit.php");
include("../../includes_pit/sidebar_docente.php");

/*=========================================
DATOS DEL DOCENTE
=========================================*/

$id_usuario = $_SESSION["id_usuario"];

$sql_docente = mysqli_query($conn,"
    SELECT *
    FROM personal_academico
    WHERE id_usuario = '$id_usuario'
    LIMIT 1
");

$docente = mysqli_fetch_assoc($sql_docente);

$id_personal = $docente['id_personal'];

$nombre_docente = strtoupper(
    $docente['apellido_p'].' '.
    $docente['apellido_m'].' '.
    $docente['nombre']
);

$carrera = $docente['carrera'];

/*=========================================
GRUPOS ASIGNADOS
=========================================*/

$grupos = mysqli_query($conn,"
    SELECT DISTINCT grupo
    FROM docente_grupos
    WHERE id_personal = '$id_personal'
    ORDER BY grupo
");

/*=========================================
MATERIAS DE LA CARRERA
=========================================*/

$materias = mysqli_query($conn,"
    SELECT *
    FROM materias
    WHERE carrera = '$carrera'
    AND estado = 'ACTIVA'
    ORDER BY nombre_materia
");

?>

<div class="separador"></div>
<div class="separador"></div>
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="anexo14.css">

<div class="contenedor-anexo14">

    <div class="encabezado-anexo14">

        <h1>
            Anexo 14
        </h1>

        <p>
            Reporte de estudiantes en riesgo académico.
        </p>

    </div>

    <div class="card-info">

        <div class="info-item">

            <span>Docente</span>

            <strong>
                <?= $nombre_docente ?>
            </strong>

        </div>

        <div class="info-item">

            <span>Carrera</span>

            <strong>
                <?= $carrera ?>
            </strong>

        </div>

    </div>

    <div class="card-filtros">

        <div class="campo">

            <label>Grupo</label>

            <select id="grupo">

                <option value="">
                    Seleccione un grupo
                </option>

                <?php while($grupo = mysqli_fetch_assoc($grupos)): ?>

                    <option
                        value="<?= $grupo['grupo']; ?>"
                    >
                        <?= $grupo['grupo']; ?>
                    </option>

                <?php endwhile; ?>

            </select>

        </div>

        <div class="campo">

            <label>Materia</label>

            <select id="materia">

                <option value="">
                    Seleccione una materia
                </option>

                <?php while($materia = mysqli_fetch_assoc($materias)): ?>

                    <option
                        value="<?= $materia['id_materia']; ?>"
                    >
                        <?= $materia['nombre_materia']; ?>
                    </option>

                <?php endwhile; ?>

            </select>

        </div>

        <button
            id="btnConsultar"
            class="btn-consultar"
        >
            Consultar Alumnos
        </button>

    </div>

    <div
        id="contenedor-alumnos"
        class="contenedor-alumnos"
    >

        <div class="mensaje-inicial">

            Seleccione un grupo y una materia.

        </div>

    </div>

</div>
<div id="modalAnexo14" class="modal-anexo14">

    <div id="contenidoModal"></div>

</div>
<script>

function cargarAlumnos(){

    let grupo =
    document.getElementById("grupo").value;

    let materia =
    document.getElementById("materia").value;

    fetch(
        "obtener_alumnos.php?grupo=" +
        encodeURIComponent(grupo) +
        "&materia=" +
        encodeURIComponent(materia)
    )

    .then(response => response.text())

    .then(data => {

        document.getElementById(
            "contenedor-alumnos"
        ).innerHTML = data;

    });

}

</script>
<script>

document
.getElementById("btnConsultar")
.addEventListener("click", function(){

    let grupo =
    document.getElementById("grupo").value;

    let materia =
    document.getElementById("materia").value;

    if(grupo === "" || materia === ""){

        alert(
            "Seleccione grupo y materia."
        );

        return;
    }

    cargarAlumnos();

});

</script>
<script>

document.addEventListener("click", function(e){

    if(
        e.target.classList.contains("btn-reportar")
    ){

        let idTutorado =
        e.target.dataset.id;

        let grupo =
        document.getElementById("grupo").value;

        let materia =
        document.getElementById("materia").value;

        fetch(
            "modal_reporte.php?id_tutorado=" +
            idTutorado +
            "&id_materia=" +
            materia +
            "&grupo=" +
            grupo
        )

        .then(response => response.text())

        .then(html => {

            document
            .getElementById("contenidoModal")
            .innerHTML = html;

            document
            .getElementById("modalAnexo14")
            .style.display = "flex";

        });

    }

});

document.addEventListener("click", function(e){

    if(e.target.id === "modalAnexo14"){

        document
        .getElementById("modalAnexo14")
        .style.display = "none";

    }

});

</script>
<script>

document.addEventListener("submit", function(e){

    if(e.target.id === "formReporte"){

        e.preventDefault();

        let formulario =
        e.target;

        let datos =
        new FormData(formulario);

        fetch(
            "guardar_reporte.php",
            {
                method: "POST",
                body: datos
            }
        )

        .then(response => response.json())

        .then(resultado => {

            if(resultado.success){

                alert(resultado.mensaje);

                document
                .getElementById("modalAnexo14")
                .style.display = "none";

                cargarAlumnos();

            }

        })

        .catch(error => {

            console.error(error);

            alert(
                "Error al guardar."
            );

        });

    }

});

</script>
<?php include("../../../includes/footer.php"); ?>