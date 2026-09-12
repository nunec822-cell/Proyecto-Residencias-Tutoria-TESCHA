<?php 

require_once("../PIT_V.4.0/sesion.php");

if (!in_array("ADMIN", $_SESSION["roles"])) {
    header("Location: ../PIT_V.4.0/indexloguin.php");
    exit();
}
include("../base_sist/conect_sist.php");
include("../PIT_V.4.0/includes_pit/sidebar_admin.php");

// 🔥 OBTENER ID
$id = $_GET['id'];

// 🔥 CONSULTA
$sql = "SELECT * FROM inicio_contenidos WHERE id='$id'";
$resultado = mysqli_query($conexion, $sql);

$fila = mysqli_fetch_assoc($resultado);

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="css/estilo.css">
<div class="separador"></div>
<div class="separador"></div>
<div class="contenedor-admin">

    <h1>Editar Contenido Inicio</h1>

    <!-- 🔥 LAYOUT -->
    <div class="layout-editor">

        <!-- 🔥 IZQUIERDA -->
        <div class="editor-panel">

            <!-- 🔥 FORMULARIO -->
            <form 
                action="guardar_inicio.php" 
                method="POST" 
                enctype="multipart/form-data"
                class="form-admin"
            >

                <input 
                    type="hidden" 
                    name="id" 
                    value="<?php echo $fila['id']; ?>"
                >

                <!-- 🔥 BIENVENIDA -->
                <div class="grupo-form">

                    <label>Título Bienvenida</label>

                    <input 
                        type="text"
                        id="titulo_bienvenida"
                        name="titulo_bienvenida"
                        value="<?php echo $fila['titulo_bienvenida']; ?>"
                    >

                </div>

                <div class="grupo-form">

                    <label>Texto Bienvenida</label>

                    <textarea 
                        id="texto_bienvenida"
                        name="texto_bienvenida"
                        rows="5"
                    ><?php echo $fila['texto_bienvenida']; ?></textarea>

                </div>

                <!-- 🔥 PERSONAJES -->
                <div class="grupo-form">

                    <label>Imagen Bienvenida Izquierda</label>

                    <input 
                        type="file"
                        name="personaje_izq"
                    >

                    <input 
                        type="hidden"
                        name="personaje_izq_actual"
                        value="<?php echo $fila['personaje_izq']; ?>"
                    >

                    <p>
                        Actual:
                        <?php echo $fila['personaje_izq']; ?>
                    </p>

                </div>

                <div class="grupo-form">

                    <label>Imagen Bienvenida Derecha</label>

                    <input 
                        type="file"
                        name="personaje_der"
                    >

                    <input 
                        type="hidden"
                        name="personaje_der_actual"
                        value="<?php echo $fila['personaje_der']; ?>"
                    >

                    <p>
                        Actual:
                        <?php echo $fila['personaje_der']; ?>
                    </p>

                </div>

                <!-- 🔥 SLIDER -->
                <hr>

                <h2 class="titulo-galeria-admin">
                    Imágenes Slider
                </h2>

                <!-- SLIDER 1 -->
                <div class="grupo-form">

                    <label>Slider 1</label>

                    <input 
                        type="file"
                        name="slider1"
                    >

                    <input 
                        type="hidden"
                        name="slider1_actual"
                        value="<?php echo $fila['slider1']; ?>"
                    >

                    <p>
                        Actual:
                        <?php echo $fila['slider1']; ?>
                    </p>

                </div>

                <!-- SLIDER 2 -->
                <div class="grupo-form">

                    <label>Slider 2</label>

                    <input 
                        type="file"
                        name="slider2"
                    >

                    <input 
                        type="hidden"
                        name="slider2_actual"
                        value="<?php echo $fila['slider2']; ?>"
                    >

                    <p>
                        Actual:
                        <?php echo $fila['slider2']; ?>
                    </p>

                </div>

                <!-- SLIDER 3 -->
                <div class="grupo-form">

                    <label>Slider 3</label>

                    <input 
                        type="file"
                        name="slider3"
                    >

                    <input 
                        type="hidden"
                        name="slider3_actual"
                        value="<?php echo $fila['slider3']; ?>"
                    >

                    <p>
                        Actual:
                        <?php echo $fila['slider3']; ?>
                    </p>

                </div>

                <!-- SLIDER 4 -->
                <div class="grupo-form">

                    <label>Slider 4</label>

                    <input 
                        type="file"
                        name="slider4"
                    >

                    <input 
                        type="hidden"
                        name="slider4_actual"
                        value="<?php echo $fila['slider4']; ?>"
                    >

                    <p>
                        Actual:
                        <?php echo $fila['slider4']; ?>
                    </p>

                </div>

                <!-- 🔥 SECCION 1 -->
                <hr>

                <div class="grupo-form">

                    <label>Título Sección 1</label>

                    <input 
                        type="text"
                        id="titulo_seccion1"
                        name="titulo_seccion1"
                        value="<?php echo $fila['titulo_seccion1']; ?>"
                    >

                </div>

                <div class="grupo-form">

                    <label>Texto Sección 1</label>

                    <textarea 
                        id="texto_seccion1"
                        name="texto_seccion1"
                        rows="5"
                    ><?php echo $fila['texto_seccion1']; ?></textarea>

                </div>

                <div class="grupo-form">

                    <label>Imagen Sección 1</label>

                    <input 
                        type="file"
                        name="imagen_seccion1"
                    >

                    <input 
                        type="hidden"
                        name="imagen_seccion1_actual"
                        value="<?php echo $fila['imagen_seccion1']; ?>"
                    >

                    <p>
                        Actual:
                        <?php echo $fila['imagen_seccion1']; ?>
                    </p>

                </div>

                <!-- 🔥 SECCION 2 -->
                <hr>

                <div class="grupo-form">

                    <label>Título Sección 2</label>

                    <input 
                        type="text"
                        id="titulo_seccion2"
                        name="titulo_seccion2"
                        value="<?php echo $fila['titulo_seccion2']; ?>"
                    >

                </div>

                <div class="grupo-form">

                    <label>Texto Resumen Sección 2</label>

                    <textarea 
                        id="texto_seccion2"
                        name="texto_seccion2"
                        rows="5"
                    ><?php echo $fila['texto_seccion2']; ?></textarea>

                </div>

                <div class="grupo-form">

                    <label>Texto Completo Sección 2</label>

                    <textarea 
                        id="texto_completo_seccion2"
                        name="texto_completo_seccion2"
                        rows="8"
                    ><?php echo $fila['texto_completo_seccion2']; ?></textarea>

                </div>

                <div class="grupo-form">

                    <label>Imagen Sección 2</label>

                    <input 
                        type="file"
                        name="imagen_seccion2"
                    >

                    <input 
                        type="hidden"
                        name="imagen_seccion2_actual"
                        value="<?php echo $fila['imagen_seccion2']; ?>"
                    >

                    <p>
                        Actual:
                        <?php echo $fila['imagen_seccion2']; ?>
                    </p>

                </div>

                <!-- 🔥 BOTÓN -->
                <button type="submit" class="btn-guardar">
                    Guardar Cambios
                </button>

            </form>

            <!-- 🔥 GALERÍA -->
            <hr>

            <h2 class="titulo-galeria-admin">
                Administrar Galería
            </h2>

            <!-- 🔥 SUBIR -->
            <form 
                action="subir_galeria.php" 
                method="POST"
                enctype="multipart/form-data"
                class="form-galeria"
            >

                <input 
                    type="file" 
                    name="imagen" 
                    required
                >

                <button 
                    type="submit" 
                    class="btn-subir"
                >
                    Subir Imagen
                </button>

            </form>

            <!-- 🔥 MOSTRAR IMÁGENES -->
            <div class="galeria-admin">

                <?php

                $sqlGaleria = "
                SELECT * 
                FROM galeria_inicio 
                ORDER BY id DESC
                ";

                $resGaleria = mysqli_query(
                    $conexion, 
                    $sqlGaleria
                );

                while($img = mysqli_fetch_assoc($resGaleria)){

                ?>

                    <div class="card-imagen">

                        <img 
                            src="../assets/<?php echo $img['imagen']; ?>"
                        >

                        <a 
                            href="eliminar_imagen.php?id=<?php echo $img['id']; ?>"
                            class="btn-eliminar"
                        >
                            Eliminar
                        </a>

                    </div>

                <?php } ?>

            </div>

        </div>

        <!-- 🔥 DERECHA -->
        <div class="preview-panel">

            <div class="preview-header">
                Vista real del sitio
            </div>

            <iframe 
                src="../index.php"
                id="previewFrame"
            ></iframe>

        </div>

    </div>

</div>

<script src="js/admin.js"></script>

<?php include("../includes/footer.php"); ?>