/*=========================================================
    MOD_TUTOR/actividades/actividades.js
=========================================================*/

/*=====================================
    INICIAR MÓDULO
=====================================*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        cargarGrupos();

    }
);

/*=====================================
    CARGAR GRUPOS
=====================================*/

function cargarGrupos(){

    fetch(
        "obtener_grupos.php"
    )

    .then(
        response =>
        response.text()
    )

    .then(
        data => {

            document.getElementById(
                "grupo"
            ).innerHTML = data;

        }
    )

    .catch(
        error => {

            console.log(
                error
            );

        }
    );

}

/*=====================================
    CAMBIO DE GRUPO
=====================================*/

document.addEventListener(
    "change",
    function(e){

        if(
            e.target.id ==
            "grupo"
        ){

            cargarActividades(
                e.target.value
            );

        }

    }
);

/*=====================================
    CARGAR ACTIVIDADES
=====================================*/

function cargarActividades(
    grupo
){

    if(
        grupo == ""
    ){

        document.getElementById(
            "contenedor-actividades"
        ).innerHTML = `

            <div class="mensaje-inicial">

                Seleccione un grupo para visualizar
                las actividades publicadas.

            </div>

        `;

        return;

    }

    fetch(
        "obtener_actividades.php?grupo=" +
        encodeURIComponent(grupo)
    )

    .then(
        response =>
        response.text()
    )

    .then(
        data => {

            document.getElementById(
                "contenedor-actividades"
            ).innerHTML = data;

        }
    )

    .catch(
        error => {

            console.log(
                error
            );

        }
    );

}

/*=====================================
    GUARDAR ACTIVIDAD
=====================================*/

document.addEventListener(
    "submit",
    function(e){

        if(
            e.target.id ==
            "formActividad"
        ){

            e.preventDefault();

            let datos =
            new FormData(
                e.target
            );

            fetch(
                "guardar_actividad.php",
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
                            "Actividad publicada correctamente."
                        );

                        e.target.reset();

                        let grupo =
                        document.getElementById(
                            "grupo"
                        ).value;

                        cargarGrupos();

                        setTimeout(

                            function(){

                                document.getElementById(
                                    "grupo"
                                ).value = grupo;

                                cargarActividades(
                                    grupo
                                );

                            },

                            300

                        );

                    }
                    else{

                        alert(
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
                        "Ocurrió un error al publicar la actividad."
                    );

                }
            );

        }

    }
);

/*=====================================
    ELIMINAR ACTIVIDAD
=====================================*/

document.addEventListener(
    "click",
    function(e){

        if(
            e.target.classList.contains(
                "btn-eliminar"
            )
        ){

            if(
                !confirm(
                    "¿Desea eliminar esta actividad?"
                )
            ){

                return;

            }

            let id =
            e.target.dataset.id;

            fetch(
                "eliminar_actividad.php",
                {

                    method:"POST",

                    headers:{

                        "Content-Type":
                        "application/x-www-form-urlencoded"

                    },

                    body:
                    "id_actividad=" +
                    id

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

                        let grupo =
                        document.getElementById(
                            "grupo"
                        ).value;

                        cargarActividades(
                            grupo
                        );

                    }
                    else{

                        alert(
                            "No fue posible eliminar la actividad."
                        );

                    }

                }
            );

        }

    }
);