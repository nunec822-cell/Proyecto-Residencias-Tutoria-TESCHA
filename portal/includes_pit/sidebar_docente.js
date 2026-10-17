/*=========================================================
    SIDEBAR DOCENTE
    SIST V.4.0 - PIT V.4.0
=========================================================*/

document.addEventListener("DOMContentLoaded", function () {

    const btnMenu = document.getElementById("btnSidebarDocente");
    const sidebar = document.getElementById("sidebarDocente");
    const overlay = document.getElementById("sidebarOverlayDocente");

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
        TOGGLE
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

    document.addEventListener("keydown", function (e) {

        if (e.key === "Escape") {

            cerrarSidebar();

        }

    });

    /*=========================================
        RESPONSIVE
    =========================================*/

    window.addEventListener("resize", function () {

        if (window.innerWidth > 768) {

            overlay.classList.remove("activo");

            document.body.style.overflow = "";

        }

    });

});