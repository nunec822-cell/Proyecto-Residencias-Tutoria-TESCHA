<?php
/*=========================================================
    ACTUALIZAR AVISO
    MOD_PSICOLOGOS
    SIST V.4.0 - PIT V.4.0
=========================================================*/


/*=========================================================
    SESIÓN
=========================================================*/

require_once("../sesion.php");


/*=========================================================
    VALIDAR ROL
=========================================================*/

if (!in_array("PSICOLOGO", $_SESSION["roles"])) {

    header("Location: ../indexloguin.php");
    exit();

}


/*=========================================================
    CONEXIÓN
=========================================================*/

require_once("../base_pit/conect_pit.php");


/*=========================================================
    VALIDAR MÉTODO
=========================================================*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: inicio.php");
    exit();

}


/*=========================================================
    RECIBIR ID
=========================================================*/

$id_aviso = isset($_POST["id_aviso"])
    ? (int)$_POST["id_aviso"]
    : 0;


if ($id_aviso <= 0) {

    header("Location: inicio.php");
    exit();

}


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

if ($titulo === "" || $descripcion === "") {

    die("El título y la descripción son obligatorios.");

}


/*=========================================================
    CATEGORÍAS PERMITIDAS
=========================================================*/

$categoriasPermitidas = [

    "AVISO",
    "TALLER",
    "CURSO",
    "CONFERENCIA",
    "CAMPAÑA",
    "ACTIVIDAD"

];


if (!in_array($categoria, $categoriasPermitidas)) {

    die("La categoría seleccionada no es válida.");

}


/*=========================================================
    ESTADOS PERMITIDOS
=========================================================*/

$estadosPermitidos = [

    "PUBLICADO",
    "BORRADOR",
    "OCULTO"

];


if (!in_array($estado, $estadosPermitidos)) {

    die("El estado seleccionado no es válido.");

}


/*=========================================================
    VALIDAR ENLACE
=========================================================*/

if ($enlace !== "") {

    if (!filter_var($enlace, FILTER_VALIDATE_URL)) {

        die("El enlace proporcionado no es válido.");

    }

}


/*=========================================================
    OBTENER PSICÓLOGO
=========================================================*/

$id_usuario = $_SESSION["id_usuario"];


$sqlPsicologo = "

    SELECT id_registro

    FROM administradores_psicologos

    WHERE id_usuario = ?

    AND activo = 1

    LIMIT 1

";


$stmtPsicologo =
    $conn->prepare($sqlPsicologo);


if (!$stmtPsicologo) {

    die("Error al preparar la consulta del psicólogo.");

}


$stmtPsicologo->bind_param(
    "i",
    $id_usuario
);


$stmtPsicologo->execute();


$resultadoPsicologo =
    $stmtPsicologo->get_result();


$psicologo =
    $resultadoPsicologo->fetch_assoc();


if (!$psicologo) {

    die("No se encontró el perfil del psicólogo.");

}


$id_psicologo =
    (int)$psicologo["id_registro"];


/*=========================================================
    OBTENER AVISO ACTUAL
=========================================================*/

$sqlAviso = "

    SELECT
        imagen,
        pdf

    FROM avisos_psicologia

    WHERE id_aviso = ?

    AND id_psicologo = ?

    LIMIT 1

";


$stmtAviso =
    $conn->prepare($sqlAviso);


if (!$stmtAviso) {

    die("Error al preparar la consulta del aviso.");

}


$stmtAviso->bind_param(
    "ii",
    $id_aviso,
    $id_psicologo
);


$stmtAviso->execute();


$resultadoAviso =
    $stmtAviso->get_result();


$aviso =
    $resultadoAviso->fetch_assoc();


if (!$aviso) {

    die("El aviso no existe o no pertenece a este psicólogo.");

}


/*=========================================================
    ARCHIVOS ACTUALES
=========================================================*/

$imagenActual =
    $aviso["imagen"];

