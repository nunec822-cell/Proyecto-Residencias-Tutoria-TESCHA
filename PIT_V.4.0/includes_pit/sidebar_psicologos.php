<?php
/*=========================================================
    SIDEBAR PSICÓLOGOS
    SIST V.4.0 - PIT V.4.0
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pagina_actual = $_SERVER['PHP_SELF'];

$base_url = "/SIST V.4.0/PIT_V.4.0";
?>

<!--=========================================
    BARRA SUPERIOR
==========================================-->

<header class="topbar-psicologos">

    <div class="topbar-left">

        <button
            id="btnSidebar"
            class="btn-menu"
        >

            <i class="fas fa-bars"></i>

        </button>

        <div class="topbar-logo">

            <div class="logo-icon">

                <i class="fas fa-brain"></i>

            </div>

            <div>

                <h2>Psicología TESCHA</h2>

                <span>Departamento de Psicología</span>

            </div>

        </div>

    </div>


    <!--=====================================
        LADO DERECHO
    ======================================-->

    <div class="topbar-right">

        <div class="usuario-info">

            <i class="fas fa-user-doctor"></i>

            <span>

                <?php

                echo isset($_SESSION["usuario"])
                    ? $_SESSION["usuario"]
                    : "Psicólogo";

                ?>

            </span>

        </div>


        <a
            class="btn-salir"
            href="<?= $base_url ?>/cerrar_sesion.php"
        >

            <i class="fas fa-right-from-bracket"></i>

            Cerrar sesión

        </a>

    </div>

</header>


<!--=========================================
    OVERLAY
==========================================-->

<div
    id="sidebarOverlay"
    class="sidebar-overlay"
>
</div>


<!--=========================================
    SIDEBAR
==========================================-->

<aside
    id="sidebarPsicologos"
    class="sidebar-psicologos"
>


    <!--=====================================
        CABECERA
    ======================================-->

    <div class="sidebar-header">

        <div class="sidebar-avatar">

            <i class="fas fa-brain"></i>

        </div>

        <div>

            <h3>Psicología</h3>

            <span>Panel Principal</span>

        </div>

    </div>


    <!--=====================================
        MENÚ
    ======================================-->

    <nav class="sidebar-menu">

        <ul>


            <!--=====================================
                INICIO
            ======================================-->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_PSICOLOGOS/inicio.php"

                    class="<?= (
                        basename($pagina_actual) == 'inicio.php'
                        &&
                        strpos(
                            $pagina_actual,
                            '/MOD_PSICOLOGOS/agenda/'
                        ) === false
                        &&
                        strpos(
                            $pagina_actual,
                            '/MOD_PSICOLOGOS/citas/'
                        ) === false
                    )
                    ? 'activo'
                    : '';
                    ?>"
                >

                    <i class="fas fa-house"></i>

                    <span>Inicio</span>

                </a>

            </li>


            <!--=====================================
                CREAR ESPACIOS DISPONIBLES
            ======================================-->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_PSICOLOGOS/agenda/index.php"

                    class="<?= (
                        strpos(
                            $pagina_actual,
                            '/MOD_PSICOLOGOS/agenda/'
                        ) !== false
                    )
                    ? 'activo'
                    : '';
                    ?>"
                >

                    <i class="fas fa-calendar-plus"></i>

                    <span>Crear espacios disponibles</span>

                </a>

            </li>


            <!--=====================================
                GESTIONAR CITAS
            ======================================-->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_PSICOLOGOS/citas/index.php"

                    class="<?= (
                        strpos(
                            $pagina_actual,
                            '/MOD_PSICOLOGOS/citas/'
                        ) !== false
                    )
                    ? 'activo'
                    : '';
                    ?>"
                >

                    <i class="fas fa-calendar-check"></i>

                    <span>Gestionar citas</span>

                </a>

            </li>


        </ul>

    </nav>


    <!--=====================================
        FOOTER
    ======================================-->

    <div class="sidebar-footer">

        <i class="fas fa-heart-pulse"></i>

        <div>

            <strong>Departamento</strong>

            <small>Psicología TESCHA</small>

        </div>

    </div>


</aside>


<!--=========================================
    CSS
==========================================-->

<link
    rel="stylesheet"
    href="<?= $base_url ?>/includes_pit/sidebar_psicologos.css"
>


<!--=========================================
    JS
==========================================-->

<script
    src="<?= $base_url ?>/includes_pit/sidebar_psicologos.js"
>
</script>