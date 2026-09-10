<?php
require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}
include("../../base_pit/conect_pit.php");
include("../../includes_pit/sidebar_admin.php"); 





?>
<div class="separador"></div>
<div class="separador"></div>
<link rel="stylesheet" href="css/estilo.css">
<!-- ICONOS (opcional pero recomendado) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="contenedor-registro">
    
    <div class="titulo-principal">
        <h1>REGISTRO DE USUARIOS</h1>
        <p>Seleccione el tipo de usuario y complete la información requerida.</p>
        
    </div>
    <div class="contenedor-descarga">

    <a
        href="descargar_credenciales.php"
        class="btn-descargar"
    >
        📥 Descargar Credenciales
    </a>

</div>
    <div class="contenedor-grid">

        <!-- FORMULARIO -->
        <div class="card-formulario">
            <?php if(isset($_GET['ok'])){ ?>
            <div class="alerta-exito">
                ✓ Usuario registrado correctamente.
            </div>
            <?php } ?>

            <?php if(isset($_GET['error'])){ ?>
            <div class="mensaje-error">
                ✗ Ocurrió un error al registrar.
            </div>
            <?php } ?>

            <?php if(isset($_GET['usuario'])){ ?>
            <div class="mensaje-error">
                ✗ El usuario ya existe.
            </div>
            <?php } ?>
            <?php if(isset($_GET['error']) && $_GET['error']=="password"){ ?>
            <div class="mensaje-error">
                ✗ La contraseña ya está siendo utilizada por otro usuario.
            </div>
            <?php } ?>
            <?php if(isset($_GET['error']) && $_GET['error']=="tutor"){ ?>
            <div class="mensaje-error">
                ✗ Debe seleccionar un tutor para registrar al tutorado.
            </div>
            <?php } ?>
            <form
                id="formRegistro"
                action="guardar_usuario.php"
                method="POST"
            >

                <!-- TIPO -->
                <div class="grupo">
                    <label>Tipo de Usuario *</label>

                    <select
                        name="tipo_usuario"
                        id="tipo_usuario"
                        required
                    >
                        <option value="">
                            Seleccione...
                        </option>

                        <option value="DOCENTE">
                            DOCENTE
                        </option>

                        <option value="JEFE_CARRERA">
                            JEFE DE CARRERA
                        </option>

                        <option value="DIRECTIVO">
                            DIRECTIVO
                        </option>

                        <option value="PSICOLOGO">
                            PSICÓLOGO
                        </option>

                        <option value="TUTORADO">
                            TUTORADO
                        </option>
                    </select>
                </div>

                <!-- DATOS COMUNES -->
                <div id="datos_comunes" style="display:none;">

                    <div class="grupo">
                        <label>Usuario *   (Este dato será utilizado posteriormente para el inicio de sesión.)</label>
                        <input
                            type="text"
                            name="usuario"
                            required
                        >
                    </div>

                    <div class="grupo">
                        <label>Contraseña *   (Este dato será utilizado posteriormente para el inicio de sesión.) </label>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                        >

                        

                    </div>

                    <div class="grupo">
                        <label>
                            Confirmar Contraseña *
                        </label>

                        <input
                            type="password"
                            id="confirmar_password"
                            required
                        >

                        <div id="mensajeCoincide"></div>
                    </div>
                    <div class="fortaleza-box">

        <strong>
            Fortaleza de la contraseña
        </strong>

        <div id="fortaleza" class="fortaleza">
            Esperando contraseña...
        </div>

    </div>
                    <div class="grupo">
                        <label>Estado *</label>

                        <select
                            name="activo"
                            required
                        >
                            <option value="1">
                                Activo
                            </option>

                            <option value="0">
                                Inactivo
                            </option>
                        </select>
                    </div>

                </div>

                <!-- DOCENTE -->
                <div
                    id="form_docente"
                    class="formulario_dinamico"
                >

                    <h3>Información del Docente</h3>

                    <div class="grupo">
                        <label>No. Empleado *</label>

                        <input
                            type="text"
                            name="doc_no_empleado"
                        >
                    </div>

                    <div class="grupo">
                        <label>Nombre *</label>

                        <input
                            type="text"
                            name="doc_nombre"
                        >
                    </div>

                    <div class="grupo">
                        <label>Apellido Paterno *</label>

                        <input
                            type="text"
                            name="doc_apellido_p"
                        >
                    </div>

                    <div class="grupo">
                        <label>Apellido Materno *</label>

                        <input
                            type="text"
                            name="doc_apellido_m"
                        >
                    </div>

                    <div class="grupo">
                        <label>Carrera *</label>

                        <select name="doc_carrera">

                            <option value="">
                                Seleccione...
                            </option>

                            <option>
                                Ingeniería en Sistemas Computacionales
                            </option>

                            <option>
                                Ingeniería Industrial
                            </option>

                            <option>
                                Ingeniería en Informática
                            </option>

                            <option>
                                Ingeniería Electromecánica
                            </option>

                            <option>
                                Ingeniería Electrónica
                            </option>

                            <option>
                                Ingeniería en Administración
                            </option>

                        </select>
                    </div>

                    <div class="roles">
                        <h4>
                            Funciones Adicionales
                        </h4>

                        <label>
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="TUTOR"
                            >
                            Tutor
                        </label>

                        <label>
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="COORDINADOR"
                            >
                            Coordinador
                        </label>
                    </div>

                </div>

                <!-- JEFE -->
                <div
                    id="form_jefe"
                    class="formulario_dinamico"
                >

                    <h3>Jefe de Carrera</h3>

                    <input
                        type="text"
                        name="jefe_no_empleado"
                        placeholder="No. Empleado"
                    >

                    <input
                        type="text"
                        name="jefe_nombre"
                        placeholder="Nombre"
                    >

                    <input
                        type="text"
                        name="jefe_apellido_p"
                        placeholder="Apellido Paterno"
                    >

                    <input
                        type="text"
                        name="jefe_apellido_m"
                        placeholder="Apellido Materno"
                    >
                    <select name="jefe_carrera">

                        <option value="">
                            Seleccione Carrera
                        </option>

                        <option>
                            Ingeniería en Sistemas Computacionales
                        </option>

                        <option>
                            Ingeniería Industrial
                        </option>

                        <option>
                            Ingeniería en Informática
                        </option>

                        <option>
                            Ingeniería Electromecánica
                        </option>

                        <option>
                            Ingeniería Electrónica
                        </option>

                        <option>
                            Ingeniería en Administración
                        </option>

                    </select>
                </div>

                <!-- DIRECTIVO -->
                <div
                    id="form_directivo"
                    class="formulario_dinamico"
                >

                    <h3>Directivo</h3>

                    <input
                        type="text"
                        name="dir_no_empleado"
                        placeholder="No. Empleado"
                    >

                    <input
                        type="text"
                        name="dir_nombre"
                        placeholder="Nombre"
                    >

                    <input
                        type="text"
                        name="dir_apellido_p"
                        placeholder="Apellido Paterno"
                    >

                    <input
                        type="text"
                        name="dir_apellido_m"
                        placeholder="Apellido Materno"
                    >

                </div>

                <!-- PSICOLOGO -->
                <div
                    id="form_psicologo"
                    class="formulario_dinamico"
                >

                    <h3>Psicólogo</h3>

                    <input
                        type="text"
                        name="psi_no_empleado"
                        placeholder="No. Empleado"
                    >

                    <input
                        type="text"
                        name="psi_nombre"
                        placeholder="Nombre"
                    >

                    <input
                        type="text"
                        name="psi_apellido_p"
                        placeholder="Apellido Paterno"
                    >

                    <input
                        type="text"
                        name="psi_apellido_m"
                        placeholder="Apellido Materno"
                    >

                    <input
                        type="email"
                        name="correo_institucional"
                        placeholder="Correo Institucional"
                    >

                </div>

                <!-- TUTORADO -->
