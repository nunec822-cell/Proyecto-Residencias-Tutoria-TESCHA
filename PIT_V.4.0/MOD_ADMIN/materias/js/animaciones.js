document.addEventListener("DOMContentLoaded", () => {

    const tarjetas = document.querySelectorAll(
        ".tarjeta-formulario,.tabla-contenedor"
    );

    tarjetas.forEach((tarjeta, index) => {

        tarjeta.style.opacity = "0";
        tarjeta.style.transform = "translateY(20px)";

        setTimeout(() => {

            tarjeta.style.transition = "all .5s ease";

            tarjeta.style.opacity = "1";
            tarjeta.style.transform = "translateY(0px)";

        }, index * 200);

    });

});