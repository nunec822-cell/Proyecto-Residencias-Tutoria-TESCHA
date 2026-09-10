/*=========================================================
    MODULO COORDINADOR
    SIST V.4.0 - PIT V.4.0
=========================================================*/

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /*=====================================================
            ANIMACIÓN DE TARJETAS
        =====================================================*/

        const tarjetas =
            document.querySelectorAll(
                ".estadistica-card"
            );


        tarjetas.forEach(
            function (tarjeta, indice) {

                tarjeta.style.opacity = "0";

                tarjeta.style.transform =
                    "translateY(12px)";


                setTimeout(
                    function () {

                        tarjeta.style.transition =
                            "opacity .4s ease, transform .4s ease";

                        tarjeta.style.opacity =
                            "1";

                        tarjeta.style.transform =
                            "translateY(0)";

                    },
                    indice * 80
                );

            }
        );


        /*=====================================================
            EFECTO EN FILAS DE TABLA
        =====================================================*/

        const filas =
            document.querySelectorAll(
                ".tabla tbody tr"
            );


        filas.forEach(
            function (fila) {

                fila.addEventListener(
                    "mouseenter",
                    function () {

                        fila.style.transform =
                            "translateX(2px)";

                    }
                );


                fila.addEventListener(
                    "mouseleave",
                    function () {

                        fila.style.transform =
                            "translateX(0)";

                    }
                );

            }
        );


        /*=====================================================
            EVITAR DOBLE CLIC EN BOTONES
        =====================================================*/

        const botones =
            document.querySelectorAll(
                ".btn, .btn-regresar"
            );


        botones.forEach(
            function (boton) {

                boton.addEventListener(
                    "click",
                    function () {

                        boton.classList.add(
                            "boton-presionado"
                        );

                    }
                );

            }
        );


    }
);