<?php

require_once("../../sesion.php");

if(
    !in_array(
        "TUTORADO",
        $_SESSION["roles"]
    )
){

    header(
        "Location: ../../indexloguin.php"
    );

    exit();

}

include(
    "../../includes_pit/sidebar_tutorado.php"
);

?>



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

            Mis Actividades

        </h1>

        <p>

            En este apartado encontrarás todas las
            actividades publicadas por tu tutor para
            tu grupo. Lee cuidadosamente las
            instrucciones y envía tu evidencia
            correspondiente.

        </p>

    </div>

    <!--=====================================
        INSTRUCCIONES
    =====================================-->

    <div class="card-instrucciones">

        <h2>

            <i class="fa-solid fa-circle-info"></i>

            Indicaciones

        </h2>

        <ul>

            <li>

                Revisa completamente la actividad
                antes de enviar tu evidencia.

            </li>

            <li>

                Puedes consultar el PDF,
                las imágenes o el enlace
                proporcionado por tu tutor.

            </li>

            <li>

                La evidencia deberá enviarse
                únicamente en formato
                <strong>PDF.</strong>

            </li>

            <li>

                El tamaño máximo permitido
                para el archivo es de
                <strong>2 MB.</strong>

            </li>

            <li>

                Una vez enviada la evidencia,
                podrás visualizarla y
                reemplazarla mientras la
                actividad permanezca activa.

            </li>

        </ul>

        <div class="alerta-importante">

            <strong>Importante:</strong>

            Es responsabilidad del tutorado
            verificar que el archivo enviado
            corresponda a la actividad solicitada
            y que pueda abrirse correctamente.

        </div>

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

                Cargando actividades...

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