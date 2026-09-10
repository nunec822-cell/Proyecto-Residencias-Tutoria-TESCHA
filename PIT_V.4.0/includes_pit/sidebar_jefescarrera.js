/* =========================================================
   SIDEBAR JEFE DE CARRERA
   SIST V.4.0 - PIT V.4.0
========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    const btnSidebar = document.getElementById("btnSidebarJefe");

    const btnCerrar = document.getElementById("btnCerrarSidebarJefe");

    const sidebar = document.getElementById("sidebarJefesCarrera");

    const overlay = document.getElementById("sidebarOverlayJefe");


    /* =====================================================
       VERIFICAR ELEMENTOS
    ===================================================== */

    if (!btnSidebar || !sidebar || !overlay) {

        console.warn(
            "Sidebar Jefe de Carrera: no se encontraron todos los elementos."
        );

        return;
    }


    /* =====================================================
       ABRIR SIDEBAR
    ===================================================== */

    function abrirSidebar() {

        sidebar.classList.add("abierto");

        overlay.classList.add("visible");

        document.body.classList.add("sidebar-jefe-abierto");
    }


    /* =====================================================
       CERRAR SIDEBAR
    ===================================================== */

    function cerrarSidebar() {

        sidebar.classList.remove("abierto");

        overlay.classList.remove("visible");

        document.body.classList.remove("sidebar-jefe-abierto");
    }


    /* =====================================================
       BOTÓN TRES RAYITAS
    ===================================================== */

    btnSidebar.addEventListener("click", function (e) {

        e.preventDefault();

        abrirSidebar();

    });


    /* =====================================================
       BOTÓN X
    ===================================================== */

    if (btnCerrar) {

        btnCerrar.addEventListener("click", function (e) {

            e.preventDefault();

            cerrarSidebar();

        });

    }


    /* =====================================================
       OVERLAY
    ===================================================== */

    overlay.addEventListener("click", function () {

        cerrarSidebar();

    });


    /* =====================================================
       ESC
    ===================================================== */

    document.addEventListener("keydown", function (e) {

        if (e.key === "Escape") {

            cerrarSidebar();

        }

    });


    /* =====================================================
       CERRAR AL SELECCIONAR OPCIÓN
    ===================================================== */

    const enlaces = sidebar.querySelectorAll(
        ".sidebar-menu-jefe a"
    );

    enlaces.forEach(function (enlace) {

        enlace.addEventListener("click", function () {

            cerrarSidebar();

        });

    });

});
