document.addEventListener("DOMContentLoaded", () => {

    const galeria = document.querySelector(".galeria");
    const btnNext = document.getElementById("btn-next");
    const btnPrev = document.getElementById("btn-prev");

    if (!galeria) return;

    // 🔥 DUPLICAR PARA LOOP
    galeria.innerHTML += galeria.innerHTML;

    let auto = true;
    let velocidad = 1; // 🔥 MÁS RÁPIDO

    // 🔥 AUTO SCROLL SUAVE Y ESTABLE
    function autoScroll() {

        if (auto) {
            galeria.scrollLeft += velocidad;

            if (galeria.scrollLeft >= galeria.scrollWidth / 2) {
                galeria.scrollLeft = 0;
            }
        }

        requestAnimationFrame(autoScroll);
    }

    autoScroll();

    // 🔥 PAUSA AL PASAR MOUSE (SIN MOVER POSICIÓN)
    galeria.addEventListener("mouseenter", () => auto = false);
    galeria.addEventListener("mouseleave", () => auto = true);

    // 🔥 BOTÓN SIGUIENTE
    btnNext.addEventListener("click", () => {
        auto = false;

        galeria.scrollBy({
            left: 300,
            behavior: "smooth"
        });

        setTimeout(() => auto = true, 2000);
    });

    // 🔥 BOTÓN ANTERIOR
    btnPrev.addEventListener("click", () => {
        auto = false;

        galeria.scrollBy({
            left: -300,
            behavior: "smooth"
        });

        setTimeout(() => auto = true, 2000);
    });

});