$pdfActual =
    $aviso["pdf"];


/*=========================================================
    RUTAS
=========================================================*/

$directorioImagenes =
    __DIR__ . "/uploads/imagenes/";


$directorioPDF =
    __DIR__ . "/uploads/imagenes/pdf/";


/*=========================================================
    CREAR DIRECTORIOS SI NO EXISTEN
=========================================================*/

if (!is_dir($directorioImagenes)) {

    mkdir(
        $directorioImagenes,
        0755,
        true
    );

}


if (!is_dir($directorioPDF)) {

    mkdir(
        $directorioPDF,
        0755,
        true
    );

}


/*=========================================================
    VARIABLES PARA NUEVOS ARCHIVOS
=========================================================*/

$nuevaImagen =
    $imagenActual;

$nuevoPDF =
    $pdfActual;


/*=========================================================
    PROCESAR IMAGEN
=========================================================*/

if (
    isset($_FILES["imagen"]) &&
    $_FILES["imagen"]["error"] !== UPLOAD_ERR_NO_FILE
) {


    if (
        $_FILES["imagen"]["error"] !== UPLOAD_ERR_OK
    ) {

        die("Ocurrió un error al subir la imagen.");

    }


    /*---------------------------------------------
        TAMAÑO MÁXIMO
    ---------------------------------------------*/

    $maxImagen =
        5 * 1024 * 1024;


    if (
        $_FILES["imagen"]["size"] > $maxImagen
    ) {

        die(
            "La imagen no puede superar los 5 MB."
        );

    }


    /*---------------------------------------------
        EXTENSIONES
    ---------------------------------------------*/

    $extensionesImagen = [

        "jpg",
        "jpeg",
        "png",
        "webp"

    ];


    $nombreOriginal =
        $_FILES["imagen"]["name"];


    $extension =
        strtolower(
            pathinfo(
                $nombreOriginal,
                PATHINFO_EXTENSION
            )
        );


    if (
        !in_array(
            $extension,
            $extensionesImagen
        )
    ) {

        die(
            "El formato de imagen no está permitido."
        );

    }


    /*---------------------------------------------
        VALIDAR MIME REAL
    ---------------------------------------------*/

    $tiposPermitidos = [

        "image/jpeg",
        "image/png",
        "image/webp"

    ];


    $finfo =
        finfo_open(FILEINFO_MIME_TYPE);


    $mime =
        finfo_file(
            $finfo,
            $_FILES["imagen"]["tmp_name"]
        );


    finfo_close($finfo);


    if (
        !in_array(
            $mime,
            $tiposPermitidos
        )
    ) {

        die(
            "El archivo seleccionado no es una imagen válida."
        );

    }


    /*---------------------------------------------
        GENERAR NOMBRE NUEVO
    ---------------------------------------------*/

    $nuevoNombreImagen =
        "IMG_" .
        bin2hex(random_bytes(8)) .
        "." .
        $extension;


    $rutaNuevaImagen =
        $directorioImagenes .
        $nuevoNombreImagen;


    /*---------------------------------------------
        MOVER IMAGEN
    ---------------------------------------------*/

    if (
        !move_uploaded_file(
            $_FILES["imagen"]["tmp_name"],
            $rutaNuevaImagen
        )
    ) {

        die(
            "No fue posible guardar la nueva imagen."
        );

    }


    /*---------------------------------------------
        ELIMINAR IMAGEN ANTERIOR
    ---------------------------------------------*/

    if (
        !empty($imagenActual)
    ) {

        $rutaImagenAnterior =
            $directorioImagenes .
            $imagenActual;


        if (
            file_exists(
                $rutaImagenAnterior
            )
        ) {

            unlink(
                $rutaImagenAnterior
            );

        }

    }


    $nuevaImagen =
        $nuevoNombreImagen;

}


/*=========================================================
    PROCESAR PDF
=========================================================*/

