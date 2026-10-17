const password =
document.getElementById("password");

const confirmarPassword =
document.getElementById("confirmar_password");

const fortaleza =
document.getElementById("fortaleza");

const mensajeCoincide =
document.getElementById("mensajeCoincide");

/*=========================================
FORTALEZA
=========================================*/

if(password){

password.addEventListener("keyup",function(){

    let valor = this.value;

    let mayuscula =
    /[A-Z]/.test(valor);

    let minuscula =
    /[a-z]/.test(valor);

    let numero =
    /[0-9]/.test(valor);

    let longitud =
    valor.length >= 8;

    let puntos = 0;

    if(mayuscula) puntos++;
    if(minuscula) puntos++;
    if(numero) puntos++;
    if(longitud) puntos++;

    fortaleza.classList.remove(
        "debil",
        "media",
        "fuerte"
    );

    if(valor.length === 0){

        fortaleza.innerHTML =
        "Fortaleza de la contraseña";

        return;
    }

    if(puntos <= 2){

        fortaleza.classList.add("debil");

        fortaleza.innerHTML =
        "█░░░░ Débil";
    }
    else if(puntos === 3){

        fortaleza.classList.add("media");

        fortaleza.innerHTML =
        "███░░ Media";
    }
    else{

        fortaleza.classList.add("fuerte");

        fortaleza.innerHTML =
        "█████ Fuerte";
    }

});

}

/*=========================================
CONFIRMAR PASSWORD
=========================================*/

function validarCoincidencia(){

    if(!password || !confirmarPassword)
    return;

    mensajeCoincide.classList.remove(
        "correcto",
        "incorrecto"
    );

    if(confirmarPassword.value === "")
    {
        mensajeCoincide.innerHTML = "";
        return;
    }

    if(
        password.value ===
        confirmarPassword.value
    ){

        mensajeCoincide.classList.add(
            "correcto"
        );

        mensajeCoincide.innerHTML =
        "✓ Las contraseñas coinciden";
    }
    else{

        mensajeCoincide.classList.add(
            "incorrecto"
        );

        mensajeCoincide.innerHTML =
        "✗ Las contraseñas no coinciden";
    }

}

if(password)
password.addEventListener(
    "keyup",
    validarCoincidencia
);

if(confirmarPassword)
confirmarPassword.addEventListener(
    "keyup",
    validarCoincidencia
);
/*=========================================
OCULTAR MENSAJES AUTOMÁTICAMENTE
=========================================*/

setTimeout(() => {

    const mensaje =
    document.querySelector(
        ".alerta-exito, .mensaje-error"
    );

    if(mensaje)
    {

        mensaje.style.transition =
        "all 0.3s ease";

        mensaje.style.opacity =
        "0";

        mensaje.style.transform =
        "translateY(-10px)";

        setTimeout(() => {

            mensaje.remove();

        },500);

    }

},4000);