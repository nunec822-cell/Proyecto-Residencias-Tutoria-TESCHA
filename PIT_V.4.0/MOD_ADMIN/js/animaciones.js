
/* =========================
   FECHA Y HORA EN TIEMPO REAL
========================= */

function actualizarFechaHora() {

    const ahora = new Date();

    // FECHA
    const opcionesFecha = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    const fecha = ahora.toLocaleDateString('es-MX', opcionesFecha);

    // HORA
    let horas = ahora.getHours();
    let minutos = ahora.getMinutes();
    let segundos = ahora.getSeconds();

    horas = horas < 10 ? "0" + horas : horas;
    minutos = minutos < 10 ? "0" + minutos : minutos;
    segundos = segundos < 10 ? "0" + segundos : segundos;

    const hora = horas + ":" + minutos + ":" + segundos;

    // Insertar en HTML
    const fechaEl = document.getElementById("fecha");
    const horaEl = document.getElementById("hora");

    if (fechaEl) fechaEl.innerText = "📅 " + fecha;
    if (horaEl) horaEl.innerText = "🕒 " + hora;
}

/* =========================
   SALUDO DINÁMICO
========================= */

function saludoDinamico() {

    const hora = new Date().getHours();
    let saludo = "";

    if (hora >= 6 && hora < 12) {
        saludo = "🌅 Buenos días";
    } 
    else if (hora >= 12 && hora < 19) {
        saludo = "☀️ Buenas tardes";
    } 
    else {
        saludo = "🌙 Buenas noches";
    }

    const saludoEl = document.getElementById("saludo");

    if (saludoEl) {
        saludoEl.innerText = saludo;
    }
}

/* =========================
   FRASES INSTITUCIONALES
========================= */

const frases = [
    "La educación transforma vidas.",
    "El conocimiento compartido fortalece la comunidad.",
    "La tutoría es clave para el éxito académico.",
    "Cada estudiante cuenta en nuestra institución.",
    "El compromiso académico construye el futuro.",
    "Formar personas es construir nación."
];

function fraseAleatoria() {

    const index = Math.floor(Math.random() * frases.length);

    const fraseEl = document.getElementById("frase-texto");

    if (fraseEl) {
        fraseEl.innerText = frases[index];
    }
}

/* =========================
   INICIALIZACIÓN
========================= */

document.addEventListener("DOMContentLoaded", function () {

    saludoDinamico();
    fraseAleatoria();
    actualizarFechaHora();

    // reloj en tiempo real
    setInterval(actualizarFechaHora, 1000);

});