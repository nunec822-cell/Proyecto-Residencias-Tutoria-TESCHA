/*=========================================================
    CITAS DEL PSICÓLOGO
    SIST V.4.0 - PIT V4.0
=========================================================*/


document.addEventListener(
    "DOMContentLoaded",
    function () {


        /*=====================================================
            MODAL DE CONFIRMACIÓN
        =====================================================*/

        const botones =
            document.querySelectorAll(
                ".btn-cambiar-estado"
            );


        /*=====================================================
            CONFIGURACIÓN DE ACCIONES
        =====================================================*/

        const acciones = {

            confirmar: {

                titulo:
                    "¿Confirmar cita?",

                mensaje:
                    "La cita será confirmada y el tutorado podrá verla como una cita confirmada.",

                boton:
                    "Sí, confirmar",

                clase:
                    "modal-confirmar"

            },


            cancelar: {

                titulo:
                    "¿Cancelar cita?",

                mensaje:
                    "La cita será cancelada. El tutorado podrá volver a solicitar una nueva cita.",

                boton:
                    "Sí, cancelar",

                clase:
                    "modal-cancelar"

            },


            atendiendo: {

                titulo:
                    "¿Iniciar atención?",

                mensaje:
                    "La cita cambiará a estado de atención. Esto indica que el tutorado ya se encuentra siendo atendido.",

                boton:
                    "Sí, iniciar",

                clase:
                    "modal-atendiendo"

            },


            no_asistio: {

                titulo:
                    "¿Registrar inasistencia?",

                mensaje:
                    "La cita se marcará como 'No asistió'. Verifica que el tutorado efectivamente no haya acudido.",

                boton:
                    "Sí, registrar",

                clase:
                    "modal-no-asistio"

            },


            atendida: {

                titulo:
                    "¿Finalizar atención?",

                mensaje:
                    "La cita se marcará como atendida y quedará registrada en el historial.",

                boton:
                    "Sí, finalizar",

                clase:
                    "modal-atendida"

            }

        };


        /*=====================================================
            CREAR MODAL DE CONFIRMACIÓN
        =====================================================*/

        const overlay =
            document.createElement(
                "div"
            );


        overlay.id =
            "modalConfirmacionCita";


        overlay.className =
            "modal-cita-overlay";


        overlay.innerHTML = `

            <div class="modal-cita">

                <button
                    type="button"
                    class="modal-cita-cerrar"
                    id="cerrarModalCita"
                >
                    ×
                </button>


                <div
                    class="modal-cita-icono"
                    id="modalCitaIcono"
                >

                    <i class="fas fa-circle-question"></i>

                </div>


                <div class="modal-cita-contenido">

                    <span class="modal-cita-etiqueta">
                        GESTIÓN DE CITA
                    </span>


                    <h2 id="modalCitaTitulo">
                        ¿Confirmar cita?
                    </h2>


                    <p id="modalCitaMensaje">
                        ¿Deseas realizar esta acción?
                    </p>

                </div>


                <div class="modal-cita-acciones">

                    <button
                        type="button"
                        class="modal-btn modal-btn-regresar"
                        id="cancelarModalCita"
                    >

                        <i class="fas fa-arrow-left"></i>

                        Regresar

                    </button>


                    <button
                        type="button"
                        class="modal-btn modal-btn-confirmar"
                        id="confirmarModalCita"
                    >

                        <i class="fas fa-check"></i>

                        Confirmar

                    </button>

                </div>

            </div>

        `;


        document.body.appendChild(
            overlay
        );


        /*=====================================================
            ELEMENTOS
        =====================================================*/

        const modalTitulo =
            document.getElementById(
                "modalCitaTitulo"
            );


        const modalMensaje =
            document.getElementById(
                "modalCitaMensaje"
            );


        const modalIcono =
            document.getElementById(
                "modalCitaIcono"
            );


        const botonConfirmar =
            document.getElementById(
                "confirmarModalCita"
            );


        const botonCerrar =
            document.getElementById(
                "cerrarModalCita"
            );


        const botonCancelar =
            document.getElementById(
                "cancelarModalCita"
            );


        /*=====================================================
            VARIABLES
        =====================================================*/

        let citaSeleccionada =
            null;


        let accionSeleccionada =
            null;


        /*=====================================================
            ABRIR MODAL DE CONFIRMACIÓN
        =====================================================*/

        function abrirModal(
            boton
        ) {

            const id =
                boton.dataset.id;


            const accion =
                boton.dataset.accion;


            if (
                !id ||
                !accion ||
                !acciones[accion]
            ) {

                return;

            }


            citaSeleccionada =
                id;


            accionSeleccionada =
                accion;


            const configuracion =
                acciones[accion];


            modalTitulo.textContent =
                configuracion.titulo;


            modalMensaje.textContent =
                configuracion.mensaje;


            botonConfirmar.innerHTML = `

                <i class="fas fa-check"></i>

                ${configuracion.boton}

            `;


            overlay.className =
                "modal-cita-overlay " +
                configuracion.clase;


            let icono =
                "fa-circle-question";


            switch (
                accion
            ) {

                case "confirmar":

                    icono =
                        "fa-circle-check";

                    break;


                case "cancelar":

                    icono =
                        "fa-ban";

                    break;


                case "atendiendo":

                    icono =
                        "fa-user-doctor";

                    break;


                case "no_asistio":

                    icono =
                        "fa-user-xmark";

                    break;


                case "atendida":

                    icono =
                        "fa-check-double";

                    break;

            }


            modalIcono.innerHTML = `

                <i class="fas ${icono}"></i>

            `;


            botonConfirmar.disabled =
                false;


            document.body.classList.add(
                "modal-cita-abierto"
            );


            overlay.classList.add(
                "mostrar"
            );

        }


        /*=====================================================
            CERRAR MODAL DE CONFIRMACIÓN
        =====================================================*/

        function cerrarModal() {

            overlay.classList.remove(
                "mostrar"
            );


            document.body.classList.remove(
                "modal-cita-abierto"
            );


            citaSeleccionada =
                null;


            accionSeleccionada =
                null;


            botonConfirmar.disabled =
                false;

        }


        /*=====================================================
            EVENTOS BOTONES
        =====================================================*/

        botones.forEach(
            function (
                boton
            ) {

                boton.addEventListener(
                    "click",
                    function () {

                        abrirModal(
                            boton
                        );

                    }
                );

            }
        );


        /*=====================================================
            CERRAR
        =====================================================*/

        botonCerrar.addEventListener(
            "click",
            function () {

                cerrarModal();

            }
        );


        botonCancelar.addEventListener(
            "click",
            function () {

                cerrarModal();

            }
        );


        /*=====================================================
            CERRAR AL HACER CLICK FUERA
        =====================================================*/

        overlay.addEventListener(
            "click",
            function (
                evento
            ) {

                if (
                    evento.target ===
                    overlay
                ) {

                    cerrarModal();

                }

            }
        );


        /*=====================================================
            TECLA ESC
        =====================================================*/

        document.addEventListener(
            "keydown",
            function (
                evento
            ) {

                if (
                    evento.key === "Escape" &&
                    overlay.classList.contains(
                        "mostrar"
                    )
                ) {

                    cerrarModal();

                }

            }
        );


        /*=====================================================
            CONFIRMAR ACCIÓN
        =====================================================*/

        botonConfirmar.addEventListener(
            "click",
            function () {


                if (
                    !citaSeleccionada ||
                    !accionSeleccionada
                ) {

                    return;

                }


                botonConfirmar.disabled =
                    true;


                botonConfirmar.innerHTML = `

                    <i class="fas fa-spinner fa-spin"></i>

                    Procesando...

                `;


                const url =

                    "cambiar_estado.php" +

                    "?id=" +

                    encodeURIComponent(
                        citaSeleccionada
                    ) +

                    "&accion=" +

                    encodeURIComponent(
                        accionSeleccionada
                    );


                window.location.href =
                    url;

            }
        );



        /*=====================================================
            MODAL DE RESULTADO
        =====================================================*/

        const modalResultado =
            document.getElementById(
                "modalResultadoCita"
            );


        if (
            modalResultado
        ) {


            const mostrar =
                modalResultado.dataset.mostrar;


            const tipo =
                modalResultado.dataset.tipo;


            const resultadoIcono =
                document.getElementById(
                    "resultadoCitaIcono"
                );


            const resultadoTitulo =
                document.getElementById(
                    "resultadoCitaTitulo"
                );


            const resultadoMensaje =
                document.getElementById(
                    "resultadoCitaMensaje"
                );


            const cerrarResultado =
                document.getElementById(
                    "cerrarResultadoCita"
                );


            const continuarResultado =
                document.getElementById(
                    "continuarResultadoCita"
                );


            /*=================================================
                CONFIGURACIÓN DE RESULTADOS
            =================================================*/

            const resultados = {


                confirmada: {

                    icono:
                        "fa-circle-check",

                    titulo:
                        "Cita confirmada",

                    mensaje:
                        "La cita fue confirmada correctamente.",

                    clase:
                        "resultado-confirmada"

                },


                cancelada: {

                    icono:
                        "fa-ban",

                    titulo:
                        "Cita cancelada",

                    mensaje:
                        "La cita fue cancelada correctamente.",

                    clase:
                        "resultado-cancelada"

                },


                atencion_iniciada: {

                    icono:
                        "fa-user-doctor",

                    titulo:
                        "Atención iniciada",

                    mensaje:
                        "La cita ahora se encuentra en estado de atención.",

                    clase:
                        "resultado-atendiendo"

                },


                atencion_finalizada: {

                    icono:
                        "fa-check-double",

                    titulo:
                        "Atención finalizada",

                    mensaje:
                        "La atención fue finalizada y quedó registrada correctamente.",

                    clase:
                        "resultado-atendida"

                },


                no_asistio: {

                    icono:
                        "fa-user-xmark",

                    titulo:
                        "Inasistencia registrada",

                    mensaje:
                        "La inasistencia del tutorado fue registrada correctamente.",

                    clase:
                        "resultado-no-asistio"

                }

            };


            /*=================================================
                ABRIR RESULTADO
            =================================================*/

            function abrirResultado() {


                if (
                    !resultados[tipo]
                ) {

                    return;

                }


                const configuracion =
                    resultados[tipo];


                resultadoIcono.innerHTML = `

                    <i class="fas ${configuracion.icono}"></i>

                `;


                resultadoTitulo.textContent =
                    configuracion.titulo;


                /*
                    Buscamos el nombre directamente
                    en el contenido de la página.

                    El PHP coloca el nombre en un
                    atributo del modal.
                */

                const nombre =
                    modalResultado.dataset.nombre;


                let mensajeFinal =
                    configuracion.mensaje;


                if (
                    nombre &&
                    nombre.trim() !== ""
                ) {


                    if (
                        tipo === "confirmada"
                    ) {

                        mensajeFinal =
                            "La cita de " +
                            nombre +
                            " fue confirmada correctamente.";

                    }


                    else if (
                        tipo === "cancelada"
                    ) {

                        mensajeFinal =
                            "La cita de " +
                            nombre +
                            " fue cancelada correctamente.";

                    }


                    else if (
                        tipo === "atencion_iniciada"
                    ) {

                        mensajeFinal =
                            "La cita de " +
                            nombre +
                            " ahora se encuentra en estado de atención.";

                    }


                    else if (
                        tipo === "atencion_finalizada"
                    ) {

                        mensajeFinal =
                            "La atención de " +
                            nombre +
                            " fue finalizada correctamente.";

                    }


                    else if (
                        tipo === "no_asistio"
                    ) {

                        mensajeFinal =
                            "La inasistencia de " +
                            nombre +
                            " fue registrada correctamente.";

                    }

                }


                resultadoMensaje.textContent =
                    mensajeFinal;


                modalResultado.className =
                    "modal-resultado-overlay " +
                    configuracion.clase;


                document.body.classList.add(
                    "modal-resultado-abierto"
                );


                /*
                    Pequeña espera para permitir
                    la animación CSS.
                */

                setTimeout(
                    function () {

                        modalResultado.classList.add(
                            "mostrar"
                        );

                    },
                    30
                );

            }


            /*=================================================
                CERRAR RESULTADO
            =================================================*/

            function cerrarResultadoModal() {

                modalResultado.classList.remove(
                    "mostrar"
                );


                document.body.classList.remove(
                    "modal-resultado-abierto"
                );


                /*
                    Quitamos los parámetros de la URL
                    para que el modal no vuelva a salir
                    al actualizar la página.
                */

                if (
                    window.history &&
                    window.history.replaceState
                ) {

                    const url =
                        window.location.pathname;

                    window.history.replaceState(
                        {},
                        document.title,
                        url
                    );

                }

            }


            /*=================================================
                EVENTOS DEL RESULTADO
            =================================================*/

            if (
                cerrarResultado
            ) {

                cerrarResultado.addEventListener(
                    "click",
                    function () {

                        cerrarResultadoModal();

                    }
                );

            }


            if (
                continuarResultado
            ) {

                continuarResultado.addEventListener(
                    "click",
                    function () {

                        cerrarResultadoModal();

                    }
                );

            }


            modalResultado.addEventListener(
                "click",
                function (
                    evento
                ) {

                    if (
                        evento.target ===
                        modalResultado
                    ) {

                        cerrarResultadoModal();

                    }

                }
            );


            document.addEventListener(
                "keydown",
                function (
                    evento
                ) {

                    if (
                        evento.key === "Escape" &&
                        modalResultado.classList.contains(
                            "mostrar"
                        )
                    ) {

                        cerrarResultadoModal();

                    }

                }
            );


            /*=================================================
                MOSTRAR SI PHP LO INDICÓ
            =================================================*/

            if (
                mostrar === "1"
            ) {

                abrirResultado();

            }

        }


    }

);