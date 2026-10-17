/*=========================================
CARGAR GRUPOS
=========================================*/

const carrera =
document.getElementById(
    "carrera"
);

const grupo =
document.getElementById(
    "grupo"
);

const btnConsultar =
document.getElementById(
    "btnConsultar"
);

const btnCSV =
document.getElementById(
    "btnCSV"
);

const contenedor =
document.getElementById(
    "contenedor-anexo"
);

/*=========================================
CARRERA
=========================================*/

carrera.addEventListener(
    "change",
    function(){

        let valor =
        this.value;

        grupo.innerHTML = `
            <option>
                Cargando...
            </option>
        `;

        fetch(
            "obtener_grupos.php?carrera=" +
            encodeURIComponent(
                valor
            )
        )

        .then(
            response =>
            response.text()
        )

        .then(
            data => {

                grupo.innerHTML =
                data;

            }
        )

        .catch(() => {

            grupo.innerHTML = `
                <option>
                    Error al cargar
                </option>
            `;

        });

    }
);

/*=========================================
CONSULTAR ANEXO
=========================================*/

btnConsultar.addEventListener(
    "click",
    function(){

        let carreraSeleccionada =
        carrera.value;

        let grupoSeleccionado =
        grupo.value;

        if(
            carreraSeleccionada === "" ||
            grupoSeleccionado === ""
        ){

            alert(
                "Seleccione una carrera y un grupo."
            );

            return;

        }

        contenedor.innerHTML = `

            <div class="cargando">

                <i class="
                    fa-solid
                    fa-spinner
                    fa-spin
                ">
                </i>

                Cargando información...

            </div>

        `;

        fetch(

            "obtener_anexo.php?" +

            "carrera=" +

            encodeURIComponent(
                carreraSeleccionada
            )

            +

            "&grupo=" +

            encodeURIComponent(
                grupoSeleccionado
            )

        )

        .then(
            response =>
            response.text()
        )

        .then(
            data => {

                contenedor.innerHTML =
                data;

            }
        )

        .catch(() => {

            contenedor.innerHTML = `

                <div
                    class="
                    mensaje-error
                    "
                >

                    Ocurrió un error
                    al consultar el
                    Anexo 14.

                </div>

            `;

        });

    }
);

/*=========================================
DESCARGAR CSV
=========================================*/

btnCSV.addEventListener(
    "click",
    function(){

        let carreraSeleccionada =
        carrera.value;

        let grupoSeleccionado =
        grupo.value;

        if(
            carreraSeleccionada === "" ||
            grupoSeleccionado === ""
        ){

            alert(
                "Seleccione una carrera y un grupo."
            );

            return;

        }

        window.location.href =

            "exportar_csv.php?"

            +

            "carrera=" +

            encodeURIComponent(
                carreraSeleccionada
            )

            +

            "&grupo=" +

            encodeURIComponent(
                grupoSeleccionado
            );

    }
);