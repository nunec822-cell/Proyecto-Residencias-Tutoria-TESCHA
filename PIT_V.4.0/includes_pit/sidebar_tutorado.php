<?php
/*=========================================================
    SIDEBAR TUTORADO
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
<header class="topbar-tutorado">

    <!-- Lado izquierdo -->
    <div class="topbar-left">

        <button id="btnSidebarTutorado" class="btn-menu">

            <i class="fas fa-bars"></i>

        </button>

        <div class="topbar-logo">

            <div class="logo-icon">

                <i class="fas fa-user-graduate"></i>

            </div>

            <div>

                <h2>PIT V4.0</h2>

                <span>Panel del Tutorado</span>

            </div>

        </div>

    </div>

    <!-- Lado derecho -->
    <div class="topbar-right">

        <div class="usuario-info">

            <i class="fas fa-user-circle"></i>

            <span>

                <?php
                echo isset($_SESSION["usuario"])
                    ? $_SESSION["usuario"]
                    : "Tutorado";
                ?>

            </span>

        </div>

        <a class="btn-salir"
           href="<?= $base_url ?>/cerrar_sesion.php">

            <i class="fas fa-right-from-bracket"></i>

            Cerrar sesión

        </a>

    </div>

</header>

<!--=========================================
    FONDO OSCURO
==========================================-->

<div id="sidebarOverlayTutorado"
     class="sidebar-overlay">
</div>

<!--=========================================
    SIDEBAR
==========================================-->

<aside
id="sidebarTutorado"
class="sidebar-tutorado">

    <!--=====================================
        CABECERA
    ======================================-->

    <div class="sidebar-header">

        <div class="sidebar-avatar">

            <i class="fas fa-user-graduate"></i>

        </div>

        <div>

            <h3>Tutorado</h3>

            <span>Panel Principal</span>

        </div>

    </div>

    <!--=====================================
        MENÚ
    ======================================-->

    <nav class="sidebar-menu">

        <ul>

            <!-- INICIO -->

            <li>

                <a
                href="<?= $base_url ?>/MOD_TUTORADO/Inicio_tutorado.php"

                class="<?= (basename($pagina_actual) == 'Inicio_tutorado.php') ? 'activo' : ''; ?>">

                    <i class="fas fa-house"></i>

                    <span>Inicio</span>

                </a>

            </li>
            <li>

                <a
                    href="<?= $base_url ?>/MOD_TUTORADO/actividades/index.php"
                    class="<?= (strpos($pagina_actual,'/MOD_TUTORADO/actividades/') !== false) ? 'activo' : ''; ?>"
                >

                    <i class="fas fa-clipboard-list"></i>

                    <span>Actividades</span>

                </a>

            </li>
            <!-- CITA DE PSICOLOGÍA -->

            <li>

                <a
                    href="<?= $base_url ?>/MOD_TUTORADO/cita_psicologia_alumno/index.php"

                    class="<?= (
                        strpos(
                            $pagina_actual,
                            '/MOD_TUTORADO/cita_psicologia_alumno/'
                        ) !== false
                    ) ? 'activo' : ''; ?>"
                >

                    <i class="fas fa-calendar-check"></i>

                    <span>Citas Psicología</span>

                </a>

            </li>

        </ul>

    </nav>

    <!--=====================================
        FOOTER
    ======================================-->

    <div class="sidebar-footer">

        <i class="fas fa-circle-check"></i>

        <div>

            <strong>Sistema PIT</strong>

            <small>Tutorado</small>

        </div>

    </div>

</aside>

<!--=========================================
    CSS
==========================================-->

<link rel="stylesheet"
href="<?= $base_url ?>/includes_pit/sidebar_tutorado.css">

<!--=========================================
    JAVASCRIPT
==========================================-->

<script
src="<?= $base_url ?>/includes_pit/sidebar_tutorado.js"></script>