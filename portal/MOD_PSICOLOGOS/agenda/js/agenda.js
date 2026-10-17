
/*=========================================================
    AGENDA DEL PSICÓLOGO
    SIST V.4.0 - PIT V.4.0
=========================================================*/

document.addEventListener("DOMContentLoaded", function () {


    /*=====================================================
        FORMULARIO REGISTRAR
    =====================================================*/

    const formularioRegistrar =
        document.getElementById("formDisponibilidad");


    /*=====================================================
        FORMULARIO EDITAR
    =====================================================*/

    const formularioEditar =
        document.getElementById("formEditarDisponibilidad");


    /*=====================================================
        FUNCIÓN VALIDAR HORARIO
    =====================================================*/

    function validarHorario(formulario) {

        const fecha =
            formulario.querySelector("#fecha");

        const horaInicio =
            formulario.querySelector("#hora_inicio");

        const horaFin =
            formulario.querySelector("#hora_fin");


        if (
            !fecha ||
            !horaInicio ||
            !horaFin
        ) {

            return true;

        }


        /*=============================================
            VALIDAR FECHA
        =============================================*/

        if (!fecha.value) {

            mostrarMensaje(
                "Selecciona una fecha.",
                "error"
            );

            fecha.focus();

            return false;

        }


        /*=============================================
            VALIDAR HORA INICIO
        =============================================*/

        if (!horaInicio.value) {

            mostrarMensaje(
                "Selecciona la hora de inicio.",
                "error"
            );

            horaInicio.focus();

            return false;

        }


        /*=============================================
            VALIDAR HORA FIN
        =============================================*/

        if (!horaFin.value) {

            mostrarMensaje(
                "Selecciona la hora de finalización.",
                "error"
            );

            horaFin.focus();

            return false;

        }


        /*=============================================
            COMPARAR HORAS
        =============================================*/

        if (horaInicio.value >= horaFin.value) {

            mostrarMensaje(
                "La hora de finalización debe ser posterior a la hora de inicio.",
                "error"
            );

            horaFin.focus();

            return false;

        }


        return true;

    }


    /*=====================================================
        VALIDAR REGISTRO
    =====================================================*/

    if (formularioRegistrar) {

        formularioRegistrar.addEventListener(
            "submit",
            function (evento) {

                if (!validarHorario(this)) {

                    evento.preventDefault();

                    return;

                }

            }
        );

    }


    /*=====================================================
        VALIDAR EDICIÓN
    =====================================================*/

    if (formularioEditar) {

        formularioEditar.addEventListener(
            "submit",
            function (evento) {

                if (!validarHorario(this)) {

                    evento.preventDefault();

                    return;

                }

            }
        );

    }


    /*=====================================================
        ACTUALIZAR MINUTOS DE HORA
    =====================================================*/

    const horaInicio =
        document.getElementById("hora_inicio");

    const horaFin =
        document.getElementById("hora_fin");


    if (horaInicio && horaFin) {

        horaInicio.addEventListener(
            "change",
            function () {

                if (this.value) {

                    horaFin.min = this.value;

                }

            }
        );


        horaFin.addEventListener(
            "change",
            function () {

                if (
                    horaInicio.value &&
                    this.value &&
                    this.value <= horaInicio.value
                ) {

                    mostrarMensaje(
                        "La hora de finalización debe ser posterior a la hora de inicio.",
                        "error"
                    );

                }

            }
        );

    }


    /*=====================================================
        MENSAJES DESDE PHP
    =====================================================*/

    const parametros =
        new URLSearchParams(
            window.location.search
        );


    const error =
        parametros.get("error");

    const success =
        parametros.get("success");


    /*=====================================================
        MENSAJES DE ÉXITO
    =====================================================*/

    if (success === "registrado") {

        mostrarMensaje(
            "La disponibilidad se registró correctamente.",
            "success"
        );

    }


    if (success === "actualizado") {

        mostrarMensaje(
            "La disponibilidad se actualizó correctamente.",
            "success"
        );

    }


    if (success === "eliminado") {

        mostrarMensaje(
            "La disponibilidad se eliminó correctamente.",
            "success"
        );

    }


    if (success === "estado") {

        mostrarMensaje(
            "El estado de la disponibilidad se actualizó correctamente.",
            "success"
        );

    }


    /*=====================================================
        MENSAJES DE ERROR
    =====================================================*/

    if (error === "campos") {

        mostrarMensaje(
            "Debes completar todos los campos.",
            "error"
        );

    }


    if (error === "fecha") {

        mostrarMensaje(
            "La fecha seleccionada no es válida.",
            "error"
        );

    }


    if (error === "fecha_pasada") {

        mostrarMensaje(
            "No puedes registrar una fecha anterior a la actual.",
            "error"
        );

    }


    if (error === "horario") {

        mostrarMensaje(
            "La hora de finalización debe ser posterior a la hora de inicio.",
            "error"
        );

    }


    if (error === "hora") {

        mostrarMensaje(
            "El formato de la hora no es válido.",
            "error"
        );

    }


    if (error === "superpuesto") {

        mostrarMensaje(
            "Ya tienes una disponibilidad que se cruza con ese horario.",
            "error"
        );

    }


    if (error === "id") {

        mostrarMensaje(
            "El identificador de la disponibilidad no es válido.",
            "error"
        );

    }


    if (error === "no_encontrado") {

        mostrarMensaje(
            "La disponibilidad no existe o no pertenece a tu cuenta.",
            "error"
        );

    }


    if (error === "tiene_citas") {

        mostrarMensaje(
            "Esta disponibilidad no puede eliminarse porque tiene citas asociadas.",
            "error"
        );

    }


    /*=====================================================
        FUNCIÓN PARA MOSTRAR MENSAJES
    =====================================================*/

    function mostrarMensaje(texto, tipo) {


        /*=============================================
            ELIMINAR MENSAJE ANTERIOR
        =============================================*/

        const anterior =
            document.querySelector(
                ".agenda-mensaje"
            );


        if (anterior) {

            anterior.remove();

        }


        /*=============================================
            CREAR MENSAJE
        =============================================*/

        const mensaje =
            document.createElement("div");


        mensaje.className =
            "agenda-mensaje " + tipo;


        mensaje.innerHTML = `

            <div class="mensaje-icono">

                ${
                    tipo === "success"
                        ? "✓"
                        : "!"
                }

            </div>

            <div class="mensaje-texto">

                ${texto}

            </div>

            <button
                type="button"
                class="mensaje-cerrar"
                aria-label="Cerrar mensaje"
            >

                ×

            </button>

        `;


        /*=============================================
            INSERTAR MENSAJE
        =============================================*/

        document.body.appendChild(mensaje);


        /*=============================================
            ANIMACIÓN
        =============================================*/

        setTimeout(function () {

            mensaje.classList.add(
                "mostrar"
            );

        }, 50);


        /*=============================================
            CERRAR
        =============================================*/

        const botonCerrar =
            mensaje.querySelector(
                ".mensaje-cerrar"
            );


        botonCerrar.addEventListener(
            "click",
            function () {

                cerrarMensaje(mensaje);

            }
        );


        /*=============================================
            CIERRE AUTOMÁTICO
        =============================================*/

        setTimeout(function () {

            cerrarMensaje(mensaje);

        }, 5000);

    }


    /*=====================================================
        CERRAR MENSAJE
    =====================================================*/

    function cerrarMensaje(mensaje) {

        if (!mensaje) {

            return;

        }


        mensaje.classList.remove(
            "mostrar"
        );


        setTimeout(function () {

            if (mensaje.parentNode) {

                mensaje.remove();

            }

        }, 300);

    }


    /*=====================================================
        CONFIRMACIÓN DE ELIMINACIÓN
    =====================================================*/

    const botonesEliminar =
        document.querySelectorAll(
            ".btn-eliminar"
        );


    botonesEliminar.forEach(
        function (boton) {

            boton.addEventListener(
                "click",
                function (evento) {

                    const confirmar =
                        confirm(
                            "¿Estás seguro de eliminar esta disponibilidad?\n\nEsta acción no se puede deshacer."
                        );


                    if (!confirmar) {

                        evento.preventDefault();

                    }

                }
            );

        }
    );


});
/*=========================================================
    MODAL DE MENSAJES
=========================================================*/

