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

include(
    "../../base_pit/conect_pit.php"
);

/*=========================================
OBTENER TUTOR LOGUEADO
=========================================*/

$id_usuario =
$_SESSION[
    'id_usuario'
];

/*=========================================
ID DEL TUTOR
=========================================*/

$sql_tutor =
mysqli_query(
    $conn,
    "
    SELECT
        id_personal
    FROM personal_academico
    WHERE id_usuario =
    '$id_usuario'
    LIMIT 1
    "
);

$tutor =
mysqli_fetch_assoc(
    $sql_tutor
);

if(
    !$tutor
){

    echo "
    <option value=''>
        Tutor no encontrado
    </option>
    ";

    exit();

}

$id_tutor =
$tutor[
    'id_personal'
];

/*=========================================
OBTENER GRUPOS
=========================================*/

$sql =
mysqli_query(
    $conn,
    "
    SELECT DISTINCT
        grupo
    FROM tutorados
    WHERE id_tutor =
    '$id_usuario'
    ORDER BY grupo
    "
);

/*=========================================
PRIMERA OPCIÓN
=========================================*/

echo "
<option value=''>
    Seleccione...
</option>
";

/*=========================================
MOSTRAR GRUPOS
=========================================*/

if(
    mysqli_num_rows(
        $sql
    ) == 0
){

    echo "
    <option value=''>
        Sin grupos asignados
    </option>
    ";

    exit();

}

while(
    $fila =
    mysqli_fetch_assoc(
        $sql
    )
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