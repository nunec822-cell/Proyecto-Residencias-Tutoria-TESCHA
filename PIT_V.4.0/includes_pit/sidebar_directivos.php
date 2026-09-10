<?php
/*=========================================================
    SIDEBAR DIRECTIVOS
    SIST V.4.0 - PIT V.4.0
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*=========================================================
    PÁGINA ACTUAL
=========================================================*/

$pagina_actual = $_SERVER['PHP_SELF'];


/*=========================================================
    RUTA BASE
=========================================================*/

$base_url = "/SIST V.4.0/PIT_V.4.0";

?>

<!--=========================================================
    BARRA SUPERIOR
=========================================================-->

<header class="topbar-directivos">

    <div class="topbar-left">

        <button
            type="button"
            id="btnSidebar"
            class="btn-menu"
            aria-label="Abrir menú"
        >

            <i class="fas fa-bars"></i>

        </button>


        <div class="topbar-logo">

            <div class="logo-icon">

                <i class="fas fa-building-columns"></i>

            </div>


            <div>

                <h2>
                    Dirección Institucional
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

    <div class="topbar-right">

        <div class="usuario-info">

            <i class="fas fa-user-tie"></i>

            <span>

                <?php

                echo isset($_SESSION["usuario"])
                    ? htmlspecialchars($_SESSION["usuario"])
                    : "Directivo";

                ?>

            </span>

        </div>


        <a
            href="<?= $base_url ?>/cerrar_sesion.php"
            class="btn-salir"
        >

            <i class="fas fa-right-from-bracket"></i>

            Cerrar sesión

        </a>

    </div>

</header>


<!--=========================================================
    OVERLAY
=========================================================-->

<div
    id="sidebarOverlay"
    class="sidebar-overlay"
>
</div>


<!--=========================================================
    SIDEBAR
=========================================================-->

<aside
    id="sidebarDirectivos"
    class="sidebar-directivos"
>


    <!--=====================================================
        CABECERA
    ======================================================-->

    <div class="sidebar-header">

        <div class="sidebar-avatar">

            <i class="fas fa-building-columns"></i>

        </div>


        <div>

            <h3>
                Directivos
            </h3>

            <span>
                Panel Principal
            </span>

        </div>

    </div>


    <!--=====================================================
        MENÚ
    ======================================================-->

    <nav class="sidebar-menu">

        <ul>


            <!--=================================================
                INICIO
            ==================================================-->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_DIRECTIVOS/inicio.php"
                    class="<?=
                        (
                            basename($pagina_actual) === 'inicio.php'
                            &&
                            strpos(
                                $pagina_actual,
                                '/MOD_DIRECTIVOS/canalizacionesgenerales/'
                            ) === false
                        )
                        ? 'activo'
                        : '';
                    ?>"
                >

                    <i class="fas fa-house"></i>

                    <span>
                        Inicio
                    </span>

                </a>

            </li>


            <!--=================================================
                CANALIZACIONES DE ALUMNOS ACADÉMICAS
            ==================================================-->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_DIRECTIVOS/canalizacionesgenerales/index.php"
                    class="<?=
                        (
                            strpos(
                                $pagina_actual,
                                '/MOD_DIRECTIVOS/canalizacionesgenerales/'
                            ) !== false
                        )
                        ? 'activo'
                        : '';
                    ?>"
                >

                    <i class="fas fa-user-graduate"></i>

                    <span>
                        Canalizaciones de alumnos académicas
                    </span>

                </a>

            </li>


        </ul>

    </nav>


    <!--=====================================================
        FOOTER SIDEBAR
    ======================================================-->

    <div class="sidebar-footer">

        <i class="fas fa-landmark"></i>

        <div>

            <strong>
                Dirección Institucional
            </strong>

            <small>
                TESCHA
            </small>

        </div>

    </div>

</aside>


<!--=========================================================
    CSS SIDEBAR
=========================================================-->

<link
    rel="stylesheet"
    href="<?= $base_url ?>/includes_pit/sidebar_directivos.css"
>


<!--=========================================================
    JAVASCRIPT SIDEBAR
=========================================================-->

<script
    src="<?= $base_url ?>/includes_pit/sidebar_directivos.js"
></script>



