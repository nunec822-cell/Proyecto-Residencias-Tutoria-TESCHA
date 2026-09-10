/*=========================================================
    MOD_TUTORADO/actividades/actividades.js
=========================================================*/

/*=====================================
    CARGAR ACTIVIDADES
=====================================*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        cargarActividades();

    }
);

function cargarActividades(){

    fetch(
        "obtener_actividades.php"
    )

    .then(
        response => response.text()
    )

    .then(
        data => {

            document
            .getElementById(
                "contenedor-actividades"
            )
            .innerHTML = data;

        }
    )

    .catch(
        error => {

            console.log(error);

            document
            .getElementById(
                "contenedor-actividades"
            )
            .innerHTML = `

                <div class="mensaje-vacio">

                    No fue posible cargar las actividades.

                </div>

            `;

        }
    );

}

/*=====================================
    SUBIR EVIDENCIA
=====================================*/

document.addEventListener(
    "submit",
    function(e){

        if(
            e.target.classList.contains(
                "form-evidencia"
            )
        ){

            e.preventDefault();

            let formulario = e.target;

            let archivo =
            formulario.pdf.files[0];

            if(!archivo){

                alert(
                    "Seleccione un archivo PDF."
                );

                return;

            }

            /*=========================
                VALIDAR PDF
            =========================*/

            let extension =
            archivo.name
            .split(".")
            .pop()
            .toLowerCase();

            if(
                extension != "pdf"
            ){

                alert(
                    "Solo se permiten archivos PDF."
                );

                return;

            }

            /*=========================
                VALIDAR TAMAÑO
            =========================*/

            if(
                archivo.size >
                (2 * 1024 * 1024)
            ){

                alert(
                    "El archivo supera el tamaño máximo permitido (2 MB)."
                );

                return;

            }

            let datos =
            new FormData(
                formulario
            );

            let boton =
            formulario.querySelector(
                ".btn-subir"
            );

            let textoOriginal =
            boton.innerHTML;

            boton.disabled = true;

            boton.innerHTML =
            '<i class="fa-solid fa-spinner fa-spin"></i> Subiendo...';

            fetch(
                "subir_evidencia.php",
                {
                    method:"POST",
                    body:datos
                }
            )

            .then(
                response => response.text()
            )

            .then(
                data => {

                    boton.disabled = false;

                    boton.innerHTML =
                    textoOriginal;

                    switch(data){

                        case "OK":

                            alert(
                                "La evidencia fue enviada correctamente."
                            );

                            cargarActividades();

                        break;

                        case "PESO":

                            alert(
                                "El PDF excede el tamaño máximo permitido de 2 MB."
                            );

                        break;

                        case "FORMATO":

                            alert(
                                "El archivo debe estar en formato PDF."
                            );

                        break;

                        case "SIN_ARCHIVO":

                            alert(
                                "Debe seleccionar un archivo."
                            );

                        break;

                        case "ERROR_ARCHIVO":

                            alert(
                                "El archivo no pudo procesarse."
                            );

                        break;

                        default:

                            alert(
                                "Ocurrió un error al subir la evidencia."
                            );

                            console.log(data);

                    }

                }
            )

            .catch(
                error => {

                    boton.disabled = false;

                    boton.innerHTML =
                    textoOriginal;

                    console.log(error);

                    alert(
                        "Error de conexión con el servidor."
                    );

                }
            );

        }

    }
);