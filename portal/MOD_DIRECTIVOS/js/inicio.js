
/*=========================================================
    INICIO DIRECTIVOS
    SIST V.4.0 - PIT V.4.0
=========================================================*/

document.addEventListener("DOMContentLoaded", function () {

    /*=====================================================
        TARJETAS DE AVISOS
    =====================================================*/

    const tarjetas = document.querySelectorAll(".aviso-card");


    /*=====================================================
        ANIMACIÓN ESCALONADA
    =====================================================*/

    if (tarjetas.length > 0) {

        tarjetas.forEach(function (tarjeta, index) {

            tarjeta.style.animationDelay =
                (index * 0.10) + "s";

        });

    }


    /*=====================================================
        EFECTO DE INTERACCIÓN
    =====================================================*/

    tarjetas.forEach(function (tarjeta) {

        tarjeta.addEventListener("mouseenter", function () {

            tarjeta.classList.add("aviso-hover");

        });


        tarjeta.addEventListener("mouseleave", function () {

            tarjeta.classList.remove("aviso-hover");

        });

    });


    /*=====================================================
        EVITAR DOBLE CLIC EN ENLACES
    =====================================================*/

    const enlacesAviso =
        document.querySelectorAll(".btn-aviso");


    enlacesAviso.forEach(function (enlace) {

        enlace.addEventListener("click", function () {

            enlace.classList.add("btn-cargando");

            setTimeout(function () {

                enlace.classList.remove("btn-cargando");

            }, 700);

        });

    });


    /*=====================================================
        ANIMACIÓN DEL CONTADOR
    =====================================================*/

    const contador =
        document.querySelector(".contador-numero");


    if (contador) {

        const valorFinal =
            parseInt(contador.textContent.trim(), 10);


        if (!isNaN(valorFinal)) {

            let valorActual = 0;

            const duracion = 500;

            const intervalo = 30;

            const incremento =
                valorFinal /
                (duracion / intervalo);


            contador.textContent = "0";


            const animacion =
                setInterval(function () {

                    valorActual += incremento;


                    if (valorActual >= valorFinal) {

                        valorActual = valorFinal;

                        clearInterval(animacion);

                    }


                    contador.textContent =
                        Math.floor(valorActual);

                }, intervalo);

        }

    }


    /*=====================================================
        ANIMACIÓN DEL RESUMEN
    =====================================================*/

    const resumen =
        document.querySelectorAll(".resumen-item");


    if (resumen.length > 0) {

        resumen.forEach(function (elemento, index) {

            elemento.style.opacity = "0";

            elemento.style.transform =
                "translateY(12px)";


            elemento.style.transition =
                "opacity 0.45s ease, transform 0.45s ease";


            setTimeout(function () {

                elemento.style.opacity = "1";

                elemento.style.transform =
                    "translateY(0)";

            }, 250 + (index * 100));

        });

    }


    /*=====================================================
        SEGURIDAD DE IMÁGENES
    =====================================================*/

    const imagenes =
        document.querySelectorAll(".aviso-imagen img");


    imagenes.forEach(function (imagen) {

        imagen.addEventListener("error", function () {

            imagen.style.display = "none";

            const contenedor =
                imagen.closest(".aviso-imagen");


            if (contenedor) {

                contenedor.classList.add(
                    "imagen-no-disponible"
                );

            }

        });

    });


    /*=====================================================
        MENSAJE EN CONSOLA
    =====================================================*/

    console.log(
        "Inicio Directivos PIT V4.0 cargado correctamente."
    );

});
