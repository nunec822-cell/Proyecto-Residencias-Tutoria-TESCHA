<?php
/*=========================================================
    EDITAR AVISO
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
    OBTENER ID DEL AVISO
=========================================================*/

$id_aviso = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;


if ($id_aviso <= 0) {

    header("Location: inicio.php");

    exit();

}


/*=========================================================
    OBTENER PSICÓLOGO
=========================================================*/

$id_usuario = $_SESSION["id_usuario"];


$sqlPsicologo = "

    SELECT id_registro

    FROM administradores_psicologos

    WHERE id_usuario = ?

    LIMIT 1

";


$stmtPsicologo = $conn->prepare($sqlPsicologo);


if (!$stmtPsicologo) {

    die("Error al preparar consulta del psicólogo.");

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

    header("Location: inicio.php");

    exit();

}


$id_psicologo =
    (int)$psicologo["id_registro"];


/*=========================================================
    OBTENER AVISO
=========================================================*/

$sqlAviso = "

    SELECT *

    FROM avisos_psicologia

    WHERE id_aviso = ?

    AND id_psicologo = ?

    LIMIT 1

";


$stmtAviso = $conn->prepare($sqlAviso);


if (!$stmtAviso) {

    die("Error al preparar consulta del aviso.");

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


/*=========================================================
    VALIDAR QUE EL AVISO EXISTA
=========================================================*/

if (!$aviso) {

    header("Location: inicio.php");

    exit();

}


/*=========================================================
    DATOS DEL AVISO
=========================================================*/

$titulo =
    $aviso["titulo"];

$descripcion =
    $aviso["descripcion"];

$categoria =
    $aviso["categoria"];

$estado =
    $aviso["estado"];

$enlace =
    $aviso["enlace"];

$imagen_actual =
    $aviso["imagen"];

$pdf_actual =
    $aviso["pdf"];

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>

Editar Aviso | Psicología

</title>


<link
    rel="stylesheet"
    href="css/estilo.css">


<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<style>

/*=========================================================
        CONTENEDOR EDITAR
=========================================================*/

.contenedor-editar{

    max-width:900px;

    margin:40px auto;

    padding:0 25px;

}


/*=========================================================
        TARJETA
=========================================================*/

.editar-card{

    background:#ffffff;

    border-radius:25px;

    padding:35px;

    box-shadow:
        0 15px 45px rgba(0,0,0,.08);

}


/*=========================================================
        ENCABEZADO
=========================================================*/

.editar-header{

    display:flex;

    align-items:center;

    gap:18px;

    margin-bottom:30px;

}


.editar-icono{

    width:60px;

    height:60px;

    border-radius:18px;

    display:flex;

    align-items:center;

    justify-content:center;

    background:linear-gradient(
        135deg,
        #7c5cff,
        #9b7cff
    );

    color:white;

    font-size:25px;

}


.editar-header h1{

    margin:0;

    font-size:28px;

    color:#243047;

}


.editar-header p{

    margin:5px 0 0;

    color:#777;

}


/*=========================================================
        GRUPOS
=========================================================*/

.editar-form .grupo{

    margin-bottom:22px;

}


.editar-form label{

    display:block;

    margin-bottom:8px;

    font-weight:600;

    color:#344054;

}


.editar-form label span{

    color:#e05252;

}


.editar-form input,
.editar-form textarea,
.editar-form select{

    width:100%;

    box-sizing:border-box;

    padding:13px 15px;

    border:1px solid #dfe3eb;

    border-radius:12px;

    font-family:inherit;

    font-size:15px;

    outline:none;

    transition:.25s;

}


.editar-form textarea{

    resize:vertical;

    min-height:150px;

}


.editar-form input:focus,
.editar-form textarea:focus,
.editar-form select:focus{

    border-color:#7c5cff;

    box-shadow:
        0 0 0 3px rgba(124,92,255,.10);

}


/*=========================================================
        FILAS
=========================================================*/

.editar-form .fila{

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:20px;

}


/*=========================================================
        ARCHIVO ACTUAL
=========================================================*/

.archivo-actual{

    margin-top:10px;

    padding:12px 15px;

    border-radius:12px;

    background:#f7f8fc;

    color:#667085;

    font-size:14px;

}


.archivo-actual i{

    margin-right:7px;

}


/*=========================================================
        IMAGEN ACTUAL
=========================================================*/

.imagen-actual{

    margin-top:12px;

}


.imagen-actual img{

    width:100%;

    max-height:230px;

    object-fit:cover;

    border-radius:15px;

    display:block;

}


/*=========================================================
        VISTA PREVIA
=========================================================*/

.preview-editar{

    margin-top:15px;

    border-radius:15px;

    overflow:hidden;

    background:#f5f6fa;

}


.preview-editar img{

    width:100%;

    max-height:260px;

    object-fit:cover;

    display:block;

}


/*=========================================================
        BOTONES
=========================================================*/

.botones-editar{

    display:flex;

    justify-content:flex-end;

    gap:12px;

    margin-top:30px;

    padding-top:25px;

    border-top:1px solid #eeeeee;

}


.btn-regresar,
.btn-guardar{

    border:none;

    border-radius:12px;

    padding:13px 22px;

    font-weight:600;

    text-decoration:none;

    cursor:pointer;

    display:inline-flex;

    align-items:center;

    gap:8px;

    transition:.25s;

}


.btn-regresar{

    background:#f1f3f7;

    color:#475467;

}


.btn-regresar:hover{

    background:#e4e7ec;

}


.btn-guardar{

    background:linear-gradient(
        135deg,
        #7c5cff,
        #9b7cff
    );

    color:white;

}


.btn-guardar:hover{

    transform:translateY(-2px);

    box-shadow:
        0 8px 20px rgba(124,92,255,.25);

}


/*=========================================================
        RESPONSIVE
=========================================================*/

@media(max-width:700px){

    .editar-card{

        padding:22px;

    }

    .editar-form .fila{

        grid-template-columns:1fr;

    }

    .botones-editar{

        flex-direction:column;

    }

    .btn-regresar,
    .btn-guardar{

        justify-content:center;

        width:100%;

        box-sizing:border-box;

    }

}

</style>

</head>


<body>


<?php include("../includes_pit/sidebar_psicologos.php"); ?>


<div class="separador"></div>


<div class="contenedor-principal">


<div class="contenedor-editar">


<div class="editar-card">


<!--=====================================================
                    ENCABEZADO
======================================================-->

<div class="editar-header">

<div class="editar-icono">

<i class="fa-solid fa-pen-to-square"></i>

</div>


<div>

<h1>

Editar Aviso

</h1>

<p>

Modifica la información de tu publicación.

</p>

</div>

</div>


<!--=====================================================
                    FORMULARIO
======================================================-->

<form

class="editar-form"

action="actualizar_aviso.php"

method="POST"

enctype="multipart/form-data"

autocomplete="off">


<!-- ID -->

<input

type="hidden"

name="id_aviso"

value="<?php echo $id_aviso; ?>">


<!--=====================================================
                    TÍTULO
======================================================-->

<div class="grupo">

<label>

Título <span>*</span>

</label>


<input

type="text"

name="titulo"

maxlength="200"

value="<?php echo htmlspecialchars($titulo); ?>"

required>

</div>


<!--=====================================================
                    DESCRIPCIÓN
======================================================-->

<div class="grupo">

<label>

Descripción <span>*</span>

</label>


<textarea

name="descripcion"

required><?php

echo htmlspecialchars($descripcion);

?></textarea>

</div>


<!--=====================================================
                    CATEGORÍA / ESTADO
======================================================-->

<div class="fila">


<div class="grupo">

<label>

Categoría

</label>


<select name="categoria">


<option
value="AVISO"
<?php
echo $categoria === "AVISO"
    ? "selected"
    : "";
?>>

Aviso

</option>


<option
value="TALLER"
<?php
echo $categoria === "TALLER"
    ? "selected"
    : "";
?>>

Taller

</option>


<option
value="CURSO"
<?php
echo $categoria === "CURSO"
    ? "selected"
    : "";
?>>

Curso

</option>


<option
value="CONFERENCIA"
<?php
echo $categoria === "CONFERENCIA"
    ? "selected"
    : "";
?>>

Conferencia

</option>


<option
value="CAMPAÑA"
<?php
echo $categoria === "CAMPAÑA"
    ? "selected"
    : "";
?>>

Campaña

</option>


<option
value="ACTIVIDAD"
<?php
echo $categoria === "ACTIVIDAD"
    ? "selected"
    : "";
?>>

Actividad

</option>


</select>

</div>


<div class="grupo">

<label>

Estado

</label>


<select name="estado">


<option
value="PUBLICADO"
<?php
echo $estado === "PUBLICADO"
    ? "selected"
    : "";
?>>

Publicado

</option>


<option
value="BORRADOR"
<?php
echo $estado === "BORRADOR"
    ? "selected"
    : "";
?>>

Borrador

</option>


<option
value="OCULTO"
<?php
echo $estado === "OCULTO"
    ? "selected"
    : "";
?>>

Oculto

</option>


</select>

</div>


</div>


<!--=====================================================
                    IMAGEN
======================================================-->

<div class="grupo">

<label>

Reemplazar imagen

</label>


<input

type="file"

id="nuevaImagen"

name="imagen"

accept="image/png,image/jpeg,image/jpg,image/webp">


<?php if (!empty($imagen_actual)) { ?>


<div class="archivo-actual">

<i class="fa-solid fa-image"></i>

Imagen actual:
<strong>

<?php
echo htmlspecialchars($imagen_actual);
?>

</strong>

</div>


<div class="imagen-actual">

<img

src="uploads/imagenes/<?php

echo htmlspecialchars($imagen_actual);

?>"

alt="Imagen actual">

</div>


<?php } ?>


<div
class="preview-editar"
id="contenedorPreview"
style="display:none;">


<img

id="previewEditar"

src=""

alt="Nueva imagen">

</div>


</div>


<!--=====================================================
                    PDF
======================================================-->

<div class="grupo">

<label>

Reemplazar PDF

</label>


<input

type="file"

name="pdf"

accept=".pdf,application/pdf">


<?php if (!empty($pdf_actual)) { ?>


<div class="archivo-actual">

<i class="fa-solid fa-file-pdf"></i>

PDF actual:
<strong>

<?php
echo htmlspecialchars($pdf_actual);
?>

</strong>

</div>


<?php } ?>


</div>


<!--=====================================================
                    ENLACE
======================================================-->

<div class="grupo">

<label>

Enlace (Opcional)

</label>


<input

type="url"

name="enlace"

value="<?php

echo htmlspecialchars($enlace ?? "");

?>"

placeholder="https://">


</div>


<!--=====================================================
                    BOTONES
======================================================-->

<div class="botones-editar">


<a

href="inicio.php"

class="btn-regresar">

<i class="fa-solid fa-arrow-left"></i>

Cancelar

</a>


<button

type="submit"

class="btn-guardar">

<i class="fa-solid fa-floppy-disk"></i>

Guardar cambios

</button>


</div>


</form>


</div>

</div>

</div>


<!--=====================================================
                    PREVISUALIZACIÓN
======================================================-->

<script>

const nuevaImagen =
document.getElementById("nuevaImagen");

const contenedorPreview =
document.getElementById("contenedorPreview");

const previewEditar =
document.getElementById("previewEditar");


if(nuevaImagen){

    nuevaImagen.addEventListener("change", function(){

        const archivo =
            this.files[0];

        if(!archivo){

            contenedorPreview.style.display =
                "none";

            return;

        }


        const lector =
            new FileReader();


        lector.onload =
            function(e){

                previewEditar.src =
                    e.target.result;

                contenedorPreview.style.display =
                    "block";

            };


        lector.readAsDataURL(archivo);

    });

}

</script>


</body>

</html>