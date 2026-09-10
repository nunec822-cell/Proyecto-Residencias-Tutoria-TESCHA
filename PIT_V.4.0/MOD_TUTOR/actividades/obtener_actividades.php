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

$id_usuario =
$_SESSION['id_usuario'];

$sqlTutor =
mysqli_query(
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

$tutor =
mysqli_fetch_assoc(
    $sqlTutor
);

$id_tutor =
$tutor['id_personal'];

/*=========================================
GRUPO
=========================================*/

$grupo =
mysqli_real_escape_string(
    $conn,
    $_GET['grupo'] ?? ""
);

if(
    empty($grupo)
){

    echo "

    <div class='mensaje-vacio'>

        Seleccione un grupo.

    </div>

    ";

    exit();

}

/*=========================================
ACTIVIDADES
=========================================*/

$sql =
mysqli_query(
    $conn,
    "
    SELECT *

    FROM actividades_tutor

    WHERE

        id_tutor='$id_tutor'

        AND

        grupo='$grupo'

    ORDER BY

        fecha_publicacion DESC

    "
);

if(
    mysqli_num_rows($sql)==0
){

    echo "

    <div class='mensaje-vacio'>

        No existen actividades
        publicadas para este grupo.

    </div>

    ";

    exit();

}

while(
    $fila =
    mysqli_fetch_assoc($sql)
):

?>

<div class="card-actividad">

    <!--=====================================
        CABECERA
    =====================================-->

    <div class="cabecera-actividad">

        <div class="cabecera-info">

            <h2>

                <i class="fa-solid fa-book-open"></i>

                <?= strtoupper(
                    htmlspecialchars(
                        $fila['titulo']
                    )
                ); ?>

            </h2>

            <div class="badges">

                <span class="badge badge-grupo">

                    <i class="fa-solid fa-users"></i>

                    Grupo <?= $fila['grupo']; ?>

                </span>

                <span class="badge badge-fecha">

                    <i class="fa-regular fa-calendar"></i>

                    <?= date(
                        "d/m/Y H:i",
                        strtotime(
                            $fila['fecha_publicacion']
                        )
                    ); ?>

                </span>

            </div>

        </div>

        <button
            class="btn-eliminar"
            data-id="<?= $fila['id_actividad']; ?>"
        >

            <i class="fa-solid fa-trash"></i>

            Eliminar

        </button>

    </div>

    <!--=====================================
        DESCRIPCIÓN
    =====================================-->

    <div class="bloque">

        <h3>

            <i class="fa-solid fa-align-left"></i>

            Descripción

        </h3>

        <div class="descripcion-actividad">

            <?= nl2br(
                htmlspecialchars(
                    $fila['descripcion']
                )
            ); ?>

        </div>

    </div>

    <!--=====================================
        RECURSOS
    =====================================-->

    <div class="bloque">

        <h3>

            <i class="fa-solid fa-paperclip"></i>

            Recursos de la actividad

        </h3>

        <div class="contenedor-recursos">

            <?php if(!empty($fila['pdf'])): ?>

                <div class="recurso">

                    <i class="fa-solid fa-file-pdf icono-pdf"></i>

                    <div>

                        <strong>

                            Documento PDF

                        </strong>

                        <a
                            href="uploads/pdf/<?= $fila['pdf']; ?>"
                            target="_blank"
                        >

                            Descargar documento

                        </a>

                    </div>

                </div>

            <?php endif; ?>

            <?php if(!empty($fila['link'])): ?>

                <div class="recurso">

                    <i class="fa-solid fa-link icono-link"></i>

                    <div>

                        <strong>

                            Enlace de apoyo

                        </strong>

                        <a
                            href="<?= htmlspecialchars($fila['link']); ?>"
                            target="_blank"
                        >

                            Abrir enlace

                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <!--=====================================
        IMÁGENES
    =====================================-->

    <?php

    $imagenes = [

        $fila['imagen1'],
        $fila['imagen2'],
        $fila['imagen3']

    ];

    $hayImagen = false;

    foreach(
        $imagenes
        as
        $img
    ){

        if(
            !empty($img)
        ){

            $hayImagen = true;

            break;

        }

    }

    ?>

    <?php if($hayImagen): ?>

        <div class="bloque">

            <h3>

                <i class="fa-solid fa-image"></i>

                Imágenes de apoyo

            </h3>

            <div class="galeria">

                <?php

                foreach(
                    $imagenes
                    as
                    $img
                ):

                    if(
                        empty($img)
                    ){

                        continue;

                    }

                ?>

                    <img

                        src="uploads/imagenes/<?= $img; ?>"

                        alt="Imagen"

                    >

                <?php endforeach; ?>

            </div>

        </div>

    <?php endif; ?>

    <!--=====================================
        ESTADO
    =====================================-->

    <div class="estado-actividad">

        <i class="fa-solid fa-circle-check"></i>

        Actividad publicada correctamente y disponible para los tutorados del grupo
        <strong><?= $fila['grupo']; ?></strong>.

    </div>

</div>

<?php endwhile; ?>