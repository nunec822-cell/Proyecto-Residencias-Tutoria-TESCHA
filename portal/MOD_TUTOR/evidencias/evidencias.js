/*=========================================================
MOD_TUTOR/evidencias/evidencias.js
=========================================================*/

/*=====================================
INICIO
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

        response => response.text()

    )

    .then(

        data=>{

            document.getElementById(

                "grupo"

            ).innerHTML=data;

        }

    )

    .catch(

        error=>{

            console.log(error);

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

            e.target.id=="grupo"

        ){

            let grupo=

            e.target.value;

            let actividad=

            document.getElementById(

                "actividad"

            );

            document.getElementById(

                "contenedor-entregas"

            ).innerHTML=`

                <div class="mensaje-inicial">

                    Seleccione una actividad.

                </div>

            `;

            /*=====================================
            REINICIAR CONTADORES
            =====================================*/

            actualizarContadores(

                0,

                0,

                0

            );

            if(

                grupo==""

            ){

                actividad.innerHTML=`

                    <option value="">

                        Primero seleccione un grupo

                    </option>

                `;

                actividad.disabled=true;

                return;

            }

            actividad.disabled=false;

            cargarActividades(

                grupo

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

    fetch(

        "obtener_actividades.php?grupo="+

        encodeURIComponent(

            grupo

        )

    )

    .then(

        response=>response.text()

    )

    .then(

        data=>{

            document.getElementById(

                "actividad"

            ).innerHTML=data;

        }

    )

    .catch(

        error=>{

            console.log(error);

        }

    );

}

/*=====================================
CAMBIO DE ACTIVIDAD
=====================================*/

document.addEventListener(

    "change",

    function(e){

        if(

            e.target.id=="actividad"

        ){

            let id=

            e.target.value;

            if(

                id==""

            ){

                document.getElementById(

                    "contenedor-entregas"

                ).innerHTML=`

                    <div class="mensaje-inicial">

                        Seleccione una actividad.

                    </div>

                `;

                actualizarContadores(

                    0,

                    0,

                    0

                );

                return;

            }

            cargarEntregas(

                id

            );

        }

    }

);

/*=====================================
CARGAR ENTREGAS
=====================================*/

function cargarEntregas(

    idActividad

){

    document.getElementById(

        "contenedor-entregas"

    ).innerHTML=`

        <div class="mensaje-inicial">

            Cargando evidencias...

        </div>

    `;

    fetch(

        "obtener_entregas.php?id_actividad="+

        idActividad

    )

    .then(

        response=>response.text()

    )

    .then(

        data=>{

            document.getElementById(

                "contenedor-entregas"

            ).innerHTML=data;

            /*=====================================
            LEER ESTADÍSTICAS
            =====================================*/

            let estadisticas = document.getElementById(

                "estadisticas"

            );

            if(

                estadisticas

            ){

                actualizarContadores(

                    estadisticas.dataset.total,

                    estadisticas.dataset.entregadas,

                    estadisticas.dataset.pendientes

                );

            }

        }

    )

    .catch(

        error=>{

            console.log(error);

            document.getElementById(

                "contenedor-entregas"

            ).innerHTML=`

                <div class="mensaje-vacio">

                    Ocurrió un error al cargar
                    las evidencias.

                </div>

            `;

        }

    );

}

/*=====================================
ACTUALIZAR CONTADORES
=====================================*/

function actualizarContadores(

    total,

    entregadas,

    pendientes

){

    const totalAlumnos = document.getElementById(

        "totalAlumnos"

    );

    const totalEntregadas = document.getElementById(

        "totalEntregadas"

    );

    const totalPendientes = document.getElementById(

        "totalPendientes"

    );

    if(

        totalAlumnos

    ){

        totalAlumnos.textContent = total;

    }

    if(

        totalEntregadas

    ){

        totalEntregadas.textContent = entregadas;

    }

    if(

        totalPendientes

    ){

        totalPendientes.textContent = pendientes;

    }

}