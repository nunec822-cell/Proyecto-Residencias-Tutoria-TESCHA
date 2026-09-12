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
href="actividades.css"
>

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
>

<div class="contenedor-actividades">

    <!--=====================================
        ENCABEZADO
    =====================================-->

    <div class="encabezado">

        <h1>

            Actividades de Tutoría

        </h1>

        <p>

            Publique actividades dirigidas a sus
            tutorados. Puede agregar un documento
            PDF, imágenes o un enlace de apoyo
            para complementar la actividad.

        </p>

    </div>

    <!--=====================================
        INSTRUCCIONES
    =====================================-->

    <div class="card-instrucciones">

        <h2>

            <i class="fa-solid fa-circle-info"></i>

            Recomendaciones

        </h2>

        <ul>

            <li>

                Seleccione primero el grupo al que
                desea asignar la actividad.

            </li>

            <li>

                El título y la descripción son
                obligatorios.

            </li>

            <li>

                El PDF es opcional y puede tener
                hasta <strong>10 MB.</strong>

            </li>

            <li>

                Puede adjuntar como máximo
                <strong>3 imágenes</strong>
                (JPG, JPEG, PNG o WEBP).

            </li>

            <li>

                También puede agregar un enlace
                de apoyo para sus tutorados.

            </li>

        </ul>

    </div>
    <!--=====================================
FORMULARIO
=====================================-->

<div class="formulario">

    <h2>

        <i class="fa-solid fa-file-circle-plus"></i>

        Nueva Actividad

    </h2>

    <form
        id="formActividad"
        enctype="multipart/form-data"
    >

        <div class="grid-formulario">

            <!--=============================
            Grupo
            ==============================-->

            <div class="campo">

                <label>

                    Grupo

                </label>

                <select
                    name="grupo"
                    id="grupo"
                    required
                >

                    <option value="">

                        Seleccione un grupo

                    </option>

                </select>

            </div>

            <!--=============================
            Título
            ==============================-->

            <div class="campo">

                <label>

                    Título

                </label>

                <input
                    type="text"
                    name="titulo"
                    maxlength="200"
                    required
                    placeholder="Ej. Actividad de integración"
                >

            </div>

            <!--=============================
            Descripción
            ==============================-->

            <div class="campo campo-completo">

                <label>

                    Descripción

                </label>

                <textarea
                    name="descripcion"
                    maxlength="1000"
                    required
                    placeholder="Explique claramente las instrucciones que deberán realizar los tutorados..."
                ></textarea>

            </div>

            <!--=============================
            PDF
            ==============================-->

            <div class="campo">

                <label>

                    Documento PDF

                </label>

                <input
                    type="file"
                    name="pdf"
                    accept=".pdf"
                >

                <small>

                    Opcional • Máximo 10 MB

                </small>

            </div>

            <!--=============================
            Link
            ==============================-->

            <div class="campo">

                <label>

                    Enlace de apoyo

                </label>

                <input
                    type="url"
                    name="link"
                    maxlength="500"
                    placeholder="https://"

                >

                <small>

                    Opcional

                </small>

            </div>

            <!--=============================
            Imagen 1
            ==============================-->

            <div class="campo">

                <label>

                    Imagen 1

                </label>

                <input
                    type="file"
                    name="imagen1"
                    accept=".jpg,.jpeg,.png,.webp"
                >

            </div>

            <!--=============================
            Imagen 2
            ==============================-->

            <div class="campo">

                <label>

                    Imagen 2

                </label>

                <input
                    type="file"
                    name="imagen2"
                    accept=".jpg,.jpeg,.png,.webp"
                >

            </div>

            <!--=============================
            Imagen 3
            ==============================-->

            <div class="campo">

                <label>

                    Imagen 3

                </label>

                <input
                    type="file"
                    name="imagen3"
                    accept=".jpg,.jpeg,.png,.webp"
                >

            </div>

        </div>

        <button
            type="submit"
            class="btn-publicar"
        >

            <i class="fa-solid fa-paper-plane"></i>

            Publicar Actividad

        </button>

    </form>

</div>

    <!--=====================================
        ACTIVIDADES
    =====================================-->

    <div class="card-listado">

        <h2>

            Actividades Publicadas

        </h2>

        <div
            id="contenedor-actividades"
        >

            <div class="mensaje-inicial">

                Seleccione un grupo para visualizar
                las actividades publicadas.

            </div>

        </div>

    </div>

</div>

<script
src="actividades.js"
></script>

<?php

include(
    "../../../includes/footer.php"
);

?>