if (
    isset($_FILES["pdf"]) &&
    $_FILES["pdf"]["error"] !== UPLOAD_ERR_NO_FILE
) {


    if (
        $_FILES["pdf"]["error"] !== UPLOAD_ERR_OK
    ) {

        die(
            "Ocurrió un error al subir el PDF."
        );

    }


    /*---------------------------------------------
        TAMAÑO MÁXIMO
    ---------------------------------------------*/

    $maxPDF =
        10 * 1024 * 1024;


    if (
        $_FILES["pdf"]["size"] > $maxPDF
    ) {

        die(
            "El PDF no puede superar los 10 MB."
        );

    }


    /*---------------------------------------------
        EXTENSIÓN
    ---------------------------------------------*/

    $nombreOriginalPDF =
        $_FILES["pdf"]["name"];


    $extensionPDF =
        strtolower(
            pathinfo(
                $nombreOriginalPDF,
                PATHINFO_EXTENSION
            )
        );


    if (
        $extensionPDF !== "pdf"
    ) {

        die(
            "El archivo seleccionado debe ser un PDF."
        );

    }


    /*---------------------------------------------
        VALIDAR MIME
    ---------------------------------------------*/

    $finfoPDF =
        finfo_open(FILEINFO_MIME_TYPE);


    $mimePDF =
        finfo_file(
            $finfoPDF,
            $_FILES["pdf"]["tmp_name"]
        );


    finfo_close($finfoPDF);


    if (
        $mimePDF !== "application/pdf"
    ) {

        die(
            "El archivo seleccionado no es un PDF válido."
        );

    }


    /*---------------------------------------------
        GENERAR NOMBRE
    ---------------------------------------------*/

    $nuevoNombrePDF =
        "PDF_" .
        bin2hex(random_bytes(8)) .
        ".pdf";


    $rutaNuevoPDF =
        $directorioPDF .
        $nuevoNombrePDF;


    /*---------------------------------------------
        MOVER PDF
    ---------------------------------------------*/

    if (
        !move_uploaded_file(
            $_FILES["pdf"]["tmp_name"],
            $rutaNuevoPDF
        )
    ) {

        die(
            "No fue posible guardar el nuevo PDF."
        );

    }


    /*---------------------------------------------
        ELIMINAR PDF ANTERIOR
    ---------------------------------------------*/

    if (
        !empty($pdfActual)
    ) {

        $rutaPDFAnterior =
            $directorioPDF .
            $pdfActual;


        if (
            file_exists(
                $rutaPDFAnterior
            )
        ) {

            unlink(
                $rutaPDFAnterior
            );

        }

    }


    $nuevoPDF =
        $nuevoNombrePDF;

}


/*=========================================================
    ACTUALIZAR BASE DE DATOS
=========================================================*/

$sqlActualizar = "

    UPDATE avisos_psicologia

    SET

        titulo = ?,

        descripcion = ?,

        imagen = ?,

        pdf = ?,

        enlace = ?,

        categoria = ?,

        estado = ?

    WHERE id_aviso = ?

    AND id_psicologo = ?

";


$stmtActualizar =
    $conn->prepare($sqlActualizar);


if (!$stmtActualizar) {

    die(
        "Error al preparar la actualización."
    );

}


$stmtActualizar->bind_param(

    "sssssssii",

    $titulo,

    $descripcion,

    $nuevaImagen,

    $nuevoPDF,

    $enlace,

    $categoria,

    $estado,

    $id_aviso,

    $id_psicologo

);


/*=========================================================
    EJECUTAR
=========================================================*/

if (
    !$stmtActualizar->execute()
) {

    die(
        "No fue posible actualizar el aviso."
    );

}


/*=========================================================
    CERRAR
=========================================================*/

$stmtActualizar->close();

$stmtAviso->close();

$stmtPsicologo->close();


/*=========================================================
    REGRESAR AL INICIO
=========================================================*/

header(
    "Location: inicio.php"
);

exit();

?>