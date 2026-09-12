
<?php

/* =====================================
   CONEXIÓN A BASE DE DATOS SIST
===================================== */

include __DIR__ . "/../base_sist/conect_sist.php";


/* =====================================
   OBTENER RUTA ACTUAL
===================================== */

$ruta_actual = $_SERVER['REQUEST_URI'];


/* =====================================
   OBTENER TEMA ACTUAL
===================================== */

$sqlTema = "
SELECT tema
FROM tema_sidebar
LIMIT 1
";

$resTema = mysqli_query(
    $conexion,
    $sqlTema
);

$filaTema = mysqli_fetch_assoc(
    $resTema
);

$temaActual = "normal";

if($filaTema){

    $temaActual = $filaTema['tema'];

}

?>


<!-- =====================================
     CSS DEL SIDEBAR
===================================== -->

<link
    rel="stylesheet"
    href="/SIST V.4.0/css/estilo_sidebar_sist.css"
>


<!-- =====================================
     SIDEBAR PRINCIPAL
===================================== -->

<div class="sidebar-container <?php echo $temaActual; ?>">


    <div class="menu-grid">



        <!-- =====================================
             INICIO
        ====================================== -->

        <a
            href="/SIST V.4.0/index.php"
            class="card <?php

            if(
                strpos($ruta_actual, '/index.php') !== false &&

                strpos($ruta_actual, 'vista_tutorias') === false &&

                strpos($ruta_actual, 'PIT_V.4.0') === false &&

                strpos($ruta_actual, 'vista_inst_vinculantes') === false &&

                strpos($ruta_actual, 'vista_psicologia') === false &&

                strpos($ruta_actual, 'vista_tutor/') === false

            ){

                echo 'active';

            }

            ?>"
        >

            <div class="titulo">
                INICIO
            </div>


            <img
                src="https://cdn-icons-png.flaticon.com/512/1946/1946436.png"
                alt="Inicio"
            >

        </a>



        <!-- =====================================
             TUTORÍAS
        ====================================== -->

        <a
            href="/SIST V.4.0/vista_tutorias/index.php"
            class="card <?php

            if(
                strpos($ruta_actual, 'vista_tutorias') !== false
            ){

                echo 'active';

            }

            ?>"
        >

            <div class="titulo">
                TUTORÍA Y SUS MODALIDADES
            </div>


            <img
                src="https://cdn-icons-png.flaticon.com/512/2995/2995433.png"
                alt="Tutorías"
            >

        </a>



        <!-- =====================================
             PIT
        ====================================== -->

        <a
            href="/SIST V.4.0/PIT_V.4.0/indexloguin.php"
            class="card <?php

            if(
                strpos($ruta_actual, 'PIT_V.4.0') !== false
            ){

                echo 'active';

            }

            ?>"
        >

            <div class="titulo">
                PROGRAMA INSTITUCIONAL DE TUTORIAS
            </div>


            <img
                src="https://cdn-icons-png.flaticon.com/512/1828/1828490.png"
                alt="Programa Institucional de Tutorías"
            >


            <div class="titulo">
                ACCEDER
            </div>

        </a>



        <!-- =====================================
             CONTACTA A TU TUTOR
        ====================================== -->

        <a
            href="/SIST V.4.0/vista_tutor/index.php"
            class="card <?php

            /*
                IMPORTANTE:

                Se utiliza "vista_tutor/"
                y NO solamente "vista_tutor"

                porque:

                vista_tutorias

                contiene también:

                vista_tutor

                Esto provocaba que ambas tarjetas
                se marcaran como activas.
            */

            if(
                strpos($ruta_actual, 'vista_tutor/') !== false
            ){

                echo 'active';

            }

            ?>"
        >

            <div class="titulo">
                CONTACTA A TU TUTOR
            </div>


            <img
                src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png"
                alt="Contacta a tu Tutor"
            >

        </a>



        <!-- =====================================
             PSICÓLOGO
        ====================================== -->

        <a
            href="/SIST V.4.0/vista_psicologia/index.php"
            class="card <?php

            if(
                strpos($ruta_actual, 'vista_psicologia') !== false
            ){

                echo 'active';

            }

            ?>"
        >

            <div class="titulo">
                CONTACTA A TU PSICÓLOGO
            </div>


            <img
                src="https://cdn-icons-png.flaticon.com/512/3774/3774299.png"
                alt="Psicólogo"
            >

        </a>



        <!-- =====================================
             INSTITUCIONES VINCULANTES
        ====================================== -->

        <a
            href="/SIST V.4.0/vista_inst_vinculantes/index.php"
            class="card <?php

            if(
                strpos($ruta_actual, 'vista_inst_vinculantes') !== false
            ){

                echo 'active';

            }

            ?>"
        >

            <div class="titulo">
                INSTITUCIONES VINCULANTES
            </div>


            <img
                src="https://cdn-icons-png.flaticon.com/512/3062/3062634.png"
                alt="Instituciones Vinculantes"
            >

        </a>


    </div>


</div>

