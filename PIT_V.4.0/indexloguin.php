
<?php
include("includes_pit/header.php"); ?>
<div class="separador"></div>

<?php include("../includes/sidebar-sist.php"); ?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portal Institucional de Tutorías | PIT</title>

    <link rel="stylesheet" href="css_pit/estilo.css">

</head>

<body>

<div class="fondo-login">

    <main class="contenedor-login">

        <!--====================================================
                        ENCABEZADO
        =====================================================-->

        <section class="encabezado-pit">

            <div class="logo logo-izquierdo">

                <!-- Logo Tecnológico -->
                <img src="img/urones.jpeg" alt="Logo TecNM">

            </div>

            <div class="titulo-principal">

                <h1>PORTAL INSTITUCIONAL DE TUTORÍAS</h1>

                <h2>Sistema Integral PIT</h2>

                <p>Versión 4.0</p>

            </div>

            <div class="logo logo-derecho">

                <!-- Logo PIT -->
                <img src="img/urones2.jpeg" alt="Logo PIT">

            </div>

        </section>

        <!--====================================================
                        CONTENIDO
        =====================================================-->

        <section class="panel-principal">

            <!--=============================================
                        LOGIN
            ==============================================-->

            <div class="card-login">

                <h2>

                    🔐 Iniciar Sesión

                </h2>

                <p class="bienvenida">

                    Bienvenido(a).

                    <br><br>

                    Ingrese sus credenciales institucionales para acceder al Portal Institucional de Tutorías.

                </p>

                <form id="formLogin" method="POST" action="validar_login.php" novalidate>

                    <!--========================
                            USUARIO
                    =========================-->

                    <div class="grupo">

                        <label>

                            Usuario

                        </label>

                        <div class="input-icono">

                            <span>

                                👤

                            </span>

                            <input

                                type="text"

                                id="usuario"

                                name="usuario"

                                maxlength="80"

                                autocomplete="off"

                                placeholder="APELLIDO PATERNO APELLIDO MATERNO NOMBRE(S)"

                                required>

                        </div>

                    </div>

                    <!--========================
                        PASSWORD
                    =========================-->

                    <div class="grupo">

                        <label>

                            Contraseña

                        </label>

                        <div class="input-icono">

                            <span>

                                🔒

                            </span>

                            <input

                                type="password"

                                id="password"

                                name="password"

                                placeholder="Ingrese su contraseña"

                                required>

                            <button

                                type="button"

                                id="mostrarPassword"

                                class="ver-password">

                                👁

                            </button>

                        </div>

                    </div>

                    <!--========================
                            MENSAJES
                    =========================-->

                    <div

                        id="mensajeLogin"

                        class="mensaje-login">

                    </div>

                    <!--========================
                            BOTÓN
                    =========================-->

                    <button

                        type="submit"

                        id="btnLogin"

                        class="btn-login">

                        🔐 INICIAR SESIÓN

                    </button>

                    <!--========================
                            LOADER
                    =========================-->

                    <div

                        id="loader"

                        class="loader-login">

                        <div class="spinner"></div>

                        <h3>

                            Verificando credenciales...

                        </h3>

                        <p>

                            Espere un momento mientras validamos su información.

                        </p>

                    </div>

                </form>

            </div>

            <!--=============================================
                    PANEL DE INSTRUCCIONES
            ==============================================-->

            <div class="card-instrucciones">
                                <h2>📋 Instrucciones de Acceso</h2>

                <!--=========================================
                        FORMATO DEL USUARIO
                ==========================================-->

                <div class="bloque-instruccion">

                    <h3>① Formato del nombre de usuario</h3>

                    <p>
                        Escriba su nombre de usuario utilizando el siguiente formato:
                    </p>

                    <div class="ejemplo-usuario">

                        <strong>

                            APELLIDO PATERNO APELLIDO MATERNO NOMBRE(S)

                        </strong>

                    </div>

                    <ul>

                        <li>Utilice únicamente letras <strong>MAYÚSCULAS</strong>.</li>

                        <li>No utilice acentos.</li>

                        <li>Respete el orden indicado.</li>

                    </ul>

                </div>

                <!--=========================================
                        EJEMPLO
                ==========================================-->

                <div class="bloque-instruccion">

                    <h3>② Ejemplo</h3>

                    <div class="ejemplo">

                        PEREZ GARCIA JUAN CARLOS

                    </div>

                </div>

                <!--=========================================
                    PERSONAL ACADÉMICO
                ==========================================-->

                <div class="bloque-instruccion">

                    <h3>③ Acceso para personal académico</h3>

                    <ul>

                        <li>

                            Si usted es <strong>DOCENTE</strong>, ingrese utilizando su cuenta institucional registrada en el sistema.

                        </li>

                        <li>

                            Si usted es <strong>TUTOR</strong> o <strong>COORDINADOR</strong>, deberá iniciar sesión utilizando su cuenta de <strong>DOCENTE</strong>. No existe un acceso independiente para estos perfiles.

                        </li>

                    </ul>

                </div>

                <!--=========================================
                        SOPORTE
                ==========================================-->

                <div class="bloque-instruccion soporte">

                    <h3>🛠️ Soporte de acceso</h3>

                    <p>

                        Si presenta problemas para iniciar sesión, su cuenta no le permite acceder o ha olvidado su contraseña, favor de acudir al

                        <strong>

                            Departamento de Desarrollo Académico

                        </strong>

                        para recibir el apoyo correspondiente.

                    </p>

                </div>

            </div>

        </section>

        <!--=========================================
                    PIE DEL LOGIN
        ==========================================-->

        <section class="pie-login">

            <p>

                Portal Institucional de Tutorías (PIT)

            </p>

            <span>

                Sistema Integral para la Gestión del Programa Institucional de Tutorías

            </span>

            <small>

                © 2026 Instituto Tecnológico | Versión 4.0

            </small>

        </section>

    </main>

</div>

<script src="js_pit/animaciones.js"></script>

</body>

</html>

<?php
include("../includes/footer.php");
?>