document.addEventListener("DOMContentLoaded", function () {

    const modal =
        document.getElementById("modalMensaje");

    const contenido =
        document.getElementById(
            "modalMensajeContenido"
        );


    /*=====================================================
        SI EXISTE EL MODAL
    =====================================================*/

    if (!modal || !contenido) {

        return;

    }


    /*=====================================================
        MOSTRAR MODAL
    =====================================================*/

    setTimeout(function () {

        modal.classList.add("activo");

    }, 50);


    /*=====================================================
        CERRAR AUTOMÁTICAMENTE
    =====================================================*/

    setTimeout(function () {

        cerrarModalMensaje();

    }, 4000);


});


/*=========================================================
    CERRAR MODAL
=========================================================*/

function cerrarModalMensaje() {

    const modal =
        document.getElementById("modalMensaje");

    const contenido =
        document.getElementById(
            "modalMensajeContenido"
        );


    if (!modal || !contenido) {

        return;

    }


    contenido.classList.add("cerrando");


    setTimeout(function () {

        modal.remove();

        /*=============================================
            LIMPIAR URL
        ==============================================*/

        const url =
            new URL(
                window.location.href
            );


        url.searchParams.delete("success");

        url.searchParams.delete("error");


        window.history.replaceState(
            {},
            document.title,
            url.pathname +
            url.search
        );


    }, 250);

}


/*=========================================================
    CERRAR AL HACER CLIC FUERA
=========================================================*/

document.addEventListener(
    "click",
    function (evento) {

        const modal =
            document.getElementById(
                "modalMensaje"
            );


        if (
            modal &&
            evento.target === modal
        ) {

            cerrarModalMensaje();

        }

    }
);


/*=========================================================
    CERRAR CON ESC
=========================================================*/

document.addEventListener(
    "keydown",
    function (evento) {

        if (evento.key === "Escape") {

            cerrarModalMensaje();

        }

    }
);

