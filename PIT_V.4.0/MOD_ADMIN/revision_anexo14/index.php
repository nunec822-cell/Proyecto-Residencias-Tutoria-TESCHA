<?php

require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}

include("../../base_pit/conect_pit.php");
include("../../includes_pit/sidebar_admin.php");

/*=========================================
CARRERAS
=========================================*/

$carreras = mysqli_query(
    $conn,
    "
    SELECT DISTINCT carrera
    FROM tutorados
    WHERE activo = 1
    ORDER BY carrera
    "
);

?>

<div class="separador"></div>
<div class="separador"></div>

<link
    rel="stylesheet"
    href="revision_anexo14.css"
>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
>

<div class="contenedor-anexo">

    <!--=====================================
    ENCABEZADO
    ======================================-->

    <div class="encabezado">

        <h1>

            Revisión Anexo 14

        </h1>

        <p>

            Consulte los reportes registrados por los
            docentes y visualice los alumnos en riesgo
            por grupo y carrera.

        </p>

    </div>

    <!--=====================================
    FILTROS
    ======================================-->

    <div class="card-filtros">

        <div class="campo">

            <label>

                Carrera

            </label>

            <select id="carrera">

                <option value="">

                    Seleccione una carrera

                </option>

                <?php while(
                    $fila =
                    mysqli_fetch_assoc(
                        $carreras
                    )
                ): ?>

                    <option
                        value="<?= $fila['carrera']; ?>"
                    >

                        <?= strtoupper(
                            $fila['carrera']
                        ); ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </div>

        <div class="campo">

            <label>

                Grupo

            </label>

            <select id="grupo">

                <option value="">

                    Seleccione una carrera primero

                </option>

            </select>

        </div>

        <div class="botones">

            <button
                id="btnConsultar"
                class="btn-consultar"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                Consultar

            </button>

            <button
                id="btnCSV"
                class="btn-csv"
            >

                <i class="fa-solid fa-file-csv"></i>

                Descargar CSV

            </button>

        </div>

    </div>
 <!--=====================================
SIMBOLOGÍA
======================================-->

<h2 class="titulo-simbologia">

    Simbología del Semáforo Académico

</h2>

<div
    id="resumen-riesgos"
    class="contenedor-resumen"
>

    <!-- SIN RIESGO -->

    <div class="card-resumen">

        <h2>🟢</h2>

        <h3>Sin Riesgo</h3>

        <p>

            0 unidades reportadas

        </p>

    </div>

    <!-- RIESGO BAJO -->

    <div class="card-resumen">

        <h2>🟡</h2>

        <h3>Riesgo Bajo</h3>

        <p>

            1 unidad reportada

        </p>

    </div>

    <!-- RIESGO MEDIO -->

    <div class="card-resumen">

        <h2>🟠</h2>

        <h3>Riesgo Medio</h3>

        <p>

            2 unidades reportadas

        </p>

    </div>

    <!-- RIESGO ALTO -->

    <div class="card-resumen">

        <h2>🔴</h2>

        <h3>Riesgo Alto</h3>

        <p>

            3 o más unidades reportadas

        </p>

    </div>

    <!-- CANALIZACIÓN -->

    <div class="card-resumen">

        <h2>📋</h2>

        <h3>Seguimiento</h3>

        <p>

            🟡 Pendiente<br>

            🟠 En Proceso<br>

            🟢 Atendido<br>

            ✅ Cerrado

        </p>

    </div>

</div>

    <!--=====================================
    RESULTADOS
    ======================================-->

    <div
        id="contenedor-anexo"
        class="card-tabla"
    >

        <div class="mensaje-inicial">

            Seleccione una carrera y un grupo
            para visualizar el Anexo 14.

        </div>

    </div>

</div>

<script src="revision_anexo14.js"></script>

<?php

include("../../../includes/footer.php");

?>