// BOTÓN VER MÁS (funciona por cada bloque)
function toggleTexto(btn) {
    const box = btn.closest(".tutorias-texto");
    const texto = box.querySelector(".completo");

    if (texto.style.display === "block") {
        texto.style.display = "none";
        btn.textContent = "Ver más";
    } else {
        texto.style.display = "block";
        btn.textContent = "Ver menos";
    }
}

// ANIMACIÓN AL SCROLL (todos los bloques)
window.addEventListener("scroll", function () {
    const boxes = document.querySelectorAll(".tutorias-box");

    boxes.forEach(box => {
        const posicion = box.getBoundingClientRect().top;
        const alturaPantalla = window.innerHeight;

        if (posicion < alturaPantalla - 100) {
            box.classList.add("visible");
        }
    });
});

// ANIMACIÓN PARA TARJETAS DE RIESGOS
window.addEventListener("scroll", function () {
    const cards = document.querySelectorAll(".riesgo-card");

    cards.forEach(card => {
        const posicion = card.getBoundingClientRect().top;
        const alturaPantalla = window.innerHeight;

        if (posicion < alturaPantalla - 100) {
            card.classList.add("visible");
        }
    });
});

// 🔥 MOSTRAR LO QUE YA ESTÁ EN PANTALLA AL CARGAR
window.addEventListener("load", function () {
    const boxes = document.querySelectorAll(".tutorias-box");

    boxes.forEach(box => {
        const posicion = box.getBoundingClientRect().top;
        const alturaPantalla = window.innerHeight;

        if (posicion < alturaPantalla) {
            box.classList.add("visible");
        }
    });

    const cards = document.querySelectorAll(".riesgo-card");

    cards.forEach(card => {
        const posicion = card.getBoundingClientRect().top;
        const alturaPantalla = window.innerHeight;

        if (posicion < alturaPantalla) {
            card.classList.add("visible");
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {

    console.log("VOZ TUTORIAS LISTA");

    const esEdge = navigator.userAgent.includes("Edg");

    // 🔥 CONTENEDORES GRANDES
    const contenedores = document.querySelectorAll(".tutorias-box, .riesgo-card");

    contenedores.forEach(contenedor => {

        let timeout;
        let hablando = false;

        contenedor.addEventListener("mouseenter", () => {

            timeout = setTimeout(() => {

                if (hablando) return;

                // 🔥 Buscar título dentro del contenedor
                const tituloElemento =
                    contenedor.querySelector("h2") ||
                    contenedor.querySelector("h3");

                if (!tituloElemento) return;

                const texto = tituloElemento.textContent.trim();

                const voz = new SpeechSynthesisUtterance(texto);

                // 🔥 Edge libre, otros con español
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

                // 🔥 ANIMAR SOLO EL TÍTULO
                tituloElemento.classList.add("hablando");

                speechSynthesis.cancel();
                speechSynthesis.speak(voz);

                console.log("Hablando:", texto);

                voz.onend = () => {
                    hablando = false;
                    tituloElemento.classList.remove("hablando");
                };

            }, 500);

        });

        contenedor.addEventListener("mouseleave", () => {
            clearTimeout(timeout);
            speechSynthesis.cancel();
            hablando = false;

            const tituloElemento =
                contenedor.querySelector("h2") ||
                contenedor.querySelector("h3");

            if (tituloElemento) {
                tituloElemento.classList.remove("hablando");
            }
        });

    });

});