<?php
/*=========================================================
    SIDEBAR ADMINISTRADOR
    SIST V.4.0 - PIT V.4.0
=========================================================*/

$pagina_actual = $_SERVER['PHP_SELF'];

$base_url = "/SIST V.4.0/PIT_V.4.0";
?>

<!--=========================================
    BARRA SUPERIOR
==========================================-->
<header class="topbar-admin">

    <!-- Lado izquierdo -->
    <div class="topbar-left">

        <button id="btnSidebar" class="btn-menu">
            <i class="fas fa-bars"></i>
        </button>

        <div class="topbar-logo">

            <i class="fas fa-user-shield"></i>

            <div>

                <h2>PIT V4.0</h2>

                <span>Programa Institucional de Tutorías</span>

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
                        : "Administrador";
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
<div id="sidebarOverlay" class="sidebar-overlay"></div>

<!--=========================================
    SIDEBAR
==========================================-->
<aside id="sidebarAdmin" class="sidebar-admin">

    <!-- Cabecera -->
    <div class="sidebar-header">

        <div class="sidebar-avatar">

            <i class="fas fa-user-shield"></i>

        </div>

        <div>

            <h3>Administrador</h3>

            <span>Panel Principal</span>

        </div>

    </div>

    <!-- Menú -->
    <nav class="sidebar-menu">

        <ul>

            <li>

                <a href="<?= $base_url ?>/MOD_ADMIN/inicioadmin.php"
                class="<?= (strpos($pagina_actual,'inicioadmin.php')!==false)?'activo':'';?>">

                    <i class="fas fa-house"></i>

                    <span>Inicio</span>

                </a>

            </li>

            <li>

                <a href="<?= $base_url ?>/MOD_ADMIN/registrar/index.php"
                class="<?= (strpos($pagina_actual,'registrar')!==false)?'activo':'';?>">

                    <i class="fas fa-user-plus"></i>

                    <span>Registrar usuarios</span>

                </a>

            </li>

            <li>

                <a href="<?= $base_url ?>/MOD_ADMIN/editar/index.php"
                class="<?= (strpos($pagina_actual,'editar')!==false)?'activo':'';?>">

                    <i class="fas fa-user-pen"></i>

                    <span>Editar usuarios</span>

                </a>

            </li>

            <li>

                <a href="<?= $base_url ?>/MOD_ADMIN/buscar/index.php"
                class="<?= (strpos($pagina_actual,'buscar')!==false)?'activo':'';?>">

                    <i class="fas fa-search"></i>

                    <span>Buscar usuarios</span>

                </a>

            </li>
            <li>

                <a href="<?= $base_url ?>/MOD_ADMIN/materias/index.php"
                class="<?= (strpos($pagina_actual,'materias')!==false)?'activo':'';?>">

                    <i class="fas fa-book-medical"></i>

                    <span>Registro de materias</span>

                </a>

            </li>
            <li>
                <a href="<?= $base_url ?>/MOD_ADMIN/asignar_grupos/index.php"
                class="<?= (strpos($pagina_actual,'asignar_grupos')!==false)?'activo':'';?>">

                    <i class="fas fa-users"></i>

                    <span>Asignar grupos</span>

                </a>
            </li>
            <li>

                <a href="<?= $base_url ?>/MOD_ADMIN/revision_anexo14/index.php"
                class="<?= (strpos($pagina_actual,'revision_anexo14')!==false)?'activo':'';?>">

                    <i class="fas fa-clipboard-check"></i>

                    <span>Revisión Anexo 14</span>

                </a>

            </li> 
            <li>

                <a href="<?= $base_url ?>/MOD_ADMIN/revision_actividades/index.php"
                class="<?= (strpos($pagina_actual,'revision_actividades') !== false) ? 'activo' : ''; ?>">

                     <i class="fas fa-clipboard-list"></i>

                    <span>Revisión de Actividades</span>

                </a>

            </li>
            <li>

                <a href="/SIST V.4.0/vista_contenidos/index.php"
                class="<?= (strpos($pagina_actual,'vista_contenidos')!==false)?'activo':'';?>">

                    <i class="fas fa-book-open"></i>

                    <span>Gestor de contenidos</span>

                </a>

            </li>
            

        </ul>

    </nav>

    <!-- Pie -->
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
<link rel="stylesheet"
href="<?= $base_url ?>/includes_pit/sidebar_admin.css">

<!--=========================================
    JAVASCRIPT
==========================================-->
<script src="<?= $base_url ?>/includes_pit/sidebar_admin.js"></script>