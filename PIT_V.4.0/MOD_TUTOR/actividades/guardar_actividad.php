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
OBTENER TUTOR
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

$sql = mysqli_query(
    $conn,
    "
    SELECT id_personal
    FROM personal_academico
    WHERE id_usuario='$id_usuario'
    LIMIT 1
    "
);

if(
    mysqli_num_rows($sql)==0
){
    exit();
}

$tutor = mysqli_fetch_assoc($sql);

$id_tutor = $tutor['id_personal'];

/*=========================================
DATOS
=========================================*/

$grupo = mysqli_real_escape_string(
    $conn,
    trim($_POST['grupo'])
);

$titulo = mysqli_real_escape_string(
    $conn,
    trim($_POST['titulo'])
);

$descripcion = mysqli_real_escape_string(
    $conn,
    trim($_POST['descripcion'])
);

$link = mysqli_real_escape_string(
    $conn,
    trim($_POST['link'])
);

/*=========================================
VALIDACIONES
=========================================*/

if(
    empty($grupo) ||
    empty($titulo) ||
    empty($descripcion)
){

    echo "Complete todos los campos obligatorios.";

    exit();

}

if(
    strlen($titulo)>200
){

    echo "El título supera los 200 caracteres.";

    exit();

}

if(
    strlen($descripcion)>1000
){

    echo "La descripción supera los 1000 caracteres.";

    exit();

}

if(
    strlen($link)>500
){

    echo "El enlace supera los 500 caracteres.";

    exit();

}

/*=========================================
CARPETAS
=========================================*/

$rutaPDF =
"uploads/pdf/";

$rutaIMG =
"uploads/imagenes/";

$pdf = "";
$imagen1 = "";
$imagen2 = "";
$imagen3 = "";

/*=========================================
SUBIR PDF
=========================================*/

if(
    !empty($_FILES['pdf']['name'])
){

    if(
        $_FILES['pdf']['size'] >
        (10*1024*1024)
    ){

        echo "El PDF supera los 10 MB.";

        exit();

    }

    $extension =
    strtolower(
        pathinfo(
            $_FILES['pdf']['name'],
            PATHINFO_EXTENSION
        )
    );

    if(
        $extension!="pdf"
    ){

        echo "Solo se permiten archivos PDF.";

        exit();

    }

    $pdf =
    uniqid().
    ".pdf";

    move_uploaded_file(

        $_FILES['pdf']['tmp_name'],

        $rutaPDF.$pdf

    );

}

/*=========================================
FUNCIÓN SUBIR IMAGEN
=========================================*/

function subirImagen(
    $archivo,
    $ruta
){

    if(
        empty(
            $_FILES[$archivo]['name']
        )
    ){

        return "";

    }

    $permitidas = [

        "jpg",
        "jpeg",
        "png",
        "webp"

    ];

    $extension =
    strtolower(
        pathinfo(
            $_FILES[$archivo]['name'],
            PATHINFO_EXTENSION
        )
    );

    if(
        !in_array(
            $extension,
            $permitidas
        )
    ){

        return "ERROR";

    }

    $nombre =
    uniqid().
    ".".
    $extension;

    move_uploaded_file(

        $_FILES[$archivo]['tmp_name'],

        $ruta.$nombre

    );

    return $nombre;

}

$imagen1 =
subirImagen(
    "imagen1",
    $rutaIMG
);

$imagen2 =
subirImagen(
    "imagen2",
    $rutaIMG
);

$imagen3 =
subirImagen(
    "imagen3",
    $rutaIMG
);

if(

    $imagen1=="ERROR" ||

    $imagen2=="ERROR" ||

    $imagen3=="ERROR"

){

    echo "Formato de imagen no permitido.";

    exit();

}

/*=========================================
INSERTAR
=========================================*/

$sql = "

INSERT INTO actividades_tutor(

    id_tutor,

    grupo,

    titulo,

    descripcion,

    pdf,

    imagen1,

    imagen2,

    imagen3,

    link

)

VALUES(

    '$id_tutor',

    '$grupo',

    '$titulo',

    '$descripcion',

    '$pdf',

    '$imagen1',

    '$imagen2',

    '$imagen3',

    '$link'

)

";

if(
    mysqli_query(
        $conn,
        $sql
    )
){

    echo "OK";

}
else{

    echo "ERROR";

}

?>