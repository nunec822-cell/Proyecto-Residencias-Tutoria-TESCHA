
<?php
/*=========================================================
    SIDEBAR DOCENTE
    SIST V.4.0 - PIT V.4.0
=========================================================*/

$pagina_actual = $_SERVER['PHP_SELF'];

$base_url = "/SIST V.4.0/PIT_V.4.0";


/*=========================================================
    ASEGURAR SESIÓN
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*=========================================================
    OBTENER ROLES DEL USUARIO
=========================================================*/

$roles_usuario = isset($_SESSION["roles"]) && is_array($_SESSION["roles"])
    ? $_SESSION["roles"]
    : [];


/*=========================================================
    DETECTAR ROLES
=========================================================*/

$es_tutor = in_array("TUTOR", $roles_usuario);

$es_coordinador = in_array("COORDINADOR", $roles_usuario);

?>

<!--=========================================
    BARRA SUPERIOR
==========================================-->

<header class="topbar-docente">

    <div class="topbar-left">

        <button
            id="btnSidebarDocente"
            class="btn-menu"
            type="button"
        >

            <i class="fas fa-bars"></i>

        </button>


        <div class="topbar-logo">

            <i class="fas fa-chalkboard-teacher"></i>

            <div>

                <h2>PIT V4.0</h2>

                <span>Panel Docente</span>

            </div>

        </div>

    </div>


    <div class="topbar-right">

        <div class="usuario-info">

            <i class="fas fa-user-circle"></i>

            <span>

                <?php

                echo isset($_SESSION["usuario"])
                    ? htmlspecialchars($_SESSION["usuario"])
                    : "Docente";

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
    id="sidebarOverlayDocente"
    class="sidebar-overlay"
></div>


<!--=========================================
    SIDEBAR
==========================================-->

<aside
    id="sidebarDocente"
    class="sidebar-docente"
>


    <!--=====================================
        ENCABEZADO
    ======================================-->

    <div class="sidebar-header">

        <div class="sidebar-avatar">

            <i class="fas fa-user-tie"></i>

        </div>


        <div>

            <h3>Docente</h3>

            <span>Panel Principal</span>

        </div>

    </div>



    <!--=====================================
        MENÚ
    ======================================-->

    <nav class="sidebar-menu">

        <ul>


            <!--=================================
                INICIO
            ==================================-->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_DOCENTE/iniciodocente.php"

                    class="<?= (
                        basename($pagina_actual) == 'iniciodocente.php'
                    ) ? 'activo' : ''; ?>"
                >

                    <i class="fas fa-house"></i>

                    <span>Inicio</span>

                </a>

            </li>



            <!--=================================
                LLENAR ANEXO 14
            ==================================-->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_DOCENTE/anexo14/index.php"

                    class="<?= (
                        strpos(
                            $pagina_actual,
                            '/MOD_DOCENTE/anexo14/'
                        ) !== false
                    ) ? 'activo' : ''; ?>"
                >

                    <i class="fas fa-traffic-light"></i>

                    <span>Llenar Anexo 14</span>

                </a>

            </li>



            <!--=================================
                CONSULTAR ANEXO 14
            ==================================-->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_DOCENTE/consultar_anexo14/index.php"

                    class="<?= (
                        strpos(
                            $pagina_actual,
                            '/MOD_DOCENTE/consultar_anexo14/'
                        ) !== false
                    ) ? 'activo' : ''; ?>"
                >

                    <i class="fas fa-file-csv"></i>

                    <span>Consultar Anexo 14</span>

                </a>

            </li>



            <!--=====================================
                PANEL TUTOR
            =====================================-->

            <?php if ($es_tutor): ?>

                <li class="titulo-menu">

                    <span>

                        <i class="fas fa-user-check"></i>

                        PANEL TUTOR

                    </span>

                </li>


                <!-- CANALIZACIONES -->

                <li>

                    <a
                        href="<?= $base_url ?>/MOD_TUTOR/canalizaciones/index.php"

                        class="<?= (
                            strpos(
                                $pagina_actual,
                                '/MOD_TUTOR/canalizaciones/'
                            ) !== false
                        ) ? 'activo' : ''; ?>"
                    >

                        <i class="fas fa-handshake"></i>

                        <span>Canalizaciones</span>

                    </a>

                </li>


                <!-- HISTORIAL CANALIZACIONES -->

                <li>

                    <a
                        href="<?= $base_url ?>/MOD_TUTOR/historial_canalizaciones/index.php"

                        class="<?= (
                            strpos(
                                $pagina_actual,
                                '/MOD_TUTOR/historial_canalizaciones/'
                            ) !== false
                        ) ? 'activo' : ''; ?>"
                    >

                        <i class="fas fa-clock-rotate-left"></i>

                        <span>Historial Canalizaciones</span>

                    </a>

                </li>


                <!-- ACTIVIDADES -->

                <li>

                    <a
                        href="<?= $base_url ?>/MOD_TUTOR/actividades/index.php"

                        class="<?= (
                            strpos(
                                $pagina_actual,
                                '/MOD_TUTOR/actividades/'
                            ) !== false
                        ) ? 'activo' : ''; ?>"
                    >

                        <i class="fas fa-calendar-check"></i>

                        <span>Actividades</span>

                    </a>

                </li>


                <!-- EVIDENCIAS -->

                <li>

                    <a
                        href="<?= $base_url ?>/MOD_TUTOR/evidencias/index.php"

                        class="<?= (
                            strpos(
                                $pagina_actual,
                                '/MOD_TUTOR/evidencias/'
                            ) !== false
                        ) ? 'activo' : ''; ?>"
                    >

                        <i class="fas fa-folder-open"></i>

                        <span>Evidencias</span>

                    </a>

                </li>

            <?php endif; ?>



            <!--=====================================
                PANEL COORDINADOR
            =====================================-->

            <?php if ($es_coordinador): ?>

                <li class="titulo-menu">

                    <span>

                        <i class="fas fa-chart-line"></i>

                        PANEL COORDINADOR

                    </span>

                </li>


                <!--=================================
                    SEGUIMIENTO CANALIZACIONES
                ==================================-->

                <li>

                    <a
                        href="<?= $base_url ?>/MOD_CORDINADOR/index.php"

                        class="<?= (
                            strpos(
                                $pagina_actual,
                                '/MOD_CORDINADOR/'
                            ) !== false
                        ) ? 'activo' : ''; ?>"
                    >

                        <i class="fas fa-chart-pie"></i>

                        <span>
                            Seguimiento Canalizaciones
                        </span>

                    </a>

                </li>

            <?php endif; ?>


        </ul>

    </nav>



    <!--=====================================
        FOOTER
    ======================================-->

    <div class="sidebar-footer">

        <i class="fas fa-circle-check"></i>

        <div>

            <strong>Sistema PIT</strong>

            <small>Versión 4.0</small>

        </div>

    </div>

</aside>



<!--=========================================
    CSS
==========================================-->

<link
    rel="stylesheet"
    href="<?= $base_url ?>/includes_pit/sidebar_docente.css"
>



<!--=========================================
    JS
==========================================-->

<script
    src="<?= $base_url ?>/includes_pit/sidebar_docente.js"
></script>



