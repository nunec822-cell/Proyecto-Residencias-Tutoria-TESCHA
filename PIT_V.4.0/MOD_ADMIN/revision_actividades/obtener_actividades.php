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
GRUPO
=========================================*/

$grupo = mysqli_real_escape_string(

    $conn,

    $_GET['grupo'] ?? ""

);

if(
    empty($grupo)
){

    echo "

    <option value=''>

        Seleccione un grupo

    </option>

    ";

    exit();

}

/*=========================================
OBTENER ACTIVIDADES
=========================================*/

$sql = mysqli_query(

    $conn,

    "

    SELECT

        a.id_actividad,

        a.titulo,

        a.fecha_publicacion,

        CONCAT(

            p.nombre,' ',

            p.apellido_p,' ',

            p.apellido_m

        ) AS tutor

    FROM actividades_tutor a

    INNER JOIN personal_academico p

        ON a.id_tutor = p.id_personal

    WHERE

        a.grupo='$grupo'

        AND

        a.estado='ACTIVA'

    ORDER BY

        a.fecha_publicacion DESC

    "

);

if(
    !$sql
){

    echo "

    <option value=''>

        Error al cargar actividades

    </option>

    ";

    exit();

}

if(
    mysqli_num_rows($sql)==0
){

    echo "

    <option value=''>

        No existen actividades para este grupo

    </option>

    ";

    exit();

}

?>

<option value="">

Seleccione una actividad

</option>

<?php

while(
    $fila =
    mysqli_fetch_assoc($sql)
):

?>

<option
value="<?= $fila['id_actividad']; ?>"
>

<?= strtoupper(htmlspecialchars($fila['titulo'])); ?>

| Tutor:
<?= strtoupper(htmlspecialchars($fila['tutor'])); ?>

| <?= date(
    "d/m/Y H:i",
    strtotime($fila['fecha_publicacion'])
); ?>

</option>

<?php

endwhile;

?>