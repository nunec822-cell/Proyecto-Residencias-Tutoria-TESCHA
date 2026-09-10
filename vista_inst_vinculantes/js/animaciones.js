// 🔥 ANIMACIÓN SCROLL TARJETAS

const cards = document.querySelectorAll(".card-inst");

function mostrarCards() {

    cards.forEach(card => {

        const top = card.getBoundingClientRect().top;

        if (top < window.innerHeight - 100) {
            card.classList.add("visible");
        }

    });

}

window.addEventListener("scroll", mostrarCards);

mostrarCards();