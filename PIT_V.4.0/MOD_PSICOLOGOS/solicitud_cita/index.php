<?php

/*=========================================================
    SESIÓN
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../../base_pit/conect_pit.php");


/*=========================================================
    TOKEN DE SEGURIDAD
=========================================================*/

if (empty($_SESSION['csrf_solicitud_cita'])) {

    $_SESSION['csrf_solicitud_cita'] =
        bin2hex(random_bytes(32));

}

$csrf_token = $_SESSION['csrf_solicitud_cita'];


/*=========================================================
    HEADER
=========================================================*/

include("../../../includes/header.php");


/*=========================================================
    CONSULTAR ESPACIOS DISPONIBLES
=========================================================*/

$sql = "
    SELECT
        id_disponibilidad,
        fecha,
        hora_inicio,
        hora_fin,
        cupos_disponibles
    FROM disponibilidad_psicologos
    WHERE estado = 'DISPONIBLE'
      AND cupos_disponibles > 0
      AND (
            fecha > CURDATE()
            OR (
                fecha = CURDATE()
                AND hora_inicio > CURTIME()
            )
      )
    ORDER BY fecha ASC, hora_inicio ASC
";

$resultado = $conn->query($sql);

?>
<div class="separador"></div>

<?php include("../../../includes/sidebar-sist.php"); ?>

<link rel="stylesheet" href="css/solicitud_cita.css">


<main class="solicitud-cita">


    <!-- =====================================================
         HERO
    ====================================================== -->

    <section class="hero-cita">

        <div class="hero-cita-contenido">

            <span class="hero-etiqueta">
                Departamento de Psicología
            </span>

            <h1>
                Solicita tu cita de atención psicológica
            </h1>

            <p>
                Encuentra un espacio disponible para realizar tu
                primera canalización al Departamento de Psicología.
            </p>

        </div>

    </section>


    <!-- =====================================================
         AVISO IMPORTANTE
    ====================================================== -->

    <section class="aviso-importante">

        <div class="aviso-icono">
            !
        </div>

        <div class="aviso-contenido">

            <h2>
                Antes de solicitar tu cita
            </h2>

            <p>
                Esta plataforma tiene como finalidad facilitar tu
                <strong>primera canalización al Departamento de Psicología</strong>.
            </p>

            <p>
                El espacio que selecciones será reservado para ti.
                Por ello, es muy importante que
                <strong>asistas a tu cita</strong>, ya que ese horario
                podría ser utilizado por otro estudiante.
            </p>

            <p>
                Después de tu primera atención, tu psicólogo será quien
                te indique directamente los acuerdos, seguimiento y,
                en caso de ser necesario, las próximas citas.
            </p>

            <div class="aviso-cancelacion">

                <strong>¿Necesitas cancelar?</strong>

                <span>
                    Para cancelar una cita deberás acudir al área de
                    Desarrollo Académico – Psicología.
                </span>

            </div>

        </div>

    </section>


    <!-- =====================================================
         ESPACIOS
    ====================================================== -->

    <section class="espacios-section">

        <div class="seccion-titulo">

            <div>

                <span class="titulo-etiqueta">
                    Disponibilidad
                </span>

                <h2>
                    Espacios disponibles
                </h2>

                <p>
                    Selecciona el horario que mejor se adapte a ti.
                </p>

            </div>

        </div>


        <?php if ($resultado && $resultado->num_rows > 0): ?>

            <div class="espacios-grid">

                <?php while ($espacio = $resultado->fetch_assoc()): ?>

                    <?php

                    $fecha = new DateTime($espacio['fecha']);

                    $dias = [
                        'Sunday'    => 'Domingo',
                        'Monday'    => 'Lunes',
                        'Tuesday'   => 'Martes',
                        'Wednesday' => 'Miércoles',
                        'Thursday'  => 'Jueves',
                        'Friday'    => 'Viernes',
                        'Saturday'  => 'Sábado'
                    ];

                    $meses = [
                        'January'   => 'enero',
                        'February'  => 'febrero',
                        'March'     => 'marzo',
                        'April'     => 'abril',
                        'May'       => 'mayo',
                        'June'      => 'junio',
                        'July'      => 'julio',
                        'August'    => 'agosto',
                        'September' => 'septiembre',
                        'October'   => 'octubre',
                        'November'  => 'noviembre',
                        'December'  => 'diciembre'
                    ];

                    $diaSemana = $dias[$fecha->format('l')];
                    $mes = $meses[$fecha->format('F')];

                    $fechaFormateada =
                        $diaSemana . ' ' .
                        $fecha->format('d') . ' de ' .
                        $mes . ' de ' .
                        $fecha->format('Y');

                    $horaInicio = date(
                        'H:i',
                        strtotime($espacio['hora_inicio'])
                    );

                    $horaFin = date(
                        'H:i',
                        strtotime($espacio['hora_fin'])
                    );

                    ?>

                    <article class="espacio-card">

                        <div class="card-disponibilidad">

                            <span class="estado-dot"></span>

                            Disponible

                        </div>


                        <div class="card-icono">
                            🗓️
                        </div>


                        <div class="card-contenido">

                            <span class="card-label">
                                Fecha
                            </span>

                            <h3>
                                <?= htmlspecialchars($fechaFormateada) ?>
                            </h3>


                            <span class="card-label">
                                Horario
                            </span>

                            <div class="horario">

                                <span>
                                    <?= htmlspecialchars($horaInicio) ?>
                                </span>

                                <span class="horario-separador">
                                    —
                                </span>

                                <span>
                                    <?= htmlspecialchars($horaFin) ?>
                                </span>

                            </div>


                            <div class="card-aviso">

                                <span>✓</span>

                                <p>
                                    Este espacio está disponible
                                    para solicitar atención.
                                </p>

                            </div>

                        </div>


                        <button
                            type="button"
                            class="btn-solicitar"
                            data-id="<?= (int)$espacio['id_disponibilidad'] ?>"
                        >

                            Solicitar cita

                            <span>→</span>

                        </button>

                    </article>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="sin-disponibilidad">

                <div class="sin-disponibilidad-icono">
                    🗓️
                </div>

                <h3>
                    Por el momento no hay espacios disponibles
                </h3>

                <p>
                    Actualmente no existen horarios disponibles para
                    solicitar una primera atención psicológica.
                </p>

                <span>
                    Te recomendamos consultar nuevamente más tarde.
                </span>

            </div>

        <?php endif; ?>

    </section>


</main>


<!-- =====================================================
     MODAL 1
     AVISO ANTES DE RESERVAR
====================================================== -->

<div
    id="modalAvisoCita"
    class="modal-overlay"
    aria-hidden="true"
>

    <div
        class="modal-cita"
        role="dialog"
        aria-modal="true"
    >

        <button
            type="button"
            class="modal-cerrar"
            id="cerrarModalAviso"
        >
            ×
        </button>


        <div class="modal-icono">
            !
        </div>


        <h2>
            Antes de continuar
        </h2>


        <p class="modal-principal">

            Estás a punto de reservar un espacio de
            atención psicológica.

        </p>


        <p>

            <strong>
                Es muy importante que asistas a tu cita.
            </strong>

            Este horario quedará reservado para ti y podría
            ser utilizado por otro estudiante.

        </p>


        <p>

            Recuerda que esta plataforma está destinada
            principalmente a tu
            <strong>primera canalización</strong>.

        </p>


        <div class="modal-nota">

            Después de tu primera atención, tu psicólogo
            te indicará directamente los acuerdos y
            próximas citas que correspondan.

        </div>


        <div class="modal-acciones">

            <button
                type="button"
                class="btn-modal-secundario"
                id="cancelarSolicitud"
            >
                Regresar
            </button>

            <button
                type="button"
                class="btn-modal-principal"
                id="continuarSolicitud"
            >
                Continuar
            </button>

        </div>

    </div>