<div
    id="form_tutorado"
    class="formulario_dinamico"
>

    <h3>Tutorado</h3>

    <input
        type="text"
        name="matricula"
        placeholder="Matrícula"
    >

    <input
        type="text"
        name="alu_nombre"
        placeholder="Nombre"
    >

    <input
        type="text"
        name="alu_apellido_p"
        placeholder="Apellido Paterno"
    >

    <input
        type="text"
        name="alu_apellido_m"
        placeholder="Apellido Materno"
    >

    <select name="alu_carrera" id="alu_carrera">

        <option value="">
            Seleccione Carrera
        </option>

        <option value="Ingeniería en Sistemas Computacionales">
            Ingeniería en Sistemas Computacionales
        </option>

        <option value="Ingeniería Industrial">
            Ingeniería Industrial
        </option>

        <option value="Ingeniería en Informática">
            Ingeniería en Informática
        </option>

        <option value="Ingeniería Electromecánica">
            Ingeniería Electromecánica
        </option>

        <option value="Ingeniería Electrónica">
            Ingeniería Electrónica
        </option>

        <option value="Ingeniería en Administración">
            Ingeniería en Administración
        </option>

    </select>

    <input
        type="text"
        name="grupo"
        placeholder="Grupo"
    >

    <select
        name="id_tutor"
        id="id_tutor"
    >

        <option value="">
            Seleccione primero una carrera
        </option>

    </select>

