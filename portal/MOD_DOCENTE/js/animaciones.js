/*==================================================
=            INICIO DOCENTE
==================================================*/

document.addEventListener("DOMContentLoaded", function(){

    obtenerSaludo();
    actualizarFecha();
    actualizarHora();

    setInterval(actualizarHora,1000);

    iniciarAnimaciones();

    iniciarFrases();

});

/*==================================================
=            SALUDO
==================================================*/

function obtenerSaludo(){

    const hora = new Date().getHours();

    let mensaje = "";

    if(hora >= 5 && hora < 12){

        mensaje = "☀️ Buenos días";

    }
    else if(hora >= 12 && hora < 19){

        mensaje = "🌤️ Buenas tardes";

    }
    else{

        mensaje = "🌙 Buenas noches";

    }

    const saludo = document.getElementById("saludo");

    if(saludo){

        saludo.innerHTML = mensaje;

    }

}

/*==================================================
=            FECHA
==================================================*/

function actualizarFecha(){

    const fecha = new Date();

    const opciones = {

        weekday:'long',
        year:'numeric',
        month:'long',
        day:'numeric'

    };

    const textoFecha =
    fecha.toLocaleDateString('es-MX',opciones);

    const elemento = document.getElementById("fecha");

    if(elemento){

        elemento.innerHTML =
        "📅 " +
        textoFecha.charAt(0).toUpperCase() +
        textoFecha.slice(1);

    }

}

/*==================================================
=            HORA
==================================================*/

function actualizarHora(){

    const fecha = new Date();

    const hora =
    fecha.toLocaleTimeString('es-MX');

    const elemento =
    document.getElementById("hora");

    if(elemento){

        elemento.innerHTML =
        "⏰ " + hora;

    }

}

/*==================================================
=            FRASES DOCENTE
==================================================*/

const frases = [

"👨‍🏫 Enseñar es dejar una huella positiva para toda la vida.",

"📘 La educación es el puente entre el conocimiento y el futuro.",

"🎓 Un docente inspira, guía y transforma vidas.",

"🌟 Cada estudiante exitoso tuvo un docente que creyó en él.",

"🏆 La enseñanza es la profesión que crea todas las demás.",

"📚 Educar no es llenar un recipiente, es encender una llama.",

"🚀 Un gran docente motiva a alcanzar nuevas metas académicas."

];

let indiceFrase = 0;

function iniciarFrases(){

    const contenedor =
    document.querySelector(".frase p");

    if(!contenedor) return;

    setInterval(function(){

        contenedor.style.opacity = "0";

        setTimeout(function(){

            indiceFrase++;

            if(indiceFrase >= frases.length){

                indiceFrase = 0;

            }

            contenedor.innerHTML =
            frases[indiceFrase];

            contenedor.style.opacity = "1";

        },400);

    },7000);

}

/*==================================================
=            ANIMACIONES
==================================================*/

function iniciarAnimaciones(){

    const elementos = document.querySelectorAll(

        ".hero, .bienvenida-principal, .avisos-section, .frase"

    );

    const observer = new IntersectionObserver(

        function(entries){

            entries.forEach(function(entry){

                if(entry.isIntersecting){

                    entry.target.classList.add("visible");

                }

            });

        },

        {

            threshold:0.15

        }

    );

    elementos.forEach(function(elemento){

        elemento.classList.add("oculto");

        observer.observe(elemento);

    });

}

/*==================================================
=            EFECTO TARJETAS
==================================================*/

document.addEventListener("mouseover",function(e){

    const tarjeta =
    e.target.closest(".aviso-card");

    if(!tarjeta) return;

    tarjeta.style.transform =
    "translateY(-8px)";

});

document.addEventListener("mouseout",function(e){

    const tarjeta =
    e.target.closest(".aviso-card");

    if(!tarjeta) return;

    tarjeta.style.transform =
    "translateY(0px)";

});