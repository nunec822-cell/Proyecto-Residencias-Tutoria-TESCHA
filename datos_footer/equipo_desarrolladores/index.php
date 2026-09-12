<?php
/*=========================================================
    EQUIPO DE DESARROLLADORES
    SIST V.4.0 - PIT V.4.0
    TESCHA
=========================================================*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Equipo de Desarrolladores | PIT V.4.0</title>

    <meta name="description"
          content="Equipo responsable del desarrollo y asesoría del Portal Institucional de Tutorías PIT V.4.0 del Tecnológico de Estudios Superiores de Chalco.">

    <link rel="stylesheet" href="equipo.css">
</head>

<body>

    <!-- =====================================================
         FONDO DECORATIVO
    ====================================================== -->

    <div class="background-effects">

        <span class="glow glow-1"></span>
        <span class="glow glow-2"></span>
        <span class="glow glow-3"></span>

        <div class="grid-background"></div>

    </div>


    <!-- =====================================================
         NAVEGACIÓN SUPERIOR
    ====================================================== -->

    <header class="top-header">

        <div class="brand">

            <div class="brand-icon">
                <span>&lt;/&gt;</span>
            </div>

            <div class="brand-text">
                <span class="brand-title">PIT V.4.0</span>
                <span class="brand-subtitle">
                    Portal Institucional de Tutorías
                </span>
            </div>

        </div>

        <div class="header-badge">
            <span class="status-dot"></span>
            TESCHA
        </div>

    </header>


    <!-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== -->

    <main class="main-container">


        <!-- =================================================
             HERO
        ================================================== -->

        <section class="hero-section">

            <div class="hero-tag">
                <span class="tag-line"></span>
                EQUIPO DE DESARROLLO
                <span class="tag-line"></span>
            </div>

            <h1>
                El equipo
                <span>detrás del sistema</span>
            </h1>

            <p class="hero-description">
                Personas que aportaron conocimiento, experiencia y dedicación
                para hacer posible el desarrollo del
                <strong>PIT V.4.0</strong>.
            </p>

            <div class="scroll-indicator">

                <span class="scroll-text">
                    CONOCE AL EQUIPO
                </span>

                <span class="scroll-arrow">
                    ↓
                </span>

            </div>

        </section>


        <!-- =================================================
             ASESOR
        ================================================== -->

        <section class="advisor-section">

            <div class="section-label">
                <span>01</span>
                ASESORÍA Y ACOMPAÑAMIENTO
            </div>


            <article class="person-card advisor-card">

                <div class="card-glow"></div>

                <div class="person-number">
                    01
                </div>

                <div class="person-avatar advisor-avatar">

                    <div class="avatar-ring"></div>

                    <div class="avatar-content">
                        LC
                    </div>

                    <div class="avatar-status"></div>

                </div>


                <div class="person-content">

                    <span class="person-role">
                        ASESOR INTERNO
                    </span>

                    <h2>
                        Leonardo
                        <strong>Cortés Vergara</strong>
                    </h2>

                    <p class="institution">
                        Tecnológico de Estudios Superiores de Chalco
                    </p>

                    <div class="person-description">

                        <span class="quote-mark">“</span>

                        <p>
                            Asesor interno encargado de brindar
                            acompañamiento y orientación durante el
                            desarrollo del sistema.
                        </p>

                    </div>

                </div>


                <div class="card-decoration">

                    <span></span>
                    <span></span>
                    <span></span>

                </div>

            </article>

        </section>


        <!-- =================================================
             DESARROLLADORES
        ================================================== -->

        <section class="developers-section">

            <div class="section-label">
                <span>02</span>
                DESARROLLO
            </div>


            <div class="developers-grid">


                <!-- =========================================
                     ALEXIS
                ========================================== -->

                <article class="person-card developer-card">

                    <div class="card-glow"></div>

                    <div class="person-number">
                        02
                    </div>

                    <div class="person-avatar developer-avatar avatar-alexis">

                        <div class="avatar-ring"></div>

                        <div class="avatar-content">
                            AC
                        </div>

                        <div class="avatar-status"></div>

                    </div>


                    <div class="person-content">

                        <span class="person-role">
                            DESARROLLADOR
                        </span>

                        <h2>
                            Alexis
                            <strong>Cercle Carrasco</strong>
                        </h2>

                        <p class="developer-text">
                            Desarrollo, implementación y construcción
                            de soluciones para el sistema PIT V.4.0.
                        </p>

                        <div class="skills">

                            <span>PHP</span>
                            <span>JavaScript</span>
                            <span>MariaDB</span>

                        </div>

                    </div>


                    <div class="code-decoration">
                        <span>&lt;</span>
                        <span>/</span>
                        <span>&gt;</span>
                    </div>

                </article>


                <!-- =========================================
                     EDNA
                ========================================== -->

                <article class="person-card developer-card">

                    <div class="card-glow"></div>

                    <div class="person-number">
                        03
                    </div>

                    <div class="person-avatar developer-avatar avatar-edna">

                        <div class="avatar-ring"></div>

                        <div class="avatar-content">
                            EM
                        </div>

                        <div class="avatar-status"></div>

                    </div>


                    <div class="person-content">

                        <span class="person-role">
                            DESARROLLADORA
                        </span>

                        <h2>
                            Edna Montserrat
                            <strong>Martínez Reyes</strong>
                        </h2>

                        <p class="developer-text">
                            Desarrollo y colaboración en la construcción
                            de funcionalidades y experiencias para el
                            sistema PIT V.4.0.
                        </p>

                        <div class="skills">

                            <span>PHP</span>
                            <span>JavaScript</span>
                            <span>CSS</span>

                        </div>

                    </div>


                    <div class="code-decoration">
                        <span>{</span>
                        <span>/</span>
                        <span>}</span>
                    </div>

                </article>


            </div>

        </section>


        <!-- =================================================
             LÍNEA DE CONEXIÓN
        ================================================== -->

        <section class="connection-section">

            <div class="connection-line"></div>

            <div class="connection-center">

                <div class="connection-icon">
                    <span>&lt;/&gt;</span>
                </div>

            </div>

            <p>
                Asesoría <span>•</span> Desarrollo <span>•</span>
                Innovación
            </p>

        </section>


        <!-- =================================================
             PROYECTO
        ================================================== -->

        <section class="project-section">

            <div class="project-content">

                <span class="project-label">
                    PROYECTO
                </span>

                <h2>
                    PIT <span>V.4.0</span>
                </h2>

                <p>
                    Portal Institucional de Tutorías
                </p>

                <div class="project-institution">
                    Tecnológico de Estudios Superiores de Chalco
                </div>

            </div>


            <div class="project-orbit">

                <div class="orbit orbit-1"></div>
                <div class="orbit orbit-2"></div>

                <div class="orbit-core">
                    PIT
                </div>

            </div>

        </section>


    </main>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer class="page-footer">

        <div class="footer-line"></div>

        <p>
            © <?php echo date('Y'); ?>
            Tecnológico de Estudios Superiores de Chalco
        </p>

        <span>
            PIT V.4.0
        </span>

    </footer>


    <script src="equipo.js"></script>

</body>
</html>