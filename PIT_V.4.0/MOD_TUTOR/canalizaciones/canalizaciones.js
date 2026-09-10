
/*=========================================================
    MOD_TUTOR/canalizaciones/canalizaciones.js
=========================================================*/


/*=====================================
    INICIAR MÓDULO
=====================================*/

document.addEventListener(
    "DOMContentLoaded",
    function(){

        cargarCanalizaciones();

    }
);


/*=====================================
    CARGAR CANALIZACIONES
=====================================*/

function cargarCanalizaciones(){

    fetch(
        "obtener_canalizaciones.php"
    )

    .then(
        response =>
        response.text()
    )

    .then(
        data => {

            document
            .getElementById(
                "contenedor-canalizaciones"
            )
            .innerHTML = data;

            actualizarResumen();

        }
    )

    .catch(
        error => {

            console.log(
                error
            );

            document
            .getElementById(
                "contenedor-canalizaciones"
            )
            .innerHTML = `

                <div class="mensaje-vacio">

                    Error al cargar
                    las canalizaciones.

                </div>

            `;

        }
    );

}


/*=====================================
    ACTUALIZAR RESUMEN
=====================================*/

function actualizarResumen(){

    let pendientes =
    document.querySelectorAll(
        ".estado-pendiente"
    ).length;


    let proceso =
    document.querySelectorAll(
        ".estado-proceso"
    ).length;


    let atendidas =
    document.querySelectorAll(
        ".estado-atendida"
    ).length;


    let total =
    pendientes +
    proceso +
    atendidas;


    document.getElementById(
        "totalGeneral"
    ).innerText =
    total;


    document.getElementById(
        "totalPendientes"
    ).innerText =
    pendientes;


    document.getElementById(
        "totalProceso"
    ).innerText =
    proceso;


    document.getElementById(
        "totalAtendidas"
    ).innerText =
    atendidas;

}


/*=====================================
    EVENTOS DE BOTONES
=====================================*/

document.addEventListener(
    "click",
    function(e){


        /*=================================
            TOMAR CANALIZACIÓN
        =================================*/

        if(
            e.target.closest(
                ".btn-tomar"
            )
        ){

            let boton =
            e.target.closest(
                ".btn-tomar"
            );


            let id =
            boton.dataset.id;


            mostrarModalAcciones(
                id
            );

        }


        /*=================================
            FINALIZAR CANALIZACIÓN
        =================================*/

        if(
            e.target.closest(
                ".btn-finalizar"
            )
        ){

            let boton =
            e.target.closest(
                ".btn-finalizar"
            );


            let id =
            boton.dataset.id;


            finalizarCanalizacion(
                id
            );

        }

    }
);


/*=====================================
    MODAL PARA TOMAR
=====================================*/

