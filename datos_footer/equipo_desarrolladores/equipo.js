/*=========================================================
    EQUIPO DE DESARROLLADORES
    SIST V.4.0 - PIT V.4.0
=========================================================*/

document.addEventListener("DOMContentLoaded", () => {


    /*=====================================================
        ELEMENTOS
    =====================================================*/

    const cards = document.querySelectorAll(".person-card");

    const glows = document.querySelectorAll(".glow");

    const projectSection =
        document.querySelector(".project-section");


    /*=====================================================
        APARICIÓN DE TARJETAS
    =====================================================*/

    const observerOptions = {
        threshold: 0.15
    };


    const observer =
        new IntersectionObserver(
            (entries, observer) => {

                entries.forEach(entry => {

                    if (entry.isIntersecting) {

                        entry.target.classList.add(
                            "card-visible"
                        );

                        observer.unobserve(
                            entry.target
                        );
                    }

                });

            },
            observerOptions
        );


    cards.forEach(card => {

        card.style.opacity = "0";

        card.style.transform =
            "translateY(40px)";

        card.style.transition =
            "opacity .8s ease, transform .8s cubic-bezier(.2,.8,.2,1)";

        observer.observe(card);

    });


    /*=====================================================
        CLASE DE TARJETA VISIBLE
    =====================================================*/

    const cardVisibleStyle =
        document.createElement("style");

    cardVisibleStyle.innerHTML = `

        .person-card.card-visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

    `;

    document.head.appendChild(
        cardVisibleStyle
    );


    /*=====================================================
        MOVIMIENTO DEL MOUSE
    =====================================================*/

    document.addEventListener(
        "mousemove",
        (event) => {

            const x =
                (event.clientX /
                    window.innerWidth -
                    0.5) * 2;

            const y =
                (event.clientY /
                    window.innerHeight -
                    0.5) * 2;


            glows.forEach(
                (glow, index) => {

                    const intensity =
                        (index + 1) * 10;

                    glow.style.transform =
                        `translate(
                            ${x * intensity}px,
                            ${y * intensity}px
                        )`;

                }
            );

        }
    );


    /*=====================================================
        EFECTO 3D EN TARJETAS
    =====================================================*/

    cards.forEach(card => {

        card.addEventListener(
            "mousemove",
            (event) => {

                if (
                    window.innerWidth < 850
                ) {
                    return;
                }


                const rect =
                    card.getBoundingClientRect();


                const x =
                    event.clientX -
                    rect.left;


                const y =
                    event.clientY -
                    rect.top;


                const centerX =
                    rect.width / 2;


                const centerY =
                    rect.height / 2;


                const rotateX =
                    ((y - centerY) /
                        centerY) * -2;


                const rotateY =
                    ((x - centerX) /
                        centerX) * 2;


                card.style.transform =
                    `translateY(-10px)
                     perspective(1000px)
                     rotateX(${rotateX}deg)
                     rotateY(${rotateY}deg)`;

            }
        );


        card.addEventListener(
            "mouseleave",
            () => {

                card.style.transform =
                    "translateY(0)";

            }
        );

    });


    /*=====================================================
        EFECTO ORBITA
    =====================================================*/

    if (projectSection) {

        document.addEventListener(
            "mousemove",
            (event) => {

                const rect =
                    projectSection.getBoundingClientRect();


                if (
                    event.clientY >= rect.top &&
                    event.clientY <= rect.bottom
                ) {

                    const x =
                        (
                            event.clientX -
                            rect.left
                        ) / rect.width;


                    const y =
                        (
                            event.clientY -
                            rect.top
                        ) / rect.height;


                    const orbit =
                        projectSection.querySelector(
                            ".project-orbit"
                        );


                    if (orbit) {

                        orbit.style.transform =
                            `translate(
                                ${(x - .5) * 25}px,
                                ${(y - .5) * 25}px
                            )`;

                    }

                }

            }
        );

    }


    /*=====================================================
        SCROLL SUAVE
    =====================================================*/

    const scrollIndicator =
        document.querySelector(
            ".scroll-indicator"
        );


    if (scrollIndicator) {

        scrollIndicator.style.cursor =
            "pointer";


        scrollIndicator.addEventListener(
            "click",
            () => {

                const advisor =
                    document.querySelector(
                        ".advisor-section"
                    );


                if (advisor) {

                    advisor.scrollIntoView({
                        behavior: "smooth"
                    });

                }

            }
        );

    }


    /*=====================================================
        PREVENIR MOVIMIENTO 3D EN CELULARES
    =====================================================*/

    window.addEventListener(
        "resize",
        () => {

            if (
                window.innerWidth < 850
            ) {

                cards.forEach(card => {

                    card.style.transform =
                        "translateY(0)";

                });

            }

        }
    );


});