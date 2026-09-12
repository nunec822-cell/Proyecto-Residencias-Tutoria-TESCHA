<?php

require_once("../../sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {

    header(
        "Location: ../../indexloguin.php"
    );

    exit();

}

$id_tutorado =
$_GET['id'] ?? 0;

if($id_tutorado == 0){

    die(
        "Alumno no válido."
    );

}

include("../../includes_pit/sidebar_admin.php");

?>

<div class="separador"></div>
<div class="separador"></div>
<div class="separador"></div>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
>
<link
rel="stylesheet"
href="canalizar_anexo14.css"
>

<div class="contenedor-canalizacion">

    <div class="encabezado">

        <h1>
            Canalización Anexo 14
        </h1>

        <p>

            Revise la información
            del alumno y determine
            si requiere seguimiento
            por parte del tutor.

        </p>

    </div>

    <div
        id="contenedor-detalle"
        class="card-detalle"
    >

        <div class="cargando">

            Cargando información
            del alumno...

        </div>

    </div>

</div>

<script>

const idTutorado =
<?= $id_tutorado; ?>;

</script>

<script
src="canalizar_anexo14.js">
</script>

<?php
include(
"../../../includes/footer.php"
);
?>