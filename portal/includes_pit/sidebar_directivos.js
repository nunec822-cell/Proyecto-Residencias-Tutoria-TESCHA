/*=========================================================
    SIDEBAR DIRECTIVOS
    SIST V.4.0 - PIT V.4.0
=========================================================*/

(function () {

    "use strict";

    function iniciarSidebar() {

        const boton = document.getElementById("btnSidebar");
        const sidebar = document.getElementById("sidebarDirectivos");
        const overlay = document.getElementById("sidebarOverlay");

        /*=====================================================
            COMPROBAR ELEMENTOS
        =====================================================*/

        if (!boton) {
            console.error("ERROR: No existe #btnSidebar");
            return;
        }

        if (!sidebar) {
            console.error("ERROR: No existe #sidebarDirectivos");
            return;
        }

        if (!overlay) {
            console.error("ERROR: No existe #sidebarOverlay");
            return;
        }


        console.log("Sidebar Directivos cargado correctamente");


        /*=====================================================
            ABRIR SIDEBAR
        =====================================================*/

        boton.addEventListener("click", function (evento) {

            evento.preventDefault();
            evento.stopPropagation();

            sidebar.classList.add("activo");
            overlay.classList.add("activo");

            document.body.classList.add("sidebar-abierto");

        });


        /*=====================================================
            CERRAR SIDEBAR
        =====================================================*/

        function cerrarSidebar() {

            sidebar.classList.remove("activo");
            overlay.classList.remove("activo");

            document.body.classList.remove("sidebar-abierto");

        }


        /*=====================================================
            CERRAR CON OVERLAY
        =====================================================*/

        overlay.addEventListener("click", function () {

            cerrarSidebar();

        });


        /*=====================================================
            CERRAR CON ESC
        =====================================================*/

        document.addEventListener("keydown", function (evento) {

            if (evento.key === "Escape") {

                cerrarSidebar();

            }

        });


        /*=====================================================
            CERRAR AL SELECCIONAR UNA OPCIÓN
        =====================================================*/

        const enlaces = sidebar.querySelectorAll("a");

        enlaces.forEach(function (enlace) {

            enlace.addEventListener("click", function () {

                cerrarSidebar();

            });

        });

    }


    /*=========================================================
        ESPERAR A QUE CARGUE EL DOM
    =========================================================*/

    if (document.readyState === "loading") {

        document.addEventListener(
            "DOMContentLoaded",
            iniciarSidebar
        );

    } else {

        iniciarSidebar();

    }

})();

