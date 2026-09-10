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
OBTENER USUARIO LOGUEADO
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

/*=========================================
OBTENER ID DEL PERSONAL
=========================================*/

$sql_tutor = mysqli_query(
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
    mysqli_num_rows($sql_tutor) == 0
){

    echo "
    <option value=''>
        Tutor no encontrado
    </option>
    ";

    exit();

}

$tutor = mysqli_fetch_assoc(
    $sql_tutor
);

$id_personal = $tutor['id_personal'];

/*=========================================
OBTENER EL id_usuario DEL TUTOR
(Así está relacionado en la tabla tutorados)
=========================================*/

$sql_usuario = mysqli_query(
    $conn,
    "
    SELECT
        id_usuario
    FROM personal_academico
    WHERE id_personal='$id_personal'
    LIMIT 1
    "
);

$usuario = mysqli_fetch_assoc(
    $sql_usuario
);

$id_usuario_tutor = $usuario['id_usuario'];

/*=========================================
OBTENER SOLO LOS GRUPOS
DONDE ES TUTOR
=========================================*/

$sql = mysqli_query(
    $conn,
    "
    SELECT DISTINCT
        grupo
    FROM tutorados
    WHERE
        id_tutor='$id_usuario_tutor'
    ORDER BY
        grupo
    "
);

if(
    mysqli_num_rows($sql) == 0
){

    echo "
    <option value=''>
        No tiene grupos asignados
    </option>
    ";

    exit();

}

echo "
<option value=''>
Seleccione un grupo
</option>
";

while(
    $fila =
    mysqli_fetch_assoc($sql)
){

?>

<option
    value="<?= $fila['grupo']; ?>"
>

    <?= strtoupper(
        $fila['grupo']
    ); ?>

</option>

<?php

}

?>