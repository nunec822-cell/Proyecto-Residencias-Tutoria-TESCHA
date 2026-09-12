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
href="historial.css"
>

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
>

<div class="contenedor-historial">

    <!--=====================================
        ENCABEZADO
    =====================================-->

    <div class="encabezado">

        <h1>

            Historial de Canalizaciones

        </h1>

        <p>

            Consulte el historial de las
            canalizaciones finalizadas de
            sus tutorados. Las canalizaciones
            activas no aparecerán en este
            apartado.

        </p>

    </div>

    <!--=====================================
        INFORMACIÓN
    =====================================-->

    <div class="card-informacion">

        <h2>

            <i
            class="fa-solid fa-book"
            ></i>

            Información General

        </h2>

        <ul>

            <li>

                Seleccione un grupo para
                consultar a los tutorados
                asignados.

            </li>

            <li>

                El historial únicamente
                mostrará canalizaciones
                con estado
                <strong>
                    CERRADO
                </strong>.

            </li>

            <li>

                Si un alumno ha sido
                canalizado en varias
                ocasiones, podrá consultar
                todas sus atenciones
                anteriores.

            </li>

            <li>

                Las canalizaciones activas
                continúan mostrándose en el
                módulo
                <strong>
                    Canalizaciones
                </strong>.

            </li>

        </ul>

    </div>

    <!--=====================================
        FILTRO DE GRUPOS
    =====================================-->

    <div class="card-grupo">

        <label>

            Seleccione un grupo:

        </label>

        <select
            id="grupo"
        >

            <option value="">

                Seleccione...

            </option>

        </select>

    </div>

    <!--=====================================
        ALUMNOS
    =====================================-->

    <div
        id="contenedor-alumnos"
    >

        <div class="mensaje-inicial">

            Seleccione un grupo para
            visualizar los tutorados.

        </div>

    </div>

    <!--=====================================
    MODAL HISTORIAL
======================================-->

<div
    id="modal-historial"
    class="modal-historial"
>

    <div class="modal-contenido">

        <span
            id="cerrar-modal"
            class="cerrar-modal"
        >
            &times;
        </span>

        <div
            id="contenedor-historial"
        >

        </div>

    </div>

</div>

</div>
<script>
document.addEventListener(
    "DOMContentLoaded",
    () => {

        const modal =
            document.getElementById(
                "modal-historial"
            );

        const cerrar =
            document.getElementById(
                "cerrar-modal"
            );

        cerrar.addEventListener(
            "click",
            () => {

                modal.style.display =
                    "none";

            }
        );

        window.addEventListener(
            "click",
            (e) => {

                if(
                    e.target === modal
                ){

                    modal.style.display =
                        "none";

                }

            }
        );

    }
);
</script>
<script src="historial.js"></script>

<?php

include(
    "../../../includes/footer.php"
);

?>