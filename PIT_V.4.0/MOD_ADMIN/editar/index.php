<?php
require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}
include("../../base_pit/conect_pit.php");
include("../../includes_pit/sidebar_admin.php"); 


/*=========================================
BUSCADOR
=========================================*/

$busqueda = "";

if(isset($_GET['buscar']))
{
    $busqueda = trim($_GET['buscar']);
}

$sql = "
SELECT DISTINCT

u.id_usuario,
u.usuario,
u.activo,

GROUP_CONCAT(
    t.nombre
    SEPARATOR ', '
) AS roles

FROM usuarios u

LEFT JOIN usuario_tipo ut
    ON u.id_usuario = ut.id_usuario

LEFT JOIN tipos t
    ON ut.id_tipo = t.id_tipo

LEFT JOIN personal_academico pa
    ON u.id_usuario = pa.id_usuario

LEFT JOIN directivos d
    ON u.id_usuario = d.id_usuario

LEFT JOIN administradores_psicologos ap
    ON u.id_usuario = ap.id_usuario

LEFT JOIN tutorados tu
    ON u.id_usuario = tu.id_usuario

WHERE

u.usuario LIKE ?

OR pa.nombre LIKE ?
OR pa.apellido_p LIKE ?
OR pa.apellido_m LIKE ?
OR pa.no_empleado LIKE ?

OR d.nombre LIKE ?
OR d.apellido_p LIKE ?
OR d.apellido_m LIKE ?
OR d.no_empleado LIKE ?

OR ap.nombre LIKE ?
OR ap.apellido_p LIKE ?
OR ap.apellido_m LIKE ?
OR ap.no_empleado LIKE ?

OR tu.nombre LIKE ?
OR tu.apellido_p LIKE ?
OR tu.apellido_m LIKE ?
OR tu.matricula LIKE ?

GROUP BY u.id_usuario

ORDER BY u.usuario
";

$stmt = $conn->prepare($sql);

$valor = "%".$busqueda."%";

$stmt->bind_param(
    "sssssssssssssssss",
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor,
    $valor
);

$stmt->execute();

$resultado = $stmt->get_result();

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/estilo.css">
<div class="separador"></div>
<div class="separador"></div>

<div class="contenedor-editar">

    <div class="titulo-principal">

        <h1>EDITAR USUARIOS</h1>

        <p>
            Busque un usuario para visualizar
            o modificar su información.
        </p>

    </div>

    <!-- BUSCADOR -->

    <div class="card-busqueda">

        <form method="GET">

            <input
                type="text"
                name="buscar"
                placeholder="Buscar por usuario, nombre, matrícula o no. empleado..."
                value="<?php echo htmlspecialchars($busqueda); ?>"
            >

            <button type="submit">
                Buscar
            </button>
            <?php if(isset($_GET['ok'])){ ?>
            <div class="alerta-exito">
                 ✓ Usuario actualizado correctamente.
            </div>
            <?php } ?>
            
            <?php if(isset($_GET['error'])){ ?>
            <div class="mensaje-error">
            ✗ Ocurrió un error al actualizar.
            </div>
            <?php } ?>
            <?php if(isset($_GET['password'])){ ?>
            <div class="mensaje-error">
            ✗ La contraseña ya está siendo utilizada por otro usuario.
            </div>
<?php } ?>
        </form>

    </div>

    <!-- RESULTADOS -->
    <div class="contenedor-cards">

<?php

if($resultado->num_rows > 0)
{

    while($fila = $resultado->fetch_assoc())
    {

        $estadoClase =
        ($fila['activo'] == 1)
        ? "estado-activo"
        : "estado-inactivo";

        $estadoTexto =
        ($fila['activo'] == 1)
        ? "ACTIVO"
        : "INACTIVO";

        ?>

        <div class="card-usuario fadeIn">

            <div class="icono-usuario">
                👤
            </div>

            <div class="nombre-usuario">
                <?php echo $fila['usuario']; ?>
            </div>

            <div class="roles-usuario">
                <?php echo $fila['roles']; ?>
            </div>

            <div class="<?php echo $estadoClase; ?>">
                <?php echo $estadoTexto; ?>
            </div>

            <a
                href="editar_usuario.php?id=<?php echo $fila['id_usuario']; ?>"
                class="btn-editar"
            >
                ✏ Editar Usuario
            </a>

        </div>

        <?php

    }

}
else
{
    ?>

    <div class="sin-resultados">

        <h3>
            No se encontraron usuarios.
        </h3>

        <p>
            Intente realizar otra búsqueda.
        </p>

    </div>

    <?php
}

?>

</div>
    

    

</div>

<script src="js/animaciones.js"></script>

<?php include("../../../includes/footer.php"); ?>