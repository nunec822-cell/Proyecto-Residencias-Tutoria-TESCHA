<?php

require_once("../../sesion.php");

if(
    !in_array(
        "TUTOR",
        $_SESSION["roles"]
    )
){

    header(
        "Location: ../../indexloguin.php"
    );

    exit();

}

include(
    "../../includes_pit/sidebar_docente.php"
);

?>

<div class="separador"></div>
<div class="separador"></div>

<link
rel="stylesheet"
href="evidencias.css"
>

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
>

<div class="contenedor-evidencias">

    <!--=====================================
        ENCABEZADO
    =====================================-->

    <div class="encabezado">

        <h1>

            Evidencias de Actividades

        </h1>

        <p>

            Consulte las evidencias enviadas por
            sus tutorados para cada una de las
            actividades publicadas. Desde este
            módulo podrá identificar rápidamente
            quién entregó y quién aún tiene una
            actividad pendiente.

        </p>

    </div>

    <!--=====================================
        INSTRUCCIONES
    =====================================-->

    <div class="card-instrucciones">

        <h2>

            <i class="fa-solid fa-circle-info"></i>

            ¿Cómo funciona?

        </h2>

        <ol>

            <li>

                Seleccione uno de los grupos que
                tiene asignados como tutor.

            </li>

            <li>

                Después seleccione la actividad
                publicada para ese grupo.

            </li>

            <li>

                El sistema mostrará todos los
                tutorados del grupo.

            </li>

            <li>

                Los alumnos que hayan enviado su
                evidencia aparecerán como
                <strong>Entregada</strong> junto
                con la fecha de entrega y un botón
                para descargar el PDF.

            </li>

            <li>

                Los alumnos que aún no hayan
                enviado evidencia aparecerán como
                <strong>Pendiente</strong>.

            </li>

        </ol>

    </div>

    <!--=====================================
        FILTROS
    =====================================-->

    <div class="card-filtros">

        <div class="campo">

            <label>

                Grupo

            </label>

            <select
                id="grupo"
            >

                <option value="">

                    Seleccione un grupo

                </option>

            </select>

        </div>

        <div class="campo">

            <label>

                Actividad

            </label>

            <select
                id="actividad"
                disabled
            >

                <option value="">

                    Primero seleccione un grupo

                </option>

            </select>

        </div>

    </div>

    <!--=====================================
        RESUMEN
    =====================================-->

    <div class="card-resumen">

        <div class="dato">

            <h3>

                Total de alumnos

            </h3>

            <span id="totalAlumnos">

                0

            </span>

        </div>

        <div class="dato">

            <h3>

                Evidencias entregadas

            </h3>

            <span id="totalEntregadas">

                0

            </span>

        </div>

        <div class="dato">

            <h3>

                Pendientes

            </h3>

            <span id="totalPendientes">

                0

            </span>

        </div>

    </div>

    <!--=====================================
        TABLA
    =====================================-->

    <div
        id="contenedor-entregas"
        class="contenedor-entregas"
    >

        <div class="mensaje-inicial">

            Seleccione un grupo y una actividad
            para consultar las evidencias.

        </div>

    </div>

</div>

<script
src="evidencias.js"
></script>

<?php

include(
    "../../../includes/footer.php"
);

?>