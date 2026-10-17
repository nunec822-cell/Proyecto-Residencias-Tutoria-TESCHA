<?php
require_once __DIR__ . '/../../../core/bootstrap.php';

require_once("../../sesion.php");

requerir_rol('ADMIN');

include("../../base_pit/conect_pit.php");

/*=========================================
CARRERA
=========================================*/

$carrera = sist_esc($_GET['carrera'] ?? '');

if(empty($carrera)){

    echo '
        <option value="">
            Seleccione una carrera
        </option>
    ';

    exit();
}

/*=========================================
OBTENER GRUPOS
=========================================*/

$sql = mysqli_query(
    $conn,
    "
    SELECT DISTINCT grupo
    FROM tutorados
    WHERE carrera='$carrera'
    AND activo=1
    ORDER BY grupo
    "
);

/*=========================================
SIN RESULTADOS
=========================================*/

if(mysqli_num_rows($sql) == 0){

    echo '
        <option value="">
            No existen grupos
        </option>
    ';

    exit();
}

/*=========================================
MOSTRAR GRUPOS
=========================================*/

echo '
    <option value="">
        Seleccione un grupo
    </option>
';

while(
    $grupo =
    mysqli_fetch_assoc(
        $sql
    )
){

    ?>

    <option
        value="<?= $grupo['grupo']; ?>"
    >

        <?= $grupo['grupo']; ?>

    </option>

    <?php
}

?>