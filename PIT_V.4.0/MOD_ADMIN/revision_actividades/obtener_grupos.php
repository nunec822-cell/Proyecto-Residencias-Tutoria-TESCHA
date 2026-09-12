<?php

require_once("../../sesion.php");

if(
    !in_array(
        "ADMIN",
        $_SESSION["roles"]
    )
){
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
CARRERA
=========================================*/

$carrera = mysqli_real_escape_string(

    $conn,

    $_GET['carrera'] ?? ""

);

if(
    empty($carrera)
){

    echo "

    <option value=''>

        Seleccione una carrera

    </option>

    ";

    exit();

}

/*=========================================
OBTENER GRUPOS
=========================================*/

$sql = mysqli_query(

    $conn,

    "

    SELECT DISTINCT

        grupo

    FROM tutorados

    WHERE

        carrera='$carrera'

        AND

        activo='1'

    ORDER BY

        grupo ASC

    "

);

if(
    !$sql
){

    echo "

    <option value=''>

        Error al obtener los grupos

    </option>

    ";

    exit();

}

if(
    mysqli_num_rows($sql)==0
){

    echo "

    <option value=''>

        No existen grupos

    </option>

    ";

    exit();

}

?>

<option value="">

Seleccione un grupo

</option>

<?php

while(
    $fila =
    mysqli_fetch_assoc($sql)
):

?>

<option
value="<?= htmlspecialchars($fila['grupo']); ?>"
>

    <?= strtoupper(htmlspecialchars($fila['grupo'])); ?>

</option>

<?php

endwhile;

?>