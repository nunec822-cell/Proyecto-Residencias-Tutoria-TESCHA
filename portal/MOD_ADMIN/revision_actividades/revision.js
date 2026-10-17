/*=========================================================
MOD_ADMIN/revision_actividades/revision.js
=========================================================*/

/*=========================================
INICIO
=========================================*/

document.addEventListener(

    "DOMContentLoaded",

    function(){

        cargarCarreras();

    }

);

/*=========================================
CARGAR CARRERAS
=========================================*/

function cargarCarreras(){

    fetch(

        "obtener_carreras.php"

    )

    .then(

        response=>response.text()

    )

    .then(

        data=>{

            document.getElementById(

                "carrera"

            ).innerHTML=data;

        }

    )

    .catch(

        error=>{

            console.log(error);

        }

    );

}

/*=========================================
CAMBIO DE CARRERA
=========================================*/

document.addEventListener(

    "change",

    function(e){

        if(

            e.target.id=="carrera"

        ){

            let carrera=

            e.target.value;

            let grupo=

            document.getElementById(

                "grupo"

            );

            let actividad=

            document.getElementById(

                "actividad"

            );

            document.getElementById(

                "contenedor-evidencias"

            ).innerHTML=`

                <div class="mensaje-inicial">

                    Seleccione una actividad.

                </div>

            `;

            actividad.innerHTML=`

                <option value="">

                    Primero seleccione un grupo

                </option>

            `;

            actividad.disabled=true;

            if(

                carrera==""

            ){

                grupo.innerHTML=`

                    <option value="">

                        Primero seleccione una carrera

                    </option>

                `;

                grupo.disabled=true;

                return;

            }

            grupo.disabled=false;

            cargarGrupos(

                carrera

            );

        }

    }

);

/*=========================================
CARGAR GRUPOS
=========================================*/

function cargarGrupos(

    carrera

){

    fetch(

        "obtener_grupos.php?carrera="+

        encodeURIComponent(

            carrera

        )

    )

    .then(

        response=>response.text()

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

/*=========================================
CAMBIO DE GRUPO
=========================================*/

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

                "contenedor-evidencias"

            ).innerHTML=`

                <div class="mensaje-inicial">

                    Seleccione una actividad.

                </div>

            `;

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

/*=========================================
CARGAR ACTIVIDADES
=========================================*/

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

/*=========================================
CAMBIO DE ACTIVIDAD
=========================================*/

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

                    "contenedor-evidencias"

                ).innerHTML=`

                    <div class="mensaje-inicial">

                        Seleccione una actividad.

                    </div>

                `;

                return;

            }

            cargarEvidencias(

                id

            );

        }

    }

);

/*=========================================
CARGAR EVIDENCIAS
=========================================*/

function cargarEvidencias(

    idActividad

){

    document.getElementById(

        "contenedor-evidencias"

    ).innerHTML=`

        <div class="mensaje-inicial">

            Cargando información...

        </div>

    `;

    fetch(

        "obtener_evidencias.php?id_actividad="+

        idActividad

    )

    .then(

        response=>response.text()

    )

    .then(

        data=>{

            document.getElementById(

                "contenedor-evidencias"

            ).innerHTML=data;

        }

    )

    .catch(

        error=>{

            console.log(error);

            document.getElementById(

                "contenedor-evidencias"

            ).innerHTML=`

                <div class="mensaje-vacio">

                    Ocurrió un error al cargar la información.

                </div>

            `;

        }

    );

}