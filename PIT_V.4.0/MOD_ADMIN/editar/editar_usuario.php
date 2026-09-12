<?php

require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}
include("../../base_pit/conect_pit.php");
include("../../includes_pit/sidebar_admin.php");

if(!isset($_GET['id']))
{
    header("Location:index.php");
    exit();
}

$id_usuario = intval($_GET['id']);

/*=========================================
USUARIO
=========================================*/

$sql = "
SELECT *
FROM usuarios
WHERE id_usuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();

$usuario = $stmt->get_result()->fetch_assoc();

if(!$usuario)
{
    header("Location:index.php");
    exit();
}

/*=========================================
ROLES
=========================================*/

$roles_usuario = [];

$sql = "
SELECT t.nombre
FROM usuario_tipo ut
INNER JOIN tipos t
ON ut.id_tipo = t.id_tipo
WHERE ut.id_usuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();

$result = $stmt->get_result();

while($fila = $result->fetch_assoc())
{
    $roles_usuario[] = $fila['nombre'];
}

/*=========================================
PERSONAL ACADÉMICO
=========================================*/

$personal = null;

$sql = "
SELECT *
FROM personal_academico
WHERE id_usuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();

$res = $stmt->get_result();

if($res->num_rows > 0)
{
    $personal = $res->fetch_assoc();
}

/*=========================================
DIRECTIVO
=========================================*/

$directivo = null;

$sql = "
SELECT *
FROM directivos
WHERE id_usuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();

$res = $stmt->get_result();

if($res->num_rows > 0)
{
    $directivo = $res->fetch_assoc();
}

/*=========================================
PSICOLOGO
=========================================*/

$psicologo = null;

$sql = "
SELECT *
FROM administradores_psicologos
WHERE id_usuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();

$res = $stmt->get_result();

if($res->num_rows > 0)
{
    $psicologo = $res->fetch_assoc();
}

/*=========================================
TUTORADO
=========================================*/

$tutorado = null;

$sql = "
SELECT *
FROM tutorados
WHERE id_usuario = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();

$res = $stmt->get_result();

if($res->num_rows > 0)
{
    $tutorado = $res->fetch_assoc();
}
/*=========================================
TUTORES
=========================================*/

