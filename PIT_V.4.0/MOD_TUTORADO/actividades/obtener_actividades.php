<?php

require_once("../../sesion.php");

if(
    !in_array(
        "TUTORADO",
        $_SESSION["roles"]
    )
){
    exit();
}

include("../../base_pit/conect_pit.php");

/*=========================================
OBTENER TUTORADO LOGUEADO
=========================================*/

$id_usuario = $_SESSION['id_usuario'];

$sqlTutorado = mysqli_query(
    $conn,
    "
    SELECT
        id_tutorado,
        grupo
    FROM tutorados
    WHERE id_usuario='$id_usuario'
    LIMIT 1
    "
);

if(mysqli_num_rows($sqlTutorado)==0){

    echo "
    <div class='mensaje-vacio'>
        No fue posible identificar al tutorado.
    </div>
    ";

    exit();

}

$tutorado = mysqli_fetch_assoc($sqlTutorado);

$id_tutorado = $tutorado['id_tutorado'];

$grupo = $tutorado['grupo'];

/*=========================================
ACTIVIDADES
=========================================*/

$sql = mysqli_query(
    $conn,
    "
    SELECT *

    FROM actividades_tutor

    WHERE grupo='$grupo'

    ORDER BY fecha_publicacion DESC
    "
);

if(mysqli_num_rows($sql)==0){

    echo "
    <div class='mensaje-vacio'>
        Tu tutor aún no ha publicado actividades para tu grupo.
    </div>
    ";

    exit();

}

while($fila = mysqli_fetch_assoc($sql)):

/*=========================================
BUSCAR SI YA ENTREGÓ
=========================================*/

$consulta = mysqli_query(
    $conn,
    "
    SELECT *

    FROM evidencias_actividades

    WHERE

        id_actividad='".$fila['id_actividad']."'

        AND

        id_tutorado='$id_tutorado'

    LIMIT 1
    "
);

$evidencia = mysqli_fetch_assoc($consulta);

?>

<div class="card-actividad">

    <!--==============================
    CABECERA
    ==============================-->

    <div class="cabecera-actividad">

        <div class="cabecera-info">

            <h2>

                <i class="fa-solid fa-book"></i>

                <?= strtoupper(
                    htmlspecialchars(
                        $fila['titulo']
                    )
                ); ?>

            </h2>

            <div class="badges">

                <span class="badge">

                    <i class="fa-solid fa-users"></i>

                    Grupo <?= $fila['grupo']; ?>

                </span>

                <span class="badge">

                    <i class="fa-solid fa-calendar"></i>

                    <?= $fila['fecha_publicacion']; ?>

                </span>

            </div>

        </div>

    </div>

    <!--==============================
    DESCRIPCIÓN
    ==============================-->

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

    <!--==============================
    RECURSOS
    ==============================-->

    <?php

    $mostrarRecursos = false;

    if(
        !empty($fila['pdf']) ||
        !empty($fila['link'])
    ){

        $mostrarRecursos = true;

    }

    ?>

    <?php if($mostrarRecursos): ?>

    <div class="bloque">

        <h3>

            <i class="fa-solid fa-folder-open"></i>

            Material de apoyo

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
                            href="../../MOD_TUTOR/actividades/uploads/pdf/<?= $fila['pdf']; ?>"
                            target="_blank"
                        >

                            Ver documento

                        </a>

                    </div>

                </div>

            <?php endif; ?>

            <?php if(!empty($fila['link'])): ?>

                <div class="recurso">

                    <i class="fa-solid fa-link icono-link"></i>

                    <div>

                        <strong>

                            Enlace

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

    <?php endif; ?>

    <!--==============================
    IMÁGENES
    ==============================-->

    <?php

    $imagenes = [

        $fila['imagen1'],
        $fila['imagen2'],
        $fila['imagen3']

    ];

    $hayImagen = false;

    foreach($imagenes as $img){

        if(!empty($img)){

            $hayImagen = true;

            break;

        }

    }

    ?>

    <?php if($hayImagen): ?>

    <div class="bloque">

        <h3>

            <i class="fa-solid fa-images"></i>

            Imágenes

        </h3>

        <div class="galeria">

            <?php

            foreach($imagenes as $img):

                if(empty($img)){

                    continue;

                }

            ?>

            <img
                src="../../MOD_TUTOR/actividades/uploads/imagenes/<?= $img; ?>"
                alt="Imagen"
            >

            <?php endforeach; ?>

        </div>

    </div>

    <?php endif; ?>

    <!--==============================
    MI EVIDENCIA
    ==============================-->

    <div class="bloque">

        <h3>

            <i class="fa-solid fa-upload"></i>

            Mi evidencia

        </h3>

        <?php if($evidencia): ?>

            <div class="mensaje-ok">

                <i class="fa-solid fa-circle-check"></i>

                Ya enviaste una evidencia.

                <br><br>

                <a
                    href="uploads/evidencias/<?= $evidencia['pdf']; ?>"
                    target="_blank"
                >

                    Ver mi PDF

                </a>

            </div>

        <?php endif; ?>

        <form
            class="form-evidencia"
            enctype="multipart/form-data"
        >

            <input
                type="hidden"
                name="id_actividad"
                value="<?= $fila['id_actividad']; ?>"
            >

            <input
                type="file"
                name="pdf"
                accept=".pdf"
                required
            >

            <small>

                Formato PDF.
                Máximo 2 MB.

            </small>

            <button
                type="submit"
                class="btn-subir"
            >

                <i class="fa-solid fa-upload"></i>

                <?= $evidencia
                    ? "Reemplazar evidencia"
                    : "Subir evidencia"; ?>

            </button>

        </form>

    </div>

</div>

<?php endwhile; ?>