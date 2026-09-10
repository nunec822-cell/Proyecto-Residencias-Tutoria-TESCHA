<?php

require_once("../../sesion.php");

if(
    !in_array(
        "TUTOR",
        $_SESSION["roles"]
    )
){
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
ID DEL TUTOR LOGUEADO
=========================================*/

$id_tutor = $_SESSION['id_usuario'];

/*=========================================
OBTENER GRUPOS DEL TUTOR
=========================================*/

$sql = mysqli_query(
    $conn,
    "
    SELECT DISTINCT

        grupo

    FROM tutorados

    WHERE

        id_tutor='$id_tutor'

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

        No tiene grupos asignados

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