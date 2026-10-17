/*=========================================================
    SIDEBAR TUTORADO
    SIST V.4.0 - PIT V.4.0
=========================================================*/

document.addEventListener("DOMContentLoaded", () => {

    /*=========================================
        ELEMENTOS
    =========================================*/

    const btnMenu = document.getElementById("btnSidebarTutorado");
    const sidebar = document.getElementById("sidebarTutorado");
    const overlay = document.getElementById("sidebarOverlayTutorado");

    if (!btnMenu || !sidebar || !overlay) {
        return;
    }

    /*=========================================
        ABRIR SIDEBAR
    =========================================*/

    function abrirSidebar() {

        sidebar.classList.add("activo");
        overlay.classList.add("activo");

        document.body.style.overflow = "hidden";

    }

    /*=========================================
        CERRAR SIDEBAR
    =========================================*/

    function cerrarSidebar() {

        sidebar.classList.remove("activo");
        overlay.classList.remove("activo");

        document.body.style.overflow = "";

    }

    /*=========================================
        ALTERNAR SIDEBAR
    =========================================*/

    function alternarSidebar() {

        if (sidebar.classList.contains("activo")) {

            cerrarSidebar();

        } else {

            abrirSidebar();

        }

    }

    /*=========================================
        EVENTOS
    =========================================*/

    btnMenu.addEventListener("click", alternarSidebar);

    overlay.addEventListener("click", cerrarSidebar);

    /*=========================================
        CERRAR CON ESC
    =========================================*/

    document.addEventListener("keydown", (e) => {

        if (e.key === "Escape") {

            cerrarSidebar();

        }

    });

    /*=========================================
        CERRAR AL SELECCIONAR UNA OPCIÓN
        (SOLO EN MÓVIL)
    =========================================*/

    const enlaces = sidebar.querySelectorAll("a");

    enlaces.forEach((enlace) => {

        enlace.addEventListener("click", () => {

            if (window.innerWidth <= 768) {

                cerrarSidebar();

            }

        });

    });

    /*=========================================
        RESPONSIVE
    =========================================*/

    window.addEventListener("resize", () => {

        if (window.innerWidth > 768) {

            overlay.classList.remove("activo");

            document.body.style.overflow = "";

        }

    });

    /*=========================================
        CERRAR AL HACER CLIC FUERA
    =========================================*/

    document.addEventListener("click", (e) => {

        if (
            window.innerWidth <= 768 &&
            sidebar.classList.contains("activo") &&
            !sidebar.contains(e.target) &&
            !btnMenu.contains(e.target)
        ) {

            cerrarSidebar();

        }

    });

});