$tutores = $conn->query("
SELECT
    pa.id_usuario,
    CONCAT(
        pa.nombre,
        ' ',
        pa.apellido_p,
        ' ',
        pa.apellido_m
    ) AS tutor
FROM personal_academico pa
INNER JOIN usuario_tipo ut
    ON pa.id_usuario = ut.id_usuario
WHERE ut.id_tipo = 6
ORDER BY pa.nombre
");
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="css/estilo.css">
<div class="separador"></div>
<div class="separador"></div>
<div class="contenedor-editar">

    <div class="titulo-principal">

        <h1>EDITAR USUARIO</h1>

        <p>
            Modifique la información del usuario.
        </p>

    </div>

    <div class="card-edicion">

        <form
            action="guardar_cambios.php"
            method="POST"
        >

            <input
                type="hidden"
                name="id_usuario"
                value="<?php echo $id_usuario; ?>"
            >

            <div class="grupo">

                <label>Usuario</label>

                <input
                    type="text"
                    name="usuario"
                    value="<?php echo $usuario['usuario']; ?>"
                    required
                >

            </div>

            <div class="grupo">

                <label>Nueva Contraseña</label>

                <input
                    type="password"
                    name="password"
                    id="password"
                >

            </div>

            <div class="grupo">

                <label>
                    Confirmar Contraseña
                </label>

                <input
                    type="password"
                    id="confirmar_password"
                >

                <div id="mensajeCoincide"></div>


            </div>
<!-- PANEL CONTRASEÑA + IMPORTANTE -->

<div class="paneles-inferiores">

    <div class="panel-password">

        <h4>Contraseña Segura</h4>

        <ul>
            <li>✓ Al menos 8 caracteres</li>
            <li>✓ Una letra mayúscula</li>
            <li>✓ Una letra minúscula</li>
            <li>✓ Un número</li>
        </ul>

        <div id="fortaleza">
            Fortaleza de la contraseña
        </div>

    </div>

    <div class="panel-importante">

        <h4>IMPORTANTE</h4>

        <p>
            Registre al <strong>Usuario</strong>
            en el siguiente formato:
        </p>

        <div class="ejemplo-formato">

            APELLIDO PATERNO
            APELLIDO MATERNO
            NOMBRE(S)

        </div>

        <p>
            Utilice únicamente letras
            <strong>MAYÚSCULAS</strong>
            y sin acentos.
        </p>

        <p>
            <strong>Ejemplo:</strong><br>
            PEREZ GARCIA JOSE ANGEL
        </p>

    </div>

</div>
            <div class="grupo">

                <label>Estado</label>

                <select name="activo">

                    <option
                        value="1"
                        <?php
                        if($usuario['activo']==1)
                        echo "selected";
                        ?>
                    >
                        Activo
                    </option>

                    <option
                        value="0"
                        <?php
                        if($usuario['activo']==0)
                        echo "selected";
                        ?>
                    >
                        Inactivo
                    </option>

                </select>

            </div>

            <hr>

            <h3>Información del Usuario</h3>

            <?php if($personal){ ?>

            <h4>Información Académica</h4>

            <div class="grupo">

                <label>No. Empleado</label>

                <input
                    type="text"
                    name="no_empleado"
                    value="<?php echo $personal['no_empleado']; ?>"
                >

            </div>

            <div class="grupo">

                <label>Nombre</label>

                <input
                    type="text"
                    name="nombre"
                    value="<?php echo $personal['nombre']; ?>"
                >

            </div>

            <div class="grupo">

                <label>Apellido Paterno</label>

                <input
                    type="text"
                    name="apellido_p"
                    value="<?php echo $personal['apellido_p']; ?>"
                >

            </div>

            <div class="grupo">

                <label>Apellido Materno</label>

                <input
                    type="text"
                    name="apellido_m"
                    value="<?php echo $personal['apellido_m']; ?>"
                >

            </div>

            <div class="grupo">

                <label>Carrera</label>

                <select name="carrera">
<option
<?php if($personal['carrera']=="Ingeniería en Sistemas Computacionales") echo "selected"; ?>
>
Ingeniería en Sistemas Computacionales
</option>

<option
<?php if($personal['carrera']=="Ingeniería Industrial") echo "selected"; ?>
>
Ingeniería Industrial
</option>

<option
<?php if($personal['carrera']=="Ingeniería en Informática") echo "selected"; ?>
>
Ingeniería en Informática
</option>

<option
<?php if($personal['carrera']=="Ingeniería Electromecánica") echo "selected"; ?>
>
Ingeniería Electromecánica
</option>

<option
<?php if($personal['carrera']=="Ingeniería Electrónica") echo "selected"; ?>
>
Ingeniería Electrónica
</option>

<option
<?php if($personal['carrera']=="Ingeniería en Administración") echo "selected"; ?>
>
Ingeniería en Administración
</option>

</select>

</div>

<?php } ?>

<?php if($directivo){ ?>

<hr>

<h4>Información del Directivo</h4>

<div class="grupo">

    <label>No. Empleado</label>

    <input
        type="text"
        name="dir_no_empleado"
        value="<?php echo $directivo['no_empleado']; ?>"
    >

</div>

<div class="grupo">

    <label>Nombre</label>

    <input
        type="text"
        name="dir_nombre"
        value="<?php echo $directivo['nombre']; ?>"
    >

</div>

<div class="grupo">

    <label>Apellido Paterno</label>

    <input
        type="text"
        name="dir_apellido_p"
        value="<?php echo $directivo['apellido_p']; ?>"
    >

</div>

<div class="grupo">

    <label>Apellido Materno</label>

    <input
        type="text"
        name="dir_apellido_m"
        value="<?php echo $directivo['apellido_m']; ?>"
    >

</div>

<?php } ?>

<?php if($psicologo){ ?>

<hr>

<h4>Información del Psicólogo</h4>

<div class="grupo">

    <label>No. Empleado</label>

    <input
        type="text"
        name="psi_no_empleado"
        value="<?php echo $psicologo['no_empleado']; ?>"
    >

</div>

<div class="grupo">

    <label>Nombre</label>

    <input
        type="text"
        name="psi_nombre"
        value="<?php echo $psicologo['nombre']; ?>"
    >

</div>

<div class="grupo">

    <label>Apellido Paterno</label>

    <input
        type="text"
        name="psi_apellido_p"
        value="<?php echo $psicologo['apellido_p']; ?>"
    >

</div>

<div class="grupo">

    <label>Apellido Materno</label>

    <input
        type="text"
        name="psi_apellido_m"
        value="<?php echo $psicologo['apellido_m']; ?>"
    >

</div>

<div class="grupo">

    <label>Correo Institucional</label>

    <input
        type="email"
        name="correo_institucional"
        value="<?php echo $psicologo['correo_institucional']; ?>"
    >

</div>

<?php } ?>

<?php if($tutorado){ ?>

<hr>

<h4>Información del Tutorado</h4>

<div class="grupo">

    <label>Matrícula</label>

    <input
        type="text"
        name="matricula"
        value="<?php echo $tutorado['matricula']; ?>"
    >

</div>

<div class="grupo">

    <label>Nombre</label>

    <input
        type="text"
        name="alu_nombre"
        value="<?php echo $tutorado['nombre']; ?>"
    >

</div>

<div class="grupo">

    <label>Apellido Paterno</label>

    <input
        type="text"
        name="alu_apellido_p"
        value="<?php echo $tutorado['apellido_p']; ?>"
    >

</div>

<div class="grupo">

    <label>Apellido Materno</label>

    <input
        type="text"
        name="alu_apellido_m"
        value="<?php echo $tutorado['apellido_m']; ?>"
    >

</div>
<div class="grupo">

    <label>Carrera</label>

    <select name="alu_carrera">

        <option
            value="Ingeniería en Sistemas Computacionales"
            <?php if($tutorado['carrera']=="Ingeniería en Sistemas Computacionales") echo "selected"; ?>
        >
            Ingeniería en Sistemas Computacionales
        </option>

        <option
            value="Ingeniería Industrial"
            <?php if($tutorado['carrera']=="Ingeniería Industrial") echo "selected"; ?>
        >
            Ingeniería Industrial
        </option>

        <option
            value="Ingeniería en Informática"
            <?php if($tutorado['carrera']=="Ingeniería en Informática") echo "selected"; ?>
        >
            Ingeniería en Informática
        </option>

        <option
            value="Ingeniería Electromecánica"
            <?php if($tutorado['carrera']=="Ingeniería Electromecánica") echo "selected"; ?>
        >
            Ingeniería Electromecánica
        </option>

        <option
            value="Ingeniería Electrónica"
            <?php if($tutorado['carrera']=="Ingeniería Electrónica") echo "selected"; ?>
        >
            Ingeniería Electrónica
        </option>

        <option
            value="Ingeniería en Administración"
            <?php if($tutorado['carrera']=="Ingeniería en Administración") echo "selected"; ?>
        >
            Ingeniería en Administración
        </option>

    </select>

</div>
<div class="grupo">

    <label>Grupo</label>

    <input
        type="text"
        name="grupo"
        value="<?php echo $tutorado['grupo']; ?>"
    >

</div>
<div class="grupo">

    <label>Tutor Asignado</label>

    <select name="id_tutor">

        <option value="">
            Seleccione Tutor
        </option>

        <?php
        while($row = $tutores->fetch_assoc())
        {
        ?>

            <option
                value="<?php echo $row['id_usuario']; ?>"

                <?php
                if(
                    $row['id_usuario']
                    ==
                    $tutorado['id_tutor']
                )
                {
                    echo "selected";
                }
                ?>
            >

                <?php echo $row['tutor']; ?>

            </option>

        <?php
        }
        ?>

    </select>

</div>
<?php } ?>

<?php
if($personal)
{
?>

<hr>

<h3>Roles Asignados</h3>

<div class="roles">

    <label>

        <input
            type="checkbox"
            name="roles[]"
            value="DOCENTE"

            <?php
            if(in_array(
                "DOCENTE",
                $roles_usuario
            ))
            echo "checked";
            ?>
        >

        DOCENTE

    </label>

    <label>

        <input
            type="checkbox"
            name="roles[]"
            value="TUTOR"

            <?php
            if(in_array(
                "TUTOR",
                $roles_usuario
            ))
            echo "checked";
            ?>
        >

        TUTOR

    </label>

    <label>

        <input
            type="checkbox"
            name="roles[]"
            value="COORDINADOR"

            <?php
            if(in_array(
                "COORDINADOR",
                $roles_usuario
            ))
            echo "checked";
            ?>
        >

        COORDINADOR

    </label>

</div>

<?php
}
?>



<button
    type="submit"
    class="btn-guardar"
>
    Guardar Cambios
</button>

</form>

</div>

</div>

<script src="js/animaciones.js"></script>

<?php include("../../../includes/footer.php"); ?>