</div>


<!-- =====================================================
     MODAL 2
     AUTENTICACIÓN
====================================================== -->

<div
    id="modalAutenticacion"
    class="modal-overlay"
    aria-hidden="true"
>

    <div
        class="modal-cita modal-autenticacion"
        role="dialog"
        aria-modal="true"
    >

        <button
            type="button"
            class="modal-cerrar"
            id="cerrarModalAutenticacion"
        >
            ×
        </button>


        <div class="modal-icono modal-icono-seguridad">
            🔐
        </div>


        <h2>
            Confirmar tu identidad
        </h2>


        <p class="modal-principal">

            Para reservar este espacio necesitamos verificar
            tus datos de acceso al PIT.

        </p>


        <form
            id="formSolicitudCita"
            autocomplete="off"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($csrf_token) ?>"
            >


            <input
                type="hidden"
                name="id_disponibilidad"
                id="idDisponibilidad"
            >


            <div class="campo-formulario">

                <label for="usuario">
                    Usuario del PIT
                </label>

                <input
                    type="text"
                    id="usuario"
                    name="usuario"
                    maxlength="50"
                    required
                    autocomplete="username"
                    placeholder="Ingresa tu usuario"
                >

            </div>


            <div class="campo-formulario">

                <label for="password">
                    Contraseña del PIT
                </label>

                <div class="password-contenedor">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        maxlength="255"
                        required
                        autocomplete="current-password"
                        placeholder="Ingresa tu contraseña"
                    >

                    <button
                        type="button"
                        id="mostrarPassword"
                        class="btn-mostrar-password"
                        aria-label="Mostrar contraseña"
                    >
                        👁
                    </button>

                </div>

            </div>


            <div class="campo-formulario">

                <label for="motivo">
                    Motivo de la solicitud
                </label>

                <textarea
                    id="motivo"
                    name="motivo"
                    maxlength="1000"
                    rows="4"
                    required
                    placeholder="Cuéntanos brevemente el motivo por el que deseas solicitar atención psicológica."
                ></textarea>

                <div class="contador-caracteres">
                    <span id="contadorMotivo">0</span>/1000
                </div>

            </div>


            <div
                id="mensajeSolicitud"
                class="mensaje-solicitud"
            ></div>


            <div class="modal-acciones">

                <button
                    type="button"
                    class="btn-modal-secundario"
                    id="regresarAutenticacion"
                >
                    Regresar
                </button>

                <button
                    type="submit"
                    class="btn-modal-principal"
                    id="btnConfirmarSolicitud"
                >
                    Confirmar solicitud
                </button>

            </div>

        </form>

    </div>

</div>


<!-- =====================================================
     MODAL 3
     CITA EXITOSA
====================================================== -->

<div
    id="modalCitaExitosa"
    class="modal-overlay"
    aria-hidden="true"
>

    <div
        class="modal-cita modal-exito"
        role="dialog"
        aria-modal="true"
    >

        <div class="modal-exito-icono">
            ✓
        </div>


        <h2>
            ¡Tu cita ha sido exitosa!
        </h2>


        <p class="modal-principal">

            Tu solicitud de atención psicológica
            ha sido registrada correctamente.

        </p>


        <div class="modal-nota">

            Podrás consultar los detalles de tu cita y conocer
            quién es tu psicólogo asignado entrando al portal pit 
            con tu usuario y contraseña  el apartado 
            <strong>“Citas”</strong> de tu menú lateral.

        </div>


        <p class="modal-recordatorio">

            Recuerda asistir puntualmente a tu cita.
            Este espacio fue reservado especialmente para ti.

        </p>


        <button
            type="button"
            class="btn-entendido"
            id="btnEntendido"
        >
            Entendido
        </button>

    </div>

</div>


<script src="js/solicitud_cita.js"></script>


<?php include("../../../includes/footer.php"); ?>