function mostrarModalAcciones(
    id
){

    let fondo =
    document.createElement(
        "div"
    );


    fondo.className =
    "modal-acciones";


    fondo.innerHTML = `

        <div class="modal-contenido">


            <div class="modal-encabezado">

                <h2>

                    <i class="fa-solid fa-hand-pointer"></i>

                    Tomar Canalización

                </h2>


                <button
                    type="button"
                    class="btn-cerrar-modal"
                    id="cerrarModal"
                >

                    <i class="fa-solid fa-xmark"></i>

                </button>

            </div>


            <div class="modal-cuerpo">


                <p>

                    Al tomar esta canalización,
                    usted será responsable de dar
                    seguimiento al tutorado.

                </p>


                <p>

                    Indique las acciones que realizará
                    para atender la situación del alumno.

                </p>


                <label
                    for="accionesTutor"
                >

                    Acciones que realizaré:

                </label>


                <textarea
                    id="accionesTutor"
                    maxlength="2000"
                    rows="7"
                    placeholder="Escriba las acciones que realizará para atender y dar seguimiento al tutorado..."
                ></textarea>


                <div class="contador-caracteres">

                    <span id="contadorAcciones">

                        0

                    </span>

                    / 2000

                </div>


                <div class="modal-botones">


                    <button
                        type="button"
                        class="btn-cancelar-modal"
                        id="cancelarAcciones"
                    >

                        Cancelar

                    </button>


                    <button
                        type="button"
                        class="btn-guardar-acciones"
                        id="guardarAcciones"
                    >

                        <i class="fa-solid fa-check"></i>

                        Tomar Canalización

                    </button>


                </div>


            </div>

        </div>

    `;


    document.body.appendChild(
        fondo
    );


    let textarea =
    document.getElementById(
        "accionesTutor"
    );


    let contador =
    document.getElementById(
        "contadorAcciones"
    );


    /*=================================
        CONTADOR
    =================================*/

    textarea.addEventListener(
        "input",
        function(){

            contador.innerText =
            textarea.value.length;

        }
    );


    /*=================================
        CERRAR MODAL
    =================================*/

    document
    .getElementById(
        "cerrarModal"
    )
    .addEventListener(
        "click",
        function(){

            fondo.remove();

        }
    );


    document
    .getElementById(
        "cancelarAcciones"
    )
    .addEventListener(
        "click",
        function(){

            fondo.remove();

        }
    );


    /*=================================
        GUARDAR CANALIZACIÓN
    =================================*/

    document
    .getElementById(
        "guardarAcciones"
    )
    .addEventListener(
        "click",
        function(){

            let acciones =
            textarea.value.trim();


            /*=========================
                VALIDAR VACÍO
            =========================*/

            if(
                acciones === ""
            ){

                alert(
                    "Debe indicar las acciones que realizará."
                );


                textarea.focus();


                return;

            }


            /*=========================
                VALIDAR LONGITUD
            =========================*/

            if(
                acciones.length < 10
            ){

                alert(
                    "Escriba con mayor detalle las acciones que realizará."
                );


                textarea.focus();


                return;

            }


            /*=========================
                BOTÓN CARGANDO
            =========================*/

            let botonGuardar =
            document.getElementById(
                "guardarAcciones"
            );


            botonGuardar.disabled =
            true;


            botonGuardar.innerHTML = `

                <i class="fa-solid fa-spinner fa-spin"></i>

                Guardando...

            `;


            /*=========================
                ENVIAR AL PHP
            =========================*/

            fetch(
                "tomar_canalizacion.php",
                {

                    method:"POST",

                    headers:{
                        "Content-Type":
                        "application/x-www-form-urlencoded"
                    },


                    body:

                        "id_canalizacion="

                        +

                        encodeURIComponent(
                            id
                        )

                        +

                        "&acciones_tutor="

                        +

                        encodeURIComponent(
                            acciones
                        )

                }
            )


            .then(
                response =>
                response.text()
            )


            .then(
                data => {


                    /*=========================
                        LIMPIAR RESPUESTA PHP
                    =========================*/

                    data =
                    data.trim();


                    console.log(
                        "RESPUESTA TOMAR:",
                        data
                    );


                    /*=========================
                        ÉXITO
                    =========================*/

                    if(
                        data ===
                        "OK"
                    ){

                        fondo.remove();


                        alert(
                            "Canalización tomada correctamente.\n\nLas acciones fueron registradas."
                        );


                        cargarCanalizaciones();

                    }


                    /*=========================
                        SIN ACCIONES
                    =========================*/

                    else if(
                        data ===
                        "SIN_ACCIONES"
                    ){

                        alert(
                            "Debe escribir las acciones que realizará."
                        );


                        botonGuardar.disabled =
                        false;


                        botonGuardar.innerHTML = `

                            <i class="fa-solid fa-check"></i>

                            Tomar Canalización

                        `;

                    }


                    /*=========================
                        ACCIONES CORTAS
                    =========================*/

                    else if(
                        data ===
                        "ACCIONES_CORTAS"
                    ){

                        alert(
                            "Describa con mayor detalle las acciones que realizará."
                        );


                        botonGuardar.disabled =
                        false;


                        botonGuardar.innerHTML = `

                            <i class="fa-solid fa-check"></i>

                            Tomar Canalización

                        `;

                    }


                    /*=========================
                        OTRO ERROR
                    =========================*/

                    else{

                        alert(
                            "No fue posible tomar la canalización.\n\nRespuesta: "
                            +
                            data
                        );


                        botonGuardar.disabled =
                        false;


                        botonGuardar.innerHTML = `

                            <i class="fa-solid fa-check"></i>

                            Tomar Canalización

                        `;

                    }

                }
            )


            .catch(
                error => {

                    console.log(
                        error
                    );


                    alert(
                        "Ocurrió un error al tomar la canalización."
                    );


                    botonGuardar.disabled =
                    false;


                    botonGuardar.innerHTML = `

                        <i class="fa-solid fa-check"></i>

                        Tomar Canalización

                    `;

                }
            );

        }
    );


    /*=================================
        ENFOCAR TEXTAREA
    =================================*/

    setTimeout(
        function(){

            textarea.focus();

        },
        100
    );

}


/*=====================================
    FINALIZAR CANALIZACIÓN
=====================================*/

function finalizarCanalizacion(
    id
){

    let confirmar =
    confirm(
        "¿Está seguro de que desea finalizar la atención de esta canalización?"
    );


    if(
        !confirmar
    ){

        return;

    }


    /*=================================
        ENVIAR
    =================================*/

    fetch(
        "finalizar_canalizacion.php",
        {

            method:"POST",

            headers:{
                "Content-Type":
                "application/x-www-form-urlencoded"
            },


            body:

            "id_canalizacion="

            +

            encodeURIComponent(
                id
            )

        }
    )


    .then(
        response =>
        response.text()
    )


    .then(
        data => {


            /*=========================
                LIMPIAR RESPUESTA PHP
            =========================*/

            data =
            data.trim();


            console.log(
                "RESPUESTA FINALIZAR:",
                data
            );


            /*=========================
                ÉXITO
            =========================*/

            if(
                data ===
                "OK"
            ){

                alert(
                    "La atención fue finalizada correctamente."
                );


                cargarCanalizaciones();

            }


            /*=========================
                ERROR
            =========================*/

            else{

                alert(
                    "No fue posible finalizar la atención.\n\nRespuesta: "
                    +
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
                "Ocurrió un error al finalizar la atención."
            );

        }
    );

}

