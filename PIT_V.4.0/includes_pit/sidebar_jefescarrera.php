<?php
/*=========================================================
    SIDEBAR JEFE DE CARRERA
    SIST V.4.0 - PIT V.4.0
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*=========================================================
    PÁGINA ACTUAL
=========================================================*/

$pagina_actual = $_SERVER["PHP_SELF"];


/*=========================================================
    RUTA BASE
=========================================================*/

$base_url = "/SIST V.4.0/PIT_V.4.0";


/*=========================================================
    DATOS DEL USUARIO
=========================================================*/

$usuario = $_SESSION["usuario"] ?? "Jefe de Carrera";

?>

<!--=========================================================
    BARRA SUPERIOR
=========================================================-->

<header class="topbar-jefe">

    <div class="topbar-left-jefe">

        <!-- BOTÓN TRES RAYITAS -->

        <button
            type="button"
            id="btnSidebarJefe"
            class="btn-menu-jefe"
            aria-label="Abrir menú"
        >

            <i class="fas fa-bars"></i>

        </button>


        <!-- LOGO -->

        <div class="topbar-logo-jefe">

            <div class="topbar-logo-icon-jefe">

                <i class="fas fa-graduation-cap"></i>

            </div>


            <div>

                <h2>
                    Jefatura de Carrera
                </h2>

                <span>
                    Portal Institucional de Tutorías
                </span>

            </div>

        </div>

    </div>


    <!--=====================================================
        LADO DERECHO
    ======================================================-->

    <div class="topbar-right-jefe">

        <div class="usuario-info-jefe">

            <i class="fas fa-user-tie"></i>

            <span>

                <?php
                echo htmlspecialchars($usuario);
                ?>

            </span>

        </div>


        <a
            href="<?= $base_url ?>/cerrar_sesion.php"
            class="btn-salir-jefe"
        >

            <i class="fas fa-right-from-bracket"></i>

            <span>
                Cerrar sesión
            </span>

        </a>

    </div>

</header>


<!--=========================================================
    OVERLAY
=========================================================-->

<div
    id="sidebarOverlayJefe"
    class="sidebar-overlay-jefe"
>
</div>


<!--=========================================================
    SIDEBAR
=========================================================-->

<aside
    id="sidebarJefesCarrera"
    class="sidebar-jefescarrera"
>


    <!--=====================================================
        CABECERA
    ======================================================-->

    <div class="sidebar-header-jefe">

        <div class="identidad-jefe">

            <div class="sidebar-avatar-jefe">

                <i class="fas fa-graduation-cap"></i>

            </div>


            <div>

                <h3>
                    Jefatura
                </h3>

                <span>
                    Panel Principal
                </span>

            </div>

        </div>


        <!-- BOTÓN CERRAR -->

        <button
            type="button"
            id="btnCerrarSidebarJefe"
            class="btn-cerrar-sidebar-jefe"
            aria-label="Cerrar menú"
        >

            <i class="fas fa-xmark"></i>

        </button>

    </div>


    <!--=====================================================
        MENÚ
    ======================================================-->

    <nav class="sidebar-menu-jefe">

        <ul>


            <!--=================================================
                INICIO
            ==================================================-->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_JEFESCARRERA/iniciojefescarrera.php"

                    class="<?= (
                        basename($pagina_actual)
                        === "iniciojefescarrera.php"
                    )
                    ? "activo"
                    : "";
                    ?>"
                >

                    <i class="fas fa-house"></i>

                    <span>
                        Inicio
                    </span>

                </a>

            </li>


            <!--=================================================
    CANALIZACIONES DE ALUMNOS
=================================================-->

<li>

    <a
        href="<?= $base_url ?>/MOD_JEFESCARRERA/canalizacionesporcarrera/index.php"

        class="<?= (
            strpos(
                $pagina_actual,
                "/MOD_JEFESCARRERA/canalizacionesporcarrera/"
            ) !== false
        )
        ? "activo"
        : "";
        ?>"
    >

        <i class="fas fa-user-graduate"></i>

        <span>
            Canalizaciones de alumnos
        </span>

    </a>

</li>
        </ul>

    </nav>


    <!--=====================================================
        FOOTER
    ======================================================-->

    <div class="sidebar-footer-jefe">

        <div class="footer-linea-jefe"></div>


        <div class="rol-jefe">

            <div class="rol-icono-jefe">

                <i class="fas fa-user-tie"></i>

            </div>


            <div class="rol-texto-jefe">

                <strong>
                    Jefe de Carrera
                </strong>

                <small>
                    TESCHA
                </small>

            </div>

        </div>

    </div>

</aside>


<!--=========================================================
    CSS
=========================================================-->

<link
    rel="stylesheet"
    href="<?= $base_url ?>/includes_pit/sidebar_jefescarrera.css"
>


<!--=========================================================
    JAVASCRIPT
=========================================================-->

<script
    src="<?= $base_url ?>/includes_pit/sidebar_jefescarrera.js"
></script>

