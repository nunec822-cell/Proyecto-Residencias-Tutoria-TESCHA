document.addEventListener(
    "DOMContentLoaded",
    function(){

        const modal =
            document.getElementById(
                "modal-fotografia-tutor"
            );

        const boton =
            document.getElementById(
                "btn-ir-fotografia"
            );

        const apartado =
            document.querySelector(
                ".fotografia-tutor-section"
            );

        const input =
            document.getElementById(
                "fotografia"
            );

        const nombreArchivo =
            document.getElementById(
                "nombre-fotografia"
            );


        /*=====================================
            MOSTRAR MODAL
        =====================================*/

        if(modal){

            setTimeout(
                function(){

                    modal.classList.add(
                        "mostrar"
                    );

                },
                500
            );

        }


        /*=====================================
            IR AL APARTADO DE FOTOGRAFÍA
        =====================================*/

        if(boton){

            boton.addEventListener(
                "click",
                function(){

                    if(modal){

                        modal.classList.remove(
                            "mostrar"
                        );

                    }

                    if(apartado){

                        apartado.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });

                    }

                }
            );

        }


        /*=====================================
            MOSTRAR NOMBRE DEL ARCHIVO
        =====================================*/

        if(input){

            input.addEventListener(
                "change",
                function(){

                    if(
                        input.files &&
                        input.files.length > 0
                    ){

                        nombreArchivo.textContent =
                            input.files[0].name;

                    }else{

                        nombreArchivo.textContent =
                            "Ningún archivo seleccionado";

                    }

                }
            );

        }

    }
);