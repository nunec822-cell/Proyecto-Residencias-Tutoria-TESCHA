/*=========================================
CARGAR INFORMACIÓN DEL ALUMNO
=========================================*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        const parametros =
        new URLSearchParams(
            window.location.search
        );

        const id =
        parametros.get("id");

        if(!id){

            document.getElementById(
                "contenedor-detalle"
            ).innerHTML =
            "Alumno no válido.";

            return;

        }

        fetch(
            "obtener_detalle.php?id=" + id
        )

        .then(
            response =>
            response.text()
        )

        .then(
            data => {

                document.getElementById(
                    "contenedor-detalle"
                ).innerHTML =
                data;

            }
        )

        .catch(
            error => {

                document.getElementById(
                    "contenedor-detalle"
                ).innerHTML =
                "Error al cargar la información del alumno.";

                console.log(
                    error
                );

            }
        );

    }
);

/*=========================================
GUARDAR CANALIZACIÓN
=========================================*/

document.addEventListener(
    "submit",
    function(e){

        if(
            e.target.id ==
            "form-canalizacion"
        ){

            e.preventDefault();

            let datos =
            new FormData(
                e.target
            );

            fetch(
                "guardar_canalizacion.php",
                {
                    method:"POST",
                    body:datos
                }
            )

            .then(
                response =>
                response.text()
            )

            .then(
                data => {

                    if(
                        data == "OK"
                    ){

                        alert(
                            "Canalización realizada correctamente."
                        );

                        location.reload();

                    }
                    else if(
                        data == "EXISTE"
                    ){

                        alert(
                            "El alumno ya tiene una canalización activa."
                        );

                    }
                    else{

                        alert(
                            "Ocurrió un error."
                        );

                        console.log(
                            data
                        );

                    }

                }
            )

            .catch(
                error => {

                    console.log(
                        error
                    );

                    alert(
                        "Error de conexión."
                    );

                }
            );

        }

    }
);