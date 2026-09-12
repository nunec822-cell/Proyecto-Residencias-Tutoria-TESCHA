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
href="canalizaciones.css"
>

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
>

<div class="contenedor-canalizaciones">

    <!--=====================================
        ENCABEZADO
    =====================================-->

    <div class="encabezado">

        <h1>

            Canalizaciones

        </h1>

        <p>

            Consulte las canalizaciones asignadas y
            registre las acciones que realizará para
            brindar atención y seguimiento a sus
            tutorados.

        </p>

    </div>


    <!--=====================================
        INSTRUCCIONES
    =====================================-->

    <div class="card-instrucciones">

        <h2>

            <i class="fa-solid fa-circle-info"></i>

            Instrucciones para la atención de canalizaciones

        </h2>

        <ol>

            <li>

                Revise las canalizaciones que le han sido
                asignadas por el Departamento de Desarrollo
                Académico.

            </li>

            <li>

                Cuando vaya a comenzar la atención de un
                alumno, presione el botón

                <strong>
                    "Tomar Canalización"
                </strong>.

                Esto cambiará el estado de la canalización
                a seguimiento.

            </li>

            <li>

                Después de tomar la canalización, registre
                las

                <strong>
                    acciones que realizará
                </strong>

                para atender la situación del tutorado.

            </li>

            <li>

                Las acciones deben describir de manera clara
                qué hará como tutor para brindar atención
                al tutorado.

            </li>

            <li>

                Una vez concluida la atención, podrá
                finalizar la canalización mediante el botón

                <strong>
                    "Finalizar Canalización"
                </strong>.

            </li>

        </ol>


        <!--=====================================
            EJEMPLO
        =====================================-->

        <div class="ejemplo-acciones">

            <h3>

                <i class="fa-solid fa-lightbulb"></i>

                Ejemplo de acciones

            </h3>

            <p>

                Si la canalización indica que el tutorado
                presenta inasistencias y bajo desempeño,
                las acciones podrían ser:

            </p>

            <ul>

                <li>

                    Entrevistar al tutorado para conocer
                    la situación.

                </li>

                <li>

                    Dar seguimiento a sus asistencias.

                </li>

                <li>

                    Orientarlo sobre estrategias de
                    estudio.

                </li>

                <li>

                    Verificar su desempeño académico
                    posteriormente.

                </li>

            </ul>

        </div>


        <!--=====================================
            ALERTA
        =====================================-->

        <div class="alerta-importante">

            <strong>

                <i class="fa-solid fa-triangle-exclamation"></i>

                Importante:

            </strong>

            Las acciones registradas corresponden a las
            actividades que usted como tutor realizará
            para atender la canalización.

            Procure escribirlas de forma clara y específica.

        </div>

    </div>


    <!--=====================================
        RESUMEN
    =====================================-->

    <div class="card-estadisticas">

        <h2>

            <i class="fa-solid fa-chart-line"></i>

            Resumen de Canalizaciones

        </h2>

        <p>

            He recibido

            <strong id="totalGeneral">

                0

            </strong>

            canalizaciones.

        </p>

        <p>

            <strong id="totalPendientes">

                0

            </strong>

            siguen pendientes,

            <strong id="totalProceso">

                0

            </strong>

            están en proceso y

            <strong id="totalAtendidas">

                0

            </strong>

            ya fueron finalizadas.

        </p>

    </div>


    <!--=====================================
        RESULTADOS
    =====================================-->

    <div
        id="contenedor-canalizaciones"
        class="contenedor-tarjetas"
    >

        <div class="mensaje-inicial">

            <i class="fa-solid fa-spinner fa-spin"></i>

            Cargando canalizaciones...

        </div>

    </div>

</div>


<script src="canalizaciones.js"></script>


<?php

include(
    "../../../includes/footer.php"
);

?>