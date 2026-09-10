<?php

require_once("../../sesion.php");

if (!in_array("DOCENTE", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}

include("../../base_pit/conect_pit.php");
include("../../includes_pit/sidebar_docente.php");

/*=========================================
DOCENTE
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

$sql = mysqli_query($conn,"
    SELECT *
    FROM personal_academico
    WHERE id_usuario='$id_usuario'
    LIMIT 1
");

$docente = mysqli_fetch_assoc($sql);

$id_docente = $docente['id_personal'];

$nombre_docente = strtoupper(
    $docente['apellido_p']." ".
    $docente['apellido_m']." ".
    $docente['nombre']
);

/*=========================================
GRUPOS DEL DOCENTE
=========================================*/

$grupos = mysqli_query($conn,"
    SELECT DISTINCT grupo
    FROM docente_grupos
    WHERE id_personal='$id_docente'
    ORDER BY grupo
");

?>
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<div class="separador"></div>
<div class="separador"></div>

<link rel="stylesheet" href="consultar_anexo14.css">

<div class="contenedor-consulta">

    <div class="encabezado">

        <h1>Consultar Anexo 14</h1>

        <p>
            Consulta y descarga los reportes
            realizados por grupo.
        </p>

    </div>

    <div class="card-docente">

        <span>Docente</span>

        <strong>

            <?= $nombre_docente; ?>

        </strong>

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

            <label>Unidad</label>

            <select id="unidad">

                <option value="TODAS">
                TODAS
            </option>

            <?php for($i=1;$i<=6;$i++): ?>

                <option value="<?= $i ?>">

                    Unidad <?= $i ?>

                </option>

            <?php endfor; ?>

        </select>

        </div>

        <button
            class="btn-consultar"
            id="btnConsultar"
        >
            Consultar
        </button>

        <button
            class="btn-csv"
            id="btnCSV"
        >
            Descargar CSV
        </button>

    </div>

    <div
        id="contenedor-reportes"
        class="contenedor-reportes"
    >

        Seleccione un grupo para consultar.

    </div>

</div>

<script>

document
.getElementById("btnCSV")
.addEventListener("click", function(){

    let grupo =
    document.getElementById(
        "grupo"
    ).value;

    if(grupo === ""){

        alert(
            "Seleccione un grupo."
        );

        return;
    }

    let unidad =
    document.getElementById("unidad").value;

    window.location =
    "exportar_csv.php?grupo=" +
    encodeURIComponent(grupo) +
    "&unidad=" +
    encodeURIComponent(unidad);

});

</script>
<script>

document
.getElementById("btnConsultar")
.addEventListener("click", function(){

    let grupo =
    document.getElementById(
        "grupo"
    ).value;

    let unidad =
    document.getElementById("unidad").value;

    if(grupo === ""){

        alert(
            "Seleccione un grupo."
        );

        return;

    }

    fetch(

    "obtener_reportes.php?grupo=" +

    encodeURIComponent(grupo)

    +

    "&unidad=" +

    encodeURIComponent(unidad)

    )

    .then(response => response.text())

    .then(data => {

        document
        .getElementById(
            "contenedor-reportes"
        )
        .innerHTML = data;

    });

});

</script>
<?php include("../../../includes/footer.php"); ?>