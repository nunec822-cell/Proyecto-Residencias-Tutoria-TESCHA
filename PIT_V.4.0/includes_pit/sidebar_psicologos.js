/*=========================================================
    SIDEBAR PSICÓLOGOS
    SIST V.4.0 - PIT V.4.0
=========================================================*/

document.addEventListener("DOMContentLoaded", () => {

    /*=========================================
        ELEMENTOS
    =========================================*/

    const btnMenu = document.getElementById("btnSidebar");
    const sidebar = document.getElementById("sidebarPsicologos");
    const overlay = document.getElementById("sidebarOverlay");

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
        ALTERNAR
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
        CERRAR AL DAR CLIC EN UNA OPCIÓN
        (SOLO MÓVIL)
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
        CERRAR SI SE HACE CLIC FUERA
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