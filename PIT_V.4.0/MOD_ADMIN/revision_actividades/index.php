```php
<?php

require_once("../../sesion.php");

if(
    !in_array(
        "ADMIN",
        $_SESSION["roles"]
    )
){

    header(
        "Location: ../../indexloguin.php"
    );

    exit();

}

include(
    "../../includes_pit/sidebar_admin.php"
);

?>

<div class="separador"></div>
<div class="separador"></div>

<link
rel="stylesheet"
href="revision.css"
>

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
>

<div class="contenedor-revision">

    <!--=====================================
    ENCABEZADO
    =====================================-->

    <div class="encabezado">

        <h1>

            Revisión de Actividades

        </h1>

        <p>

            Consulte las actividades publicadas por los tutores y
            verifique las evidencias entregadas por los tutorados.

        </p>

    </div>

    <!--=====================================
    INSTRUCCIONES
    =====================================-->

    <div class="card-instrucciones">

        <h2>

            <i class="fa-solid fa-circle-info"></i>

            Instrucciones

        </h2>

        <ul>

            <li>

                Seleccione una carrera.

            </li>

            <li>

                Después seleccione el grupo correspondiente.

            </li>

            <li>

                Elija la actividad que desea revisar.

            </li>

            <li>

                El sistema mostrará todos los tutorados del grupo.

            </li>

            <li>

                Podrá identificar qué alumnos entregaron evidencia
                y descargar el archivo PDF enviado.

            </li>

        </ul>

    </div>

    <!--=====================================
    FILTROS
    =====================================-->

    <div class="card-filtros">

        <div class="campo">

            <label>

                Carrera

            </label>

            <select
                id="carrera"
            >

                <option value="">

                    Seleccione una carrera

                </option>

            </select>

        </div>

        <div class="campo">

            <label>

                Grupo

            </label>

            <select
                id="grupo"
                disabled
            >

                <option value="">

                    Primero seleccione una carrera

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

    <div class="contenedor-resumen">

        <div class="card-resumen">

            <h2 id="totalAlumnos">

                0

            </h2>

            <span>

                Total de alumnos

            </span>

        </div>

        <div class="card-resumen">

            <h2 id="totalEntregadas">

                0

            </h2>

            <span>

                Evidencias entregadas

            </span>

        </div>

        <div class="card-resumen">

            <h2 id="totalPendientes">

                0

            </h2>

            <span>

                Pendientes

            </span>

        </div>

    </div>

    <!--=====================================
    RESULTADOS
    =====================================-->

    <div
        id="contenedor-evidencias"
    >

        <div class="mensaje-inicial">

            Seleccione una actividad para visualizar
            las evidencias.

        </div>

    </div>

</div>

<script
src="revision.js"
></script>

<?php

include(
    "../../../includes/footer.php"
);

?>
```
