/*=========================================================
        PANEL PSICÓLOGOS
        TESCHA
=========================================================*/

document.addEventListener("DOMContentLoaded", function () {

    iniciarSaludo();

    actualizarFechaHora();

    setInterval(actualizarFechaHora,1000);

    animarEntrada();

    iniciarModal();

    iniciarPreviewImagen();

});


/*=========================================================
        SALUDO
=========================================================*/

function iniciarSaludo(){

    const saludo=document.getElementById("saludo");

    if(!saludo) return;

    const hora=new Date().getHours();

    let mensaje="";
    let icono="";

    if(hora>=6 && hora<12){

        mensaje="Buenos días";
        icono="🌞";

    }
    else if(hora>=12 && hora<19){

        mensaje="Buenas tardes";
        icono="🌤️";

    }
    else{

        mensaje="Buenas noches";
        icono="🌙";

    }

    saludo.innerHTML=mensaje+" "+icono;

}


/*=========================================================
        FECHA Y HORA
=========================================================*/

function actualizarFechaHora(){

    const fechaElemento=document.getElementById("fecha");

    const horaElemento=document.getElementById("hora");

    if(!fechaElemento || !horaElemento) return;

    const ahora=new Date();

    const fecha=ahora.toLocaleDateString("es-MX",{

        weekday:"long",
        year:"numeric",
        month:"long",
        day:"numeric"

    });

    const hora=ahora.toLocaleTimeString("es-MX",{

        hour:"2-digit",
        minute:"2-digit",
        second:"2-digit"

    });

    fechaElemento.innerHTML="📅 "+fecha.charAt(0).toUpperCase()+fecha.slice(1);

    horaElemento.innerHTML="🕒 "+hora;

}


/*=========================================================
        ANIMACIONES
=========================================================*/

function animarEntrada(){

    const elementos=[

        ".hero",

        ".gestion-avisos",

        ".informacion-panel",

        ".frase"

    ];

    elementos.forEach(function(selector,index){

        const elemento=document.querySelector(selector);

        if(!elemento) return;

        elemento.style.opacity="0";

        elemento.style.transform="translateY(40px)";

        setTimeout(function(){

            elemento.style.transition="all .8s ease";

            elemento.style.opacity="1";

            elemento.style.transform="translateY(0px)";

        },200+(index*250));

    });

}


/*=========================================================
        MODAL
=========================================================*/

function iniciarModal(){

    const modal=document.getElementById("modalAviso");

    const abrir=document.getElementById("abrirModal");

    const cerrar=document.getElementById("cerrarModal");

    const cancelar=document.getElementById("cancelarModal");

    if(!modal) return;

    if(abrir){

        abrir.addEventListener("click",function(){

            modal.style.display="flex";

            document.body.style.overflow="hidden";

        });

    }

    function cerrarModal(){

        modal.style.display="none";

        document.body.style.overflow="auto";

    }

    if(cerrar){

        cerrar.addEventListener("click",cerrarModal);

    }

    if(cancelar){

        cancelar.addEventListener("click",cerrarModal);

    }

    window.addEventListener("click",function(e){

        if(e.target===modal){

            cerrarModal();

        }

    });

    document.addEventListener("keydown",function(e){

        if(e.key==="Escape"){

            cerrarModal();

        }

    });

}


/*=========================================================
        PREVIEW IMAGEN
=========================================================*/

function iniciarPreviewImagen(){

    const input=document.getElementById("imagen");

    if(!input) return;

    const preview=document.getElementById("preview");

    const texto=document.getElementById("textoPreview");

    input.addEventListener("change",function(e){

        const archivo=e.target.files[0];

        if(!archivo){

            if(preview){

                preview.style.display="none";

            }

            if(texto){

                texto.style.display="block";

            }

            return;

        }

        const lector=new FileReader();

        lector.onload=function(event){

            if(preview){

                preview.src=event.target.result;

                preview.style.display="block";

            }

            if(texto){

                texto.style.display="none";

            }

        };

        lector.readAsDataURL(archivo);

    });

}
/*=========================================================
        GUARDAR AVISO (AJAX)
=========================================================*/

const formulario = document.getElementById("formAviso");

if(formulario){

    formulario.addEventListener("submit", function(e){

        e.preventDefault();

        const boton = document.getElementById("btnGuardarAviso");
        const mensaje = document.getElementById("mensajeAviso");

        boton.disabled = true;
        boton.innerHTML =
        '<i class="fa-solid fa-spinner fa-spin"></i> Publicando...';

        const datos = new FormData(formulario);

        fetch("guardar_aviso.php",{

            method:"POST",

            body:datos

        })

        .then(res=>res.json())

        .then(respuesta=>{

            if(respuesta.ok){

                mensaje.innerHTML=
                "<div class='mensaje-exito'>"+
                respuesta.mensaje+
                "</div>";

                formulario.reset();

                const preview=document.getElementById("preview");
                const texto=document.getElementById("textoPreview");

                if(preview){

                    preview.style.display="none";

                }

                if(texto){

                    texto.style.display="block";

                }

                setTimeout(()=>{

                    document.getElementById("cancelarModal").click();

                    mensaje.innerHTML="";

                },1500);

            }else{

                mensaje.innerHTML=
                "<div class='mensaje-error'>"+
                respuesta.mensaje+
                "</div>";

            }

        })

        .catch(()=>{

            mensaje.innerHTML=
            "<div class='mensaje-error'>Ocurrió un error inesperado.</div>";

        })

        .finally(()=>{

            boton.disabled=false;

            boton.innerHTML=
            '<i class="fa-solid fa-paper-plane"></i> Publicar Aviso';

        });

    });

}
/*=========================================================
        CONFIRMAR ELIMINACIÓN
=========================================================*/

function confirmarEliminar(){

    return confirm(
        "¿Estás seguro de eliminar este aviso?\n\n" +
        "Esta acción no se puede deshacer."
    );

}