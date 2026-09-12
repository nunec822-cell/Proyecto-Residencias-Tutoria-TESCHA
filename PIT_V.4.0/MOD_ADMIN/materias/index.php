<?php
require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../../indexloguin.php");
    exit();
}

include("../../base_pit/conect_pit.php");
?>

<?php
/*=========================================
    REGISTRAR MATERIA
=========================================*/
if(isset($_POST["registrar"])){

    $materia = trim(mb_strtoupper($_POST["materia"], 'UTF-8'));
    $carrera = $_POST["carrera"];

    if(!empty($materia)){

        $stmt = $conn->prepare("
            INSERT INTO materias(nombre_materia,carrera)
            VALUES(?,?)
        ");

        $stmt->bind_param("ss",$materia,$carrera);
        $stmt->execute();
    }
}

/*=========================================
    ELIMINAR
=========================================*/
if(isset($_GET["eliminar"])){

    $id = intval($_GET["eliminar"]);

    $stmt = $conn->prepare("
        DELETE FROM materias
        WHERE id_materia = ?
    ");

    $stmt->bind_param("i",$id);
    $stmt->execute();
}

/*=========================================
    CONSULTAR
=========================================*/
$sql = "
SELECT *
FROM materias
ORDER BY carrera,nombre_materia
";

$resultado = $conn->query($sql);
$contador = 1;
?>
<?php include("../../includes_pit/sidebar_admin.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Gestión de Materias</title>

<link rel="stylesheet" href="css/estilo.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>
<body>
<div class="separador"></div>
<div class="separador"></div>

<div class="contenedor">

    <div class="encabezado">

        <h1>
            <i class="fas fa-book"></i>
            Gestión de Materias
        </h1>

        <p>
            Administra las materias disponibles para cada carrera.
        </p>

    </div>

    <div class="tarjeta-formulario">

        <form method="POST">

            <div class="grupo">

                <label>Carrera</label>

                <select name="carrera" required>

                    <option value="">Seleccionar</option>

                    <option value="INGENIERIA EN SISTEMAS COMPUTACIONALES">
                        Ingeniería en Sistemas Computacionales
                    </option>

                    <option value="INGENIERIA INDUSTRIAL">
                        Ingeniería Industrial
                    </option>

                    <option value="INGENIERIA EN INFORMATICA">
                        Ingeniería en Informática
                    </option>

                    <option value="INGENIERIA EN ELECTROMECANICA">
                        Ingeniería en Electromecánica
                    </option>

                    <option value="INGENIERIA ELECTRONICA">
                        Ingeniería Electrónica
                    </option>

                    <option value="INGENIERIA EN ADMINISTRACION">
                        Ingeniería en Administración
                    </option>

                </select>

            </div>

            <div class="grupo">

                <label>Materia</label>

                <input
                    type="text"
                    name="materia"
                    placeholder="Nombre de la materia"
                    required>

            </div>

            <button
                type="submit"
                name="registrar"
                class="btn-guardar">

                <i class="fas fa-plus"></i>
                Agregar Materia

            </button>

        </form>

    </div>

    <div class="tabla-contenedor">

        <table>

            <thead>

                <tr>

                    <th>#</th>
                    <th>Materia</th>
                    <th>Carrera</th>
                    <th>Fecha</th>
                    <th>Acción</th>

                </tr>

            </thead>

            <tbody>

            <?php while($fila = $resultado->fetch_assoc()): ?>

                <tr>

                    <td><?= $contador++ ?></td>

                    <td><?= htmlspecialchars($fila["nombre_materia"]) ?></td>

                    <td><?= htmlspecialchars($fila["carrera"]) ?></td>

                    <td><?= $fila["fecha_registro"] ?></td>

                    <td>

                        <a
                        href="?eliminar=<?= $fila["id_materia"] ?>"
                        class="btn-eliminar"
                        onclick="return confirm('¿Eliminar materia?')">

                            <i class="fas fa-trash"></i>

                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

<script src="js/animaciones.js"></script>

</body>
</html>