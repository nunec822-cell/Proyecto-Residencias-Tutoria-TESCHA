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
OBTENER CARRERAS
=========================================*/

$sql = mysqli_query(

    $conn,

    "

    SELECT DISTINCT

        carrera

    FROM materias

    WHERE

        carrera IS NOT NULL

        AND

        carrera <> ''

    ORDER BY

        carrera ASC

    "

);

if(
    !$sql
){

    echo "

    <option value=''>

        Error al cargar las carreras

    </option>

    ";

    exit();

}

if(
    mysqli_num_rows($sql)==0
){

    echo "

    <option value=''>

        No existen carreras registradas

    </option>

    ";

    exit();

}

?>

<option value="">

    Seleccione una carrera

</option>

<?php

while(
    $fila =
    mysqli_fetch_assoc($sql)
):

?>

<option
    value="<?= htmlspecialchars($fila['carrera']); ?>"
>

    <?= strtoupper(htmlspecialchars($fila['carrera'])); ?>

</option>

<?php

endwhile;

?>