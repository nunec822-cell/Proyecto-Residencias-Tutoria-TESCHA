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
OBTENER TUTOR LOGUEADO
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

$sqlTutor = mysqli_query(
    $conn,
    "
    SELECT
        id_personal
    FROM personal_academico
    WHERE id_usuario='$id_usuario'
    LIMIT 1
    "
);

if(
    mysqli_num_rows($sqlTutor)==0
){

    exit();

}

$tutor = mysqli_fetch_assoc(
    $sqlTutor
);

$id_tutor = $tutor['id_personal'];

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
ACTIVIDADES DEL TUTOR
=========================================*/

$sql = mysqli_query(

    $conn,

    "

    SELECT

        id_actividad,

        titulo,

        fecha_publicacion

    FROM actividades_tutor

    WHERE

        id_tutor='$id_tutor'

        AND

        grupo='$grupo'

        AND

        estado='ACTIVA'

    ORDER BY

        fecha_publicacion DESC

    "

);

if(
    mysqli_num_rows($sql)==0
){

    echo "

    <option value=''>

        No existen actividades publicadas

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

    $fila = mysqli_fetch_assoc($sql)

):

?>

<option
value="<?= $fila['id_actividad']; ?>"
>

<?= htmlspecialchars($fila['titulo']); ?>

(
<?= date(
"d/m/Y",
strtotime($fila['fecha_publicacion'])
); ?>

)

</option>

<?php

endwhile;

?>