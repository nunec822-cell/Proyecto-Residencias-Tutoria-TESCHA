/*==========================================================
        ANIMACIONES PORTAL DEL TUTORADO
==========================================================*/

document.addEventListener("DOMContentLoaded", () => {

    saludo();

    actualizarFecha();

    actualizarHora();

    setInterval(actualizarHora,1000);

    animacionHero();

    animacionTarjetas();

    efectoScroll();

});

/*==========================================================
                SALUDO
==========================================================*/

function saludo(){

    const ahora=new Date();

    const hora=ahora.getHours();

    let mensaje="";

    if(hora>=6 && hora<12){

        mensaje="☀ Buenos días";

    }

    else if(hora>=12 && hora<19){

        mensaje="🌤 Buenas tardes";

    }

    else{

        mensaje="🌙 Buenas noches";

    }

    escribirTexto("saludo",mensaje);

}

/*==========================================================
            EFECTO ESCRITURA
==========================================================*/

function escribirTexto(id,texto){

    let i=0;

    const elemento=document.getElementById(id);

    elemento.innerHTML="";

    const intervalo=setInterval(()=>{

        if(i<texto.length){

            elemento.innerHTML+=texto.charAt(i);

            i++;

        }

        else{

            clearInterval(intervalo);

        }

    },45);

}

/*==========================================================
                FECHA
==========================================================*/

function actualizarFecha(){

    const opciones={

        weekday:'long',

        year:'numeric',

        month:'long',

        day:'numeric'

    };

    const fecha=new Date();

    const texto=fecha.toLocaleDateString("es-MX",opciones);

    document.getElementById("fecha").innerHTML="📅 "+texto;

}

/*==========================================================
                HORA
==========================================================*/

function actualizarHora(){

    const ahora=new Date();

    let h=ahora.getHours().toString().padStart(2,"0");

    let m=ahora.getMinutes().toString().padStart(2,"0");

    let s=ahora.getSeconds().toString().padStart(2,"0");

    document.getElementById("hora").innerHTML="🕒 "+h+":"+m+":"+s;

}

/*==========================================================
        HERO
==========================================================*/

function animacionHero(){

    const hero=document.querySelector(".hero");

    hero.style.opacity="0";

    hero.style.transform="translateY(-35px)";

    setTimeout(()=>{

        hero.style.transition="all .9s ease";

        hero.style.opacity="1";

        hero.style.transform="translateY(0px)";

    },250);

}

/*==========================================================
        TARJETAS
==========================================================*/

function animacionTarjetas(){

    const tarjetas=document.querySelectorAll(

        ".tip-box,.bienvenida-grid div,.aviso-card"

    );

    tarjetas.forEach((card,index)=>{

        card.style.opacity="0";

        card.style.transform="translateY(45px)";

        setTimeout(()=>{

            card.style.transition="all .7s ease";

            card.style.opacity="1";

            card.style.transform="translateY(0px)";

        },150*index);

    });

}

/*==========================================================
        SCROLL
==========================================================*/

function efectoScroll(){

    const elementos=document.querySelectorAll(

        ".tips-estudiante,.bienvenida-card,.aviso-card,.frase"

    );

    const observer=new IntersectionObserver((entries)=>{

        entries.forEach(entry=>{

            if(entry.isIntersecting){

                entry.target.style.opacity="1";

                entry.target.style.transform="translateY(0px)";

            }

        });

    },{

        threshold:.15

    });

    elementos.forEach(el=>{

        el.style.opacity="0";

        el.style.transform="translateY(50px)";

        el.style.transition="all .8s ease";

        observer.observe(el);

    });

}

/*==========================================================
        EFECTO HOVER
==========================================================*/

document.addEventListener("mouseover",(e)=>{

    if(e.target.classList.contains("tip-box")){

        e.target.style.transform="translateY(-10px) scale(1.02)";

    }

});

document.addEventListener("mouseout",(e)=>{

    if(e.target.classList.contains("tip-box")){

        e.target.style.transform="translateY(0px)";

    }

});

/*==========================================================
        AVISOS
==========================================================*/

const avisos=document.querySelectorAll(".aviso-card");

avisos.forEach(aviso=>{

    aviso.addEventListener("mouseenter",()=>{

        aviso.style.transition=".35s";

        aviso.style.boxShadow="0 18px 40px rgba(0,0,0,.18)";

    });

    aviso.addEventListener("mouseleave",()=>{

        aviso.style.boxShadow="0 10px 25px rgba(0,0,0,.08)";

    });

});

/*==========================================================
        FRASE
==========================================================*/

const frases=[

"📘 La educación es el camino hacia un mejor futuro.",

"🎯 Cada día es una oportunidad para aprender algo nuevo.",

"🌱 El esfuerzo de hoy será el éxito de mañana.",

"🏆 La constancia supera al talento cuando el talento no es constante.",

"🎓 Tu formación profesional comienza con pequeños logros diarios."

];

const frase=document.querySelector(".frase p");

if(frase){

    let indice=0;

    setInterval(()=>{

        frase.style.opacity="0";

        setTimeout(()=>{

            indice++;

            if(indice>=frases.length){

                indice=0;

            }

            frase.innerHTML=frases[indice];

            frase.style.opacity="1";

        },400);

    },8000);

}