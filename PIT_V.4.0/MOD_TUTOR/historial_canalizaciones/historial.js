/*=========================================================
    MOD_TUTOR/historial_canalizaciones/historial.js
=========================================================*/

/*=====================================
    INICIAR MÓDULO
=====================================*/

document.addEventListener(
    "DOMContentLoaded",
    function () {

        cargarGrupos();

    }
);

/*=====================================
    CARGAR GRUPOS
=====================================*/

function cargarGrupos() {

    fetch("obtener_grupos.php")

        .then(response => response.text())

        .then(data => {

            document
                .getElementById("grupo")
                .innerHTML = data;

        })

        .catch(error => {

            console.log(error);

        });

}

/*=====================================
    CAMBIO DE GRUPO
=====================================*/

document.addEventListener(
    "change",
    function (e) {

        if (e.target.id == "grupo") {

            let grupo = e.target.value;

            if (grupo == "") {

                document
                    .getElementById("contenedor-alumnos")
                    .innerHTML = "";

                document
                    .getElementById("contenedor-historial")
                    .innerHTML = "";

                return;

            }

            fetch(
                "obtener_alumnos.php?grupo="
                + encodeURIComponent(grupo)
            )

                .then(response => response.text())

                .then(data => {

                    document
                        .getElementById("contenedor-alumnos")
                        .innerHTML = data;

                    document
                        .getElementById("contenedor-historial")
                        .innerHTML = "";

                })

                .catch(error => {

                    console.log(error);

                    document
                        .getElementById("contenedor-alumnos")
                        .innerHTML = `
                            <div class="mensaje-vacio">
                                Error al cargar los alumnos.
                            </div>
                        `;

                });

        }

    }
);

/*=====================================
    VER HISTORIAL
=====================================*/

document.addEventListener(
    "click",
    function (e) {

        const boton = e.target.closest(".btn-historial");

        if (!boton) {
            return;
        }

        let id = boton.dataset.id;

        fetch(
            "obtener_historial.php?id_tutorado=" + id
        )

            .then(response => response.text())

            .then(data => {

                document
                    .getElementById("contenedor-historial")
                    .innerHTML = data;

                document
                    .getElementById("modal-historial")
                    .style.display = "flex";

            })

            .catch(error => {

                console.log(error);

                document
                    .getElementById("contenedor-historial")
                    .innerHTML = `
                        <div class="mensaje-vacio">

                            Error al obtener
                            el historial del alumno.

                        </div>
                    `;

                document
                    .getElementById("modal-historial")
                    .style.display = "flex";

            });

    }
);