/*=========================================================
        DEPARTAMENTO DE PSICOLOGÍA
        ANIMACIONES
=========================================================*/

document.addEventListener("DOMContentLoaded", function(){


    /*=====================================================
                    ANIMACIÓN DE AVISOS
    =====================================================*/

    const avisos = document.querySelectorAll(
        ".aviso-publico-card"
    );


    avisos.forEach(function(aviso, index){

        aviso.style.animationDelay =
            (index * 0.12) + "s";

    });



    /*=====================================================
                    SCROLL SUAVE
    =====================================================*/

    const enlaces = document.querySelectorAll(
        'a[href^="#"]'
    );


    enlaces.forEach(function(enlace){

        enlace.addEventListener("click", function(e){

            const destino =
                document.querySelector(
                    this.getAttribute("href")
                );


            if(destino){

                e.preventDefault();

                destino.scrollIntoView({

                    behavior:"smooth",

                    block:"start"

                });

            }

        });

    });



    /*=====================================================
                    OBSERVER
            ANIMAR SECCIONES AL APARECER
    =====================================================*/

    const elementos = document.querySelectorAll(
        ".espacio-seguro, .seccion-avisos, .seccion-agendar, .frase"
    );


    const observer = new IntersectionObserver(

        function(entries){

            entries.forEach(function(entry){

                if(entry.isIntersecting){

                    entry.target.classList.add(
                        "seccion-visible"
                    );

                }

            });

        },

        {

            threshold:0.12

        }

    );


    elementos.forEach(function(elemento){

        observer.observe(elemento);

    });



    /*=====================================================
                    IMÁGENES DE AVISOS
            EFECTO SUAVE AL CARGAR
    =====================================================*/

    const imagenes =
        document.querySelectorAll(
            ".aviso-publico-imagen img"
        );


    imagenes.forEach(function(imagen){

        imagen.addEventListener(
            "load",
            function(){

                imagen.classList.add(
                    "imagen-cargada"
                );

            }
        );

    });



    /*=====================================================
                    BOTÓN AGENDAR
    =====================================================*/

    const botonAgendar =
        document.querySelector(".btn-agendar");


    if(botonAgendar){

        botonAgendar.addEventListener(
            "mouseenter",
            function(){

                const flecha =
                    this.querySelector(".flecha");

                if(flecha){

                    flecha.style.transform =
                        "translateX(5px)";

                }

            }
        );


        botonAgendar.addEventListener(
            "mouseleave",
            function(){

                const flecha =
                    this.querySelector(".flecha");

                if(flecha){

                    flecha.style.transform =
                        "translateX(0)";

                }

            }
        );

    }


});