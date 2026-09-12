<?php
/*=========================================================
    GUARDAR AVISO - DEPARTAMENTO DE PSICOLOGÍA
=========================================================*/

session_start();

header('Content-Type: application/json');

require_once("../base_pit/conect_pit.php");

/*=========================================================
    VALIDAR SESIÓN
=========================================================*/

if (
    !isset($_SESSION["autenticado"]) ||
    !in_array("PSICOLOGO", $_SESSION["roles"])
){

    echo json_encode([
        "ok"=>false,
        "mensaje"=>"Acceso no autorizado."
    ]);

    exit();
}

/*=========================================================
    OBTENER ID DEL PSICÓLOGO
=========================================================*/

$idUsuario = $_SESSION["id_usuario"];

$sql = "SELECT id_registro
        FROM administradores_psicologos
        WHERE id_usuario=? AND activo=1
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$idUsuario);

$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows==0){

    echo json_encode([
        "ok"=>false,
        "mensaje"=>"No se encontró el psicólogo."
    ]);

    exit();
}

$psicologo = $resultado->fetch_assoc();

$idPsicologo = $psicologo["id_registro"];


/*=========================================================
    RECIBIR DATOS
=========================================================*/

$titulo = trim($_POST["titulo"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$categoria = trim($_POST["categoria"] ?? "AVISO");
$estado = trim($_POST["estado"] ?? "PUBLICADO");
$enlace = trim($_POST["enlace"] ?? "");


/*=========================================================
    VALIDACIONES
=========================================================*/

if($titulo==""){

    echo json_encode([
        "ok"=>false,
        "mensaje"=>"Ingrese un título."
    ]);

    exit();
}

if($descripcion==""){

    echo json_encode([
        "ok"=>false,
        "mensaje"=>"Ingrese una descripción."
    ]);

    exit();
}


/*=========================================================
    CARPETAS
=========================================================*/

$carpetaImagen = __DIR__ . "/uploads/imagenes/";
$carpetaPdf    = __DIR__ . "/uploads/pdf/";

/*=========================================================
    SUBIR IMAGEN
=========================================================*/

$nombreImagen=NULL;

if(
    isset($_FILES["imagen"]) &&
    $_FILES["imagen"]["error"]==0
){

    $extension = strtolower(
        pathinfo(
            $_FILES["imagen"]["name"],
            PATHINFO_EXTENSION
        )
    );

    $permitidas=[
        "jpg",
        "jpeg",
        "png",
        "webp"
    ];

    if(!in_array($extension,$permitidas)){

        echo json_encode([
            "ok"=>false,
            "mensaje"=>"Formato de imagen no permitido."
        ]);

        exit();
    }

    $nombreImagen =
        uniqid("IMG_").".".$extension;

    move_uploaded_file(

        $_FILES["imagen"]["tmp_name"],

        $carpetaImagen.$nombreImagen

    );

}


/*=========================================================
    SUBIR PDF
=========================================================*/

$nombrePdf=NULL;

if(

    isset($_FILES["pdf"]) &&
    $_FILES["pdf"]["error"]==0

){

    $extension=strtolower(

        pathinfo(
            $_FILES["pdf"]["name"],
            PATHINFO_EXTENSION
        )

    );

    if($extension!="pdf"){

        echo json_encode([

            "ok"=>false,

            "mensaje"=>"Solo se permiten archivos PDF."

        ]);

        exit();

    }

    $nombrePdf=
        uniqid("PDF_").".pdf";

    move_uploaded_file(

        $_FILES["pdf"]["tmp_name"],

        $carpetaPdf.$nombrePdf

    );

}


/*=========================================================
    INSERTAR
=========================================================*/

$sql="

INSERT INTO avisos_psicologia(

id_psicologo,

titulo,

descripcion,

imagen,

pdf,

enlace,

categoria,

estado

)

VALUES(

?,?,?,?,?,?,?,?

)

";

$stmt=$conn->prepare($sql);

$stmt->bind_param(

"isssssss",

$idPsicologo,

$titulo,

$descripcion,

$nombreImagen,

$nombrePdf,

$enlace,

$categoria,

$estado

);

if($stmt->execute()){

    echo json_encode([

        "ok"=>true,

        "mensaje"=>"Aviso publicado correctamente."

    ]);

}else{

    echo json_encode([

        "ok"=>false,

        "mensaje"=>"No fue posible guardar el aviso."

    ]);

}

$stmt->close();

$conn->close();

?>