</div>

                <div class="contenedor-botones">

    <button
        type="button"
        id="btnVistaPrevia"
        class="btn-vista-previa"
        style="display:none;"
    >
        Vista Previa
    </button>

    <button
        type="submit"
        class="btn-registrar"
        id="btnConfirmar"
        style="display:none;"
    >
        Confirmar Registro
    </button>

</div>
            </form>

        </div>

        <!-- AYUDA -->
        <div class="card-ayuda">

    <h3>Instrucciones</h3>
    <div class="aviso-usuario">
    <strong>IMPORTANTE:</strong><br>

    Registre al Usuario* en el siguiente formato:

    <br><br>

    <strong>
        APELLIDO PATERNO APELLIDO MATERNO NOMBRE(S)
    </strong>

    <br><br>

    Utilice únicamente letras MAYÚSCULAS y sin acentos,
    ya que este dato será utilizado para el inicio de sesión.

    <br><br>

    <strong>Ejemplo:</strong>

    <br>

    PEREZ GARCIA JOSE ANGEL
</div>
    <ul>
        <li>
            Registre cada usuario una sola vez.
        </li>

        <li>
            Un docente puede ser Tutor,
            Coordinador o ambos.
        </li>

        <li>
            Los cambios de roles se
            realizarán desde la vista Editar.
        </li>

        <li>
            Verifique cuidadosamente los datos.
        </li>
    </ul>

    <div class="separador-ayuda"></div>

    <h3>Contraseña Segura</h3>

    <ul class="lista-password">
        <li>✓ Al menos 8 caracteres</li>
        <li>✓ Una letra mayúscula</li>
        <li>✓ Una letra minúscula</li>
        <li>✓ Un número</li>
    </ul>

    

</div>

    </div>

</div>
<!-- MODAL VISTA PREVIA -->

<div
    id="modalPreview"
    class="modal-preview"
>

    <div class="modal-contenido">

        <h2>
            
        </h2>

        <div id="contenidoPreview">

        </div>

        <div class="acciones-preview">

            <button
                type="button"
                id="cerrarPreview"
            >
                Cancelar
            </button>

            <button
                type="button"
                id="aceptarPreview"
            >
                Confirmar
            </button>

        </div>

    </div>

</div>
<script src="js/animaciones.js"></script>

<?php include("../../../includes/footer.php"); ?>