<?php
/*=========================================================
    EDITAR DISPONIBILIDAD
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
    OBTENER ID DEL USUARIO
=========================================================*/

$id_usuario =
    (int) $_SESSION["id_usuario"];


/*=========================================================
    OBTENER ID DEL PSICÓLOGO
=========================================================*/

$sql_psicologo = "

    SELECT
        id_registro

    FROM administradores_psicologos

    WHERE id_usuario = ?
    AND activo = 1

    LIMIT 1

";


$stmt_psicologo =
    $conn->prepare($sql_psicologo);


if (!$stmt_psicologo) {

    die(
        "Error al preparar la consulta del psicólogo."
    );

}


$stmt_psicologo->bind_param(
    "i",
    $id_usuario
);


$stmt_psicologo->execute();


$resultado_psicologo =
    $stmt_psicologo->get_result();


/*=========================================================
    VERIFICAR PSICÓLOGO
=========================================================*/

if (
    $resultado_psicologo->num_rows === 0
) {

    $stmt_psicologo->close();

    header(
        "Location: index.php?error=no_psicologo"
    );

    exit();

}


$datos_psicologo =
    $resultado_psicologo->fetch_assoc();


$id_psicologo =
    (int) $datos_psicologo["id_registro"];


$stmt_psicologo->close();


/*=========================================================
    RECIBIR ID DE DISPONIBILIDAD
=========================================================*/

$id_disponibilidad =
    filter_input(
        INPUT_GET,
        "id",
        FILTER_VALIDATE_INT
    );


/*=========================================================
    VALIDAR ID
=========================================================*/

if (
    !$id_disponibilidad ||
    $id_disponibilidad <= 0
) {

    header(
        "Location: index.php?error=id"
    );

    exit();

}


/*=========================================================
    CONSULTAR DISPONIBILIDAD
=========================================================

    MUY IMPORTANTE:

    También comprobamos:

        id_disponibilidad
        +
        id_psicologo

    Esto evita que un psicólogo pueda editar
    la disponibilidad de otro psicólogo.

=========================================================*/

$sql = "

    SELECT
        id_disponibilidad,
        fecha,
        hora_inicio,
        hora_fin,
        estado

    FROM disponibilidad_psicologos

    WHERE id_disponibilidad = ?
    AND id_psicologo = ?

    LIMIT 1

";


$stmt =
    $conn->prepare($sql);


if (!$stmt) {

    die(
        "Error al preparar la consulta."
    );

}


$stmt->bind_param(
    "ii",
    $id_disponibilidad,
    $id_psicologo
);


$stmt->execute();


$resultado =
    $stmt->get_result();


/*=========================================================
    VERIFICAR DISPONIBILIDAD
=========================================================*/

if (
    $resultado->num_rows === 0
) {

    $stmt->close();

    header(
        "Location: index.php?error=no_encontrado"
    );

    exit();

}


$disponibilidad =
    $resultado->fetch_assoc();


$stmt->close();


/*=========================================================
    DATOS
=========================================================*/

$fecha =
    $disponibilidad["fecha"];


$hora_inicio =
    substr(
        $disponibilidad["hora_inicio"],
        0,
        5
    );


$hora_fin =
    substr(
        $disponibilidad["hora_fin"],
        0,
        5
    );


$estado =
    $disponibilidad["estado"];

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
        Editar disponibilidad | Psicología
    </title>


    <!--=====================================================
        CSS
    ======================================================-->

    <link
        rel="stylesheet"
        href="css/agenda.css"
    >

</head>


<body>


<!--=========================================================
    CONTENEDOR
=========================================================-->

<main class="agenda-container">


    <!--=====================================================
        ENCABEZADO
    ======================================================-->

    <header class="agenda-header">


        <div class="agenda-header-text">


            <div class="agenda-icon">

                ✏️

            </div>


            <div>

                <h1>
                    Editar disponibilidad
                </h1>


                <p>
                    Modifica la fecha o el horario de tu disponibilidad.
                </p>

            </div>


        </div>


        <!--=================================================
            REGRESAR
        ==================================================-->

        <a
            href="index.php"
            class="btn-nueva"
        >

            ←

            Regresar a mi agenda

        </a>


    </header>



    <!--=====================================================
        FORMULARIO
    ======================================================-->

    <section class="agenda-listado">


        <div class="listado-header">


            <h2>
                Datos de la disponibilidad
            </h2>


            <p>

                Modifica únicamente los datos que necesites
                actualizar.

            </p>


        </div>



        <!--=================================================
            FORMULARIO
        ==================================================-->

        <form
            action="actualizar.php"
            method="POST"
            id="formEditarDisponibilidad"
            class="form-agenda"
        >


            <!--=================================================
                ID OCULTO
            ==================================================-->

            <input
                type="hidden"
                name="id_disponibilidad"
                value="<?php echo $id_disponibilidad; ?>"
            >


            <!--=================================================
                FECHA
            ==================================================-->

            <div class="form-grupo">


                <label for="fecha">

                    Fecha

                </label>


                <input
                    type="date"
                    id="fecha"
                    name="fecha"
                    value="<?php echo htmlspecialchars($fecha); ?>"
                    min="<?php echo date('Y-m-d'); ?>"
                    required
                >


            </div>



            <!--=================================================
                HORA INICIO
            ==================================================-->

            <div class="form-grupo">


                <label for="hora_inicio">

                    Hora de inicio

                </label>


                <input
                    type="time"
                    id="hora_inicio"
                    name="hora_inicio"
                    value="<?php echo htmlspecialchars($hora_inicio); ?>"
                    required
                >


            </div>



            <!--=================================================
                HORA FIN
            ==================================================-->

            <div class="form-grupo">


                <label for="hora_fin">

                    Hora de finalización

                </label>


                <input
                    type="time"
                    id="hora_fin"
                    name="hora_fin"
                    value="<?php echo htmlspecialchars($hora_fin); ?>"
                    required
                >


            </div>



            <!--=================================================
                ESTADO ACTUAL
            ==================================================-->

            <div class="form-grupo">


                <label>

                    Estado actual

                </label>


                <?php if ($estado === "DISPONIBLE"): ?>


                    <div class="estado estado-disponible">

                        🟢

                        Disponible

                    </div>


                <?php else: ?>


                    <div class="estado estado-cerrado">

                        🔴

                        Cerrado

                    </div>


                <?php endif; ?>


                <small>

                    El estado se administra desde
                    el botón correspondiente en tu agenda.

                </small>


            </div>



            <!--=================================================
                ACCIONES
            ==================================================-->

            <div class="form-acciones">


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

                    💾

                    Guardar cambios

                </button>


            </div>


        </form>


    </section>


</main>



<!--=========================================================
    JAVASCRIPT
=========================================================-->

<script
    src="js/agenda.js"
></script>


</body>

</html>

