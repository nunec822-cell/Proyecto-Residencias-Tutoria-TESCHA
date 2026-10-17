/*=========================================================
    INICIO JEFE DE CARRERA
=========================================================*/

document.addEventListener("DOMContentLoaded", function () {

    const bienvenida = document.querySelector(".bienvenida-jefe");

    if (!bienvenida) {
        return;
    }


    /*=====================================================
        EFECTO SUAVE AL PASAR EL MOUSE
    =====================================================*/

    bienvenida.addEventListener("mousemove", function (e) {

        const rect = bienvenida.getBoundingClientRect();

        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centroX = rect.width / 2;
        const centroY = rect.height / 2;

        const movimientoX = (x - centroX) / 70;
        const movimientoY = (y - centroY) / 70;

        const ilustracion = document.querySelector(
            ".ilustracion-jefe"
        );

        if (ilustracion) {

            ilustracion.style.transform =
                `translate(${movimientoX}px, ${movimientoY}px)`;

        }

    });


    /*=====================================================
        RESTAURAR POSICIÓN
    =====================================================*/

    bienvenida.addEventListener("mouseleave", function () {

        const ilustracion = document.querySelector(
            ".ilustracion-jefe"
        );

        if (ilustracion) {

            ilustracion.style.transform = "";

        }

    });


    /*=====================================================
        ANIMACIÓN DE ENTRADA
    =====================================================*/

    const elementos = [
        ".etiqueta-bienvenida",
        ".texto-bienvenida h1",
        ".texto-bienvenida h2",
        ".texto-bienvenida p",
        ".mensaje-institucional"
    ];

    elementos.forEach(function (selector, index) {

        const elemento = document.querySelector(selector);

        if (!elemento) {
            return;
        }

        elemento.style.opacity = "0";
        elemento.style.transform = "translateY(15px)";

        setTimeout(function () {

            elemento.style.transition =
                "all 0.6s ease";

            elemento.style.opacity = "1";
            elemento.style.transform = "translateY(0)";

        }, 150 + (index * 120));

    });

});