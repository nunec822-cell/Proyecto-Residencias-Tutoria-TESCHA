document.addEventListener("DOMContentLoaded", () => {

    console.log("Gestor cargado");

    // 🔥 CAMPOS DEL FORMULARIO
    const campos = [

        "titulo_bienvenida",
        "texto_bienvenida",

        "titulo_seccion1",
        "texto_seccion1",

        "titulo_seccion2",
        "texto_seccion2",

        "texto_completo_seccion2"

    ];

    // 🔥 VISTA PREVIA EN TIEMPO REAL
    campos.forEach(id => {

        const input = document.getElementById(id);
        const preview = document.getElementById("preview_" + id);

        if(input && preview){

            input.addEventListener("input", () => {

                preview.innerText = input.value;

            });

        }

    });

    // 🔥 PREVIEW DE IMÁGENES
    const imagenes = [

        "imagen_bienvenida_izq",
        "imagen_bienvenida_der",
        "imagen_seccion1",
        "imagen_seccion2",
        "slider1",
        "slider2",
        "slider3",
        "slider4"

    ];

    imagenes.forEach(id => {

        const input = document.querySelector(`input[name="${id}"]`);
        const preview = document.getElementById("preview_" + id);

        if(input && preview){

            input.addEventListener("change", () => {

                const archivo = input.files[0];

                if(archivo){

                    const reader = new FileReader();

                    reader.onload = function(e){

                        preview.src = e.target.result;

                    };

                    reader.readAsDataURL(archivo);

                }

            });

        }

    });

    // 🔥 EFECTO ESTABLE FORMULARIO
    const form = document.querySelector(".form-admin");

    if(form){

        form.style.transition = "all 0.25s ease";

        form.addEventListener("mouseenter", () => {

            form.style.boxShadow = "0 10px 25px rgba(0,0,0,0.10)";
            form.style.transform = "scale(1.005)";

        });

        form.addEventListener("mouseleave", () => {

            form.style.boxShadow = "none";
            form.style.transform = "scale(1)";

        });

    }

    // 🔥 ANIMACIÓN INPUTS
    const inputs = document.querySelectorAll("input, textarea");

    inputs.forEach(input => {

        input.style.transition = "all 0.2s ease";

        input.addEventListener("focus", () => {

            input.style.transform = "scale(1.01)";
            input.style.boxShadow = "0 0 10px rgba(37,99,235,0.20)";

        });

        input.addEventListener("blur", () => {

            input.style.transform = "scale(1)";
            input.style.boxShadow = "none";

        });

    });

    // 🔥 EFECTO SUAVE EN PREVIEW
    const previewBox = document.querySelector(".preview-box");

    if(previewBox){

        previewBox.style.transition = "all 0.25s ease";

        previewBox.addEventListener("mouseenter", () => {

            previewBox.style.transform = "scale(1.01)";
            previewBox.style.boxShadow = "0 10px 25px rgba(0,0,0,0.12)";

        });

        previewBox.addEventListener("mouseleave", () => {

            previewBox.style.transform = "scale(1)";
            previewBox.style.boxShadow = "none";

        });

    }

});