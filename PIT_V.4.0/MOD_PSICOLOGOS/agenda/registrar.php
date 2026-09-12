
<?php
/*=========================================================
    REGISTRAR DISPONIBILIDAD
    SIST V.4.0 - PIT V.4.0
=========================================================*/

/*=========================================================
    SESIÓN
=========================================================*/

require_once("../../sesion.php");


/*=========================================================
    VALIDAR ROL
=========================================================*/

if (
    !isset($_SESSION["roles"]) ||
    !in_array("PSICOLOGO", $_SESSION["roles"])
) {

    header("Location: ../../indexloguin.php");
    exit();

}


/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../../base_pit/conect_pit.php");


/*=========================================================
    FECHA ACTUAL
=========================================================*/

$fecha_minima = date("Y-m-d");

?>
<?php include("../../includes_pit/sidebar_psicologos.php"); ?>
<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- CONTENEDOR PRINCIPAL -->
<div class="separador"></div>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Registrar disponibilidad | Psicología
    </title>


    <!--=====================================================
        ESTILOS DE AGENDA
    ======================================================-->

    <link
        rel="stylesheet"
        href="css/agenda.css"
    >

</head>


<body>


<!--=========================================================
    CONTENEDOR PRINCIPAL
=========================================================-->

<main class="agenda-container">


    <!--=====================================================
        ENCABEZADO
    ======================================================-->

    <section class="agenda-header">

        <div class="agenda-header-text">

            <span class="agenda-icon">
                📅
            </span>

            <div>

                <h1>
                    Registrar disponibilidad
                </h1>

                <p>
                    Agrega un horario en el que estarás
                    disponible para atención psicológica.
                </p>

            </div>

        </div>


        <!--=================================================
            REGRESAR
        ==================================================-->

        <a
            href="index.php"
            class="btn-regresar"
        >

            ← Volver a mi agenda

        </a>

    </section>



    <!--=====================================================
        FORMULARIO
    ======================================================-->

    <section class="formulario-contenedor">


        <div class="formulario-card">


            <!--=================================================
                ENCABEZADO DEL FORMULARIO
            ==================================================-->

            <div class="formulario-header">

                <div class="formulario-icon">
                    🧠
                </div>

                <div>

                    <h2>
                        Nueva disponibilidad
                    </h2>

                    <p>
                        Indica la fecha y el horario
                        en el que estarás disponible.
                    </p>

                </div>

            </div>



            <!--=================================================
                FORMULARIO
            ==================================================-->

            <form
                action="guardar.php"
                method="POST"
                id="formDisponibilidad"
                autocomplete="off"
            >


                <!--=============================================
                    FECHA
                ==============================================-->

                <div class="campo-formulario">

                    <label for="fecha">

                        📅 Fecha disponible

                    </label>


                    <input
                        type="date"
                        name="fecha"
                        id="fecha"
                        min="<?php echo $fecha_minima; ?>"
                        required
                    >


                    <small>

                        Selecciona el día en el que
                        estarás disponible.

                    </small>

                </div>



                <!--=============================================
                    HORARIOS
                ==============================================-->

                <div class="horarios-grid">


                    <!--=========================================
                        HORA INICIO
                    ==========================================-->

                    <div class="campo-formulario">

                        <label for="hora_inicio">

                            🕐 Hora de inicio

                        </label>


                        <input
                            type="time"
                            name="hora_inicio"
                            id="hora_inicio"
                            required
                        >


                        <small>

                            Hora en la que comienza
                            tu disponibilidad.

                        </small>

                    </div>



                    <!--=========================================
                        HORA FIN
                    ==========================================-->

                    <div class="campo-formulario">

                        <label for="hora_fin">

                            🕐 Hora de finalización

                        </label>


                        <input
                            type="time"
                            name="hora_fin"
                            id="hora_fin"
                            required
                        >


                        <small>

                            Hora en la que termina
                            tu disponibilidad.

                        </small>

                    </div>


                </div>



                <!--=================================================
                    INFORMACIÓN
                ==================================================-->

                <div class="aviso-formulario">

                    <div class="aviso-icon">
                        💡
                    </div>

                    <div>

                        <strong>
                            Importante
                        </strong>

                        <p>

                            En este apartado solamente registrarás
                            tu horario disponible. La duración y
                            administración de las citas se manejarán
                            posteriormente en el módulo correspondiente.

                        </p>

                    </div>

                </div>



                <!--=================================================
                    BOTONES
                ==================================================-->

                <div class="formulario-acciones">


                    <a
                        href="index.php"
                        class="btn-cancelar"
                    >

                        Cancelar

                    </a>


                    <button
                        type="submit"
                        class="btn-guardar"
                    >

                        <span>
                            ✓
                        </span>

                        Guardar disponibilidad

                    </button>


                </div>


            </form>


        </div>


    </section>


</main>



<!--=========================================================
    JAVASCRIPT
=========================================================-->

<script src="js/agenda.js"></script>


</body>

</html>
