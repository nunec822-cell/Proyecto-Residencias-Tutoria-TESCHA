document.addEventListener("DOMContentLoaded", () => {

    /*=========================================================
        MODALES
    =========================================================*/

    const modalAviso =
        document.getElementById("modalAvisoCita");

    const modalAutenticacion =
        document.getElementById("modalAutenticacion");

    const modalExito =
        document.getElementById("modalCitaExitosa");


    /*=========================================================
        BOTONES MODAL AVISO
    =========================================================*/

    const cerrarModalAviso =
        document.getElementById("cerrarModalAviso");

    const cancelarSolicitud =
        document.getElementById("cancelarSolicitud");

    const continuarSolicitud =
        document.getElementById("continuarSolicitud");


    /*=========================================================
        BOTONES MODAL AUTENTICACIÓN
    =========================================================*/

    const cerrarModalAutenticacion =
        document.getElementById("cerrarModalAutenticacion");

    const regresarAutenticacion =
        document.getElementById("regresarAutenticacion");


    /*=========================================================
        FORMULARIO
    =========================================================*/

    const formulario =
        document.getElementById("formSolicitudCita");

    const idDisponibilidad =
        document.getElementById("idDisponibilidad");

    const mensajeSolicitud =
        document.getElementById("mensajeSolicitud");

    const btnConfirmarSolicitud =
        document.getElementById("btnConfirmarSolicitud");


    /*=========================================================
        PASSWORD
    =========================================================*/

    const password =
        document.getElementById("password");

    const mostrarPassword =
        document.getElementById("mostrarPassword");


    /*=========================================================
        MOTIVO
    =========================================================*/

    const motivo =
        document.getElementById("motivo");

    const contadorMotivo =
        document.getElementById("contadorMotivo");


    /*=========================================================
        ÉXITO
    =========================================================*/

    const btnEntendido =
        document.getElementById("btnEntendido");


    /*=========================================================
        DISPONIBILIDAD SELECCIONADA
    =========================================================*/

    let disponibilidadSeleccionada = null;


    /*=========================================================
        ABRIR MODAL
    =========================================================*/

    function abrirModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.add("activo");

        modal.setAttribute(
            "aria-hidden",
            "false"
        );

        document.body.style.overflow = "hidden";

    }


    /*=========================================================
        CERRAR MODAL
    =========================================================*/

    function cerrarModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.remove("activo");

        modal.setAttribute(
            "aria-hidden",
            "true"
        );

        document.body.style.overflow = "";

    }


    /*=========================================================
        BOTONES DE ESPACIOS
    =========================================================*/

    const botonesSolicitar =
        document.querySelectorAll(".btn-solicitar");


    botonesSolicitar.forEach((boton) => {

        boton.addEventListener("click", () => {

            disponibilidadSeleccionada =
                boton.dataset.id;

            abrirModal(modalAviso);

        });

    });


    /*=========================================================
        CERRAR AVISO
    =========================================================*/

    if (cerrarModalAviso) {

        cerrarModalAviso.addEventListener(
            "click",
            () => {

                cerrarModal(modalAviso);

                disponibilidadSeleccionada = null;

            }
        );

    }


    if (cancelarSolicitud) {

        cancelarSolicitud.addEventListener(
            "click",
            () => {

                cerrarModal(modalAviso);

                disponibilidadSeleccionada = null;

            }
        );

    }


    /*=========================================================
        CONTINUAR
    =========================================================*/

    if (continuarSolicitud) {

        continuarSolicitud.addEventListener(
            "click",
            () => {

                if (!disponibilidadSeleccionada) {

                    return;

                }


                idDisponibilidad.value =
                    disponibilidadSeleccionada;


                cerrarModal(modalAviso);

                limpiarMensaje();

                abrirModal(modalAutenticacion);

            }
        );

    }


    /*=========================================================
        REGRESAR A AVISO
    =========================================================*/

    if (regresarAutenticacion) {

        regresarAutenticacion.addEventListener(
            "click",
            () => {

                cerrarModal(modalAutenticacion);

                limpiarFormulario();

                abrirModal(modalAviso);

            }
        );

    }


    /*=========================================================
        CERRAR AUTENTICACIÓN
    =========================================================*/

    if (cerrarModalAutenticacion) {

        cerrarModalAutenticacion.addEventListener(
            "click",
            () => {

                cerrarModal(modalAutenticacion);

                limpiarFormulario();

                disponibilidadSeleccionada = null;

            }
        );

    }


    /*=========================================================
        MOSTRAR / OCULTAR PASSWORD
    =========================================================*/

    if (mostrarPassword) {

        mostrarPassword.addEventListener(
            "click",
            () => {

                if (password.type === "password") {

                    password.type = "text";

                    mostrarPassword.textContent = "🙈";

                } else {

                    password.type = "password";

                    mostrarPassword.textContent = "👁";

                }

            }
        );

    }


    /*=========================================================
        CONTADOR DE MOTIVO
    =========================================================*/

    if (motivo && contadorMotivo) {

        motivo.addEventListener(
            "input",
            () => {

                contadorMotivo.textContent =
                    motivo.value.length;

            }
        );

    }


    /*=========================================================
        ENVIAR SOLICITUD
    =========================================================*/

    if (formulario) {

        formulario.addEventListener(
            "submit",
            async (event) => {

                event.preventDefault();


                if (
                    !disponibilidadSeleccionada
                ) {

                    mostrarError(
                        "Selecciona nuevamente el espacio que deseas reservar."
                    );

                    return;

                }


                const datos =
                    new FormData(formulario);


                btnConfirmarSolicitud.disabled = true;

                btnConfirmarSolicitud.textContent =
                    "Verificando...";


                limpiarMensaje();


                try {

                    const respuesta =
                        await fetch(
                            "solicitar.php",
                            {
                                method: "POST",
                                body: datos
                            }
                        );


                    const resultado =
                        await respuesta.json();


                    if (resultado.success) {

                        cerrarModal(
                            modalAutenticacion
                        );


                        limpiarFormulario();


                        /*
                         * Guardamos que el alumno
                         * ya confirmó el mensaje.
                         *
                         * Posteriormente esta bandera
                         * también se podrá conectar
                         * con el módulo Citas.
                         */

                        sessionStorage.setItem(
                            "cita_modal_confirmida",
                            "true"
                        );


                        abrirModal(modalExito);


                    } else {

                        mostrarError(
                            resultado.message ||
                            "No fue posible registrar la solicitud."
                        );

                    }


                } catch (error) {

                    console.error(error);

                    mostrarError(
                        "Ocurrió un problema de comunicación con el servidor. Inténtalo nuevamente."
                    );

                } finally {

                    btnConfirmarSolicitud.disabled =
                        false;

                    btnConfirmarSolicitud.textContent =
                        "Confirmar solicitud";

                }

            }
        );

    }


    /*=========================================================
        BOTÓN ENTENDIDO
    =========================================================*/

    if (btnEntendido) {

        btnEntendido.addEventListener(
            "click",
            () => {

                cerrarModal(modalExito);

                /*
                 * Recargamos para que el espacio
                 * reservado desaparezca de la lista.
                 */

                window.location.reload();

            }
        );

    }


    /*=========================================================
        CERRAR CON ESC
    =========================================================*/

    document.addEventListener(
        "keydown",
        (event) => {

            if (
                event.key === "Escape"
            ) {

                if (
                    modalAviso &&
                    modalAviso.classList.contains("activo")
                ) {

                    cerrarModal(modalAviso);

                }

                if (
                    modalAutenticacion &&
                    modalAutenticacion.classList.contains("activo")
                ) {

                    cerrarModal(modalAutenticacion);

                }

            }

        }
    );


    /*=========================================================
        CLIC FUERA
    =========================================================*/

    [modalAviso, modalAutenticacion]
        .forEach((modal) => {

            if (!modal) {
                return;
            }

            modal.addEventListener(
                "click",
                (event) => {

                    if (
                        event.target === modal
                    ) {

                        cerrarModal(modal);

                    }

                }
            );

        });


    /*=========================================================
        MOSTRAR ERROR
    =========================================================*/

    function mostrarError(mensaje) {

        if (!mensajeSolicitud) {
            return;
        }

        mensajeSolicitud.textContent =
            mensaje;

        mensajeSolicitud.classList.add(
            "visible",
            "error"
        );

    }


    /*=========================================================
        LIMPIAR MENSAJE
    =========================================================*/

    function limpiarMensaje() {

        if (!mensajeSolicitud) {
            return;
        }

        mensajeSolicitud.textContent = "";

        mensajeSolicitud.classList.remove(
            "visible",
            "error",
            "exito"
        );

    }


    /*=========================================================
        LIMPIAR FORMULARIO
    =========================================================*/

    function limpiarFormulario() {

        if (!formulario) {
            return;
        }

        formulario.reset();

        if (contadorMotivo) {

            contadorMotivo.textContent = "0";

        }

        if (password) {

            password.type = "password";

        }

        if (mostrarPassword) {

            mostrarPassword.textContent = "👁";

        }

        limpiarMensaje();

    }

});