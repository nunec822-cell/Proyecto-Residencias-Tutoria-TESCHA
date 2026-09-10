// 🔥 SLIDER AUTOMÁTICO
document.addEventListener("DOMContentLoaded", function () {

    let slides = document.querySelectorAll('.banner img');
    let index = 0;

    setInterval(() => {
        slides[index].classList.remove('active');
        index = (index + 1) % slides.length;
        slides[index].classList.add('active');
    }, 4000);


    // 🔥 ANIMACIÓN SCROLL
    const secciones = document.querySelectorAll('.seccion');

    function mostrarSecciones() {
        secciones.forEach(sec => {
            const top = sec.getBoundingClientRect().top;
            if (top < window.innerHeight - 100) {
                sec.classList.add('visible');
            }
        });
    }

    window.addEventListener('scroll', mostrarSecciones);
    mostrarSecciones();

});
document.addEventListener("DOMContentLoaded", () => {

    console.log("VOZ LISTA");

    const secciones = document.querySelectorAll(".seccion");

    // 🔍 Detectar si es Edge
    const esEdge = navigator.userAgent.includes("Edg");

    secciones.forEach(seccion => {

        let timeout;
        let hablando = false;

        const h3Original = seccion.querySelector("h3");
        const iconoOriginal = h3Original.querySelector(".audio");

        seccion.addEventListener("mouseenter", () => {

            timeout = setTimeout(() => {

                if (hablando) return;

                // 🔥 Clonar para limpiar texto (sin icono)
                const h3 = h3Original.cloneNode(true);
                const icono = h3.querySelector(".audio");
                if (icono) icono.remove();

                const titulo = h3.textContent.trim();

                const voz = new SpeechSynthesisUtterance(titulo);

                // 🔥 SOLO seleccionar voz si NO es Edge
                if (!esEdge) {
                    const voces = speechSynthesis.getVoices();

                    let vozSeleccionada =
                        voces.find(v => v.lang.startsWith("es")) ||
                        voces[0];

                    if (vozSeleccionada) {
                        voz.voice = vozSeleccionada;
                    }
                }

                voz.lang = "es-MX";
                voz.rate = 1;
                voz.pitch = 1;
                voz.volume = 1;

                hablando = true;

                // 🔥 ACTIVAR ANIMACIÓN
                h3Original.classList.add("hablando");
                if (iconoOriginal) iconoOriginal.classList.add("hablando");

                speechSynthesis.cancel();
                speechSynthesis.speak(voz);

                console.log("Hablando:", titulo);

                voz.onend = () => {
                    hablando = false;

                    // 🔥 QUITAR ANIMACIÓN
                    h3Original.classList.remove("hablando");
                    if (iconoOriginal) iconoOriginal.classList.remove("hablando");
                };

            }, 600); // delay UX

        });

        seccion.addEventListener("mouseleave", () => {
            clearTimeout(timeout);
            speechSynthesis.cancel();
            hablando = false;

            // 🔥 LIMPIAR ANIMACIÓN
            h3Original.classList.remove("hablando");
            if (iconoOriginal) iconoOriginal.classList.remove("hablando");
        });

    });

});

function toggleTexto(btn) {
    const contenedor = btn.closest(".texto");
    const texto = contenedor.querySelector(".completo");

    if (texto.style.display === "block") {
        texto.style.display = "none";
        btn.textContent = "Ver más";
    } else {
        texto.style.display = "block";
        btn.textContent = "Ver menos";
    }
}