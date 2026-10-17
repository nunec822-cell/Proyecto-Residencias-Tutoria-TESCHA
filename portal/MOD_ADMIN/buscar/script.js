document.addEventListener("DOMContentLoaded", () => {

    const contenido = document.getElementById("contenido");
    const botones = document.querySelectorAll(".opcion");

    botones.forEach(boton => {

        boton.addEventListener("click", function () {

            let tipo = this.innerText.trim();

            // =========================
            // DOCENTES
            // =========================
            if (tipo === "Docentes") {

                contenido.innerHTML = `
                    <div class="panel-opciones">
                        <h2>DOCENTES</h2>
                        <p>Seleccione tipo de consulta</p>

                        <div class="opciones-grid">

                            <div class="opcion-card" id="docentes-general">
                                <i class="fa-solid fa-list"></i>
                                <h3>General</h3>
                            </div>

                            <div class="opcion-card" id="docentes-carrera">
                                <i class="fa-solid fa-school"></i>
                                <h3>Por Carrera</h3>
                            </div>

                        </div>
                    </div>
                `;

                setTimeout(() => {

                    document.getElementById("docentes-general")
                        .addEventListener("click", cargarDocentes);

                    document.getElementById("docentes-carrera")
                        .addEventListener("click", cargarDocentesCarrera);

                }, 100);

            }

            // =========================
            // TUTORADOS
            // =========================
            else if (tipo === "Tutorados") {

                contenido.innerHTML = `
                    <div class="panel-opciones">

                        <h2>TUTORADOS</h2>

                        <p>
                            Seleccione la forma en que desea visualizar la información.
                        </p>

                        <div class="opciones-grid">

                            <div class="opcion-card" id="tutorados-carrera">
                                <i class="fa-solid fa-school"></i>
                                <h3>Por Carrera</h3>
                            </div>

                            <div class="opcion-card" id="tutorados-tutor">
                                <i class="fa-solid fa-user-tie"></i>
                                <h3>Por Tutor</h3>
                            </div>

                            <div class="opcion-card" id="tutorados-grupo">
                                <i class="fa-solid fa-users"></i>
                                <h3>Por Grupo</h3>
                            </div>

                        </div>

                    </div>
                `;

                setTimeout(() => {

                    document.getElementById("tutorados-carrera")
                        .addEventListener("click", cargarTutoradosCarrera);

                    document.getElementById("tutorados-tutor")
                        .addEventListener("click", cargarTutoradosTutor);

                    document.getElementById("tutorados-grupo")
                        .addEventListener("click", cargarTutoradosGrupo);

                }, 100);

            }
            // =========================
            // TUTORES
            // =========================
            else if (tipo === "Tutores") {

            contenido.innerHTML = `
                <div class="panel-opciones">

                <h2>TUTORES</h2>

                <p>
                    Seleccione el tipo de consulta.
                </p>

                <div class="opciones-grid">

                    <div class="opcion-card" id="tutores-general">
                        <i class="fa-solid fa-list"></i>
                        <h3>General</h3>
                    </div>

                    <div class="opcion-card" id="tutores-carrera">
                        <i class="fa-solid fa-school"></i>
                        <h3>Por Carrera</h3>
                    </div>

                </div>

            </div>
        `;

        setTimeout(() => {

            document.getElementById("tutores-general")
                .addEventListener("click", cargarTutores);

            document.getElementById("tutores-carrera")
                .addEventListener("click", cargarTutoresCarrera);

        }, 100);

    }   
    // =========================
// COORDINADORES
// =========================
else if (tipo === "Coordinadores") {

    contenido.innerHTML = `

        <div class="panel-opciones">

            <h2>COORDINADORES</h2>

            <p>
                Seleccione el tipo de consulta.
            </p>

            <div class="opciones-grid">

                <div class="opcion-card" id="coordinadores-general">

                    <i class="fa-solid fa-list"></i>

                    <h3>General</h3>

                </div>

                <div class="opcion-card" id="coordinadores-carrera">

                    <i class="fa-solid fa-school"></i>

                    <h3>Por Carrera</h3>

                </div>

            </div>

        </div>

    `;

    setTimeout(() => {

        document
            .getElementById("coordinadores-general")
            .addEventListener("click", cargarCoordinadores);

        document
            .getElementById("coordinadores-carrera")
            .addEventListener("click", cargarCoordinadoresCarrera);

    },100);

}
// =========================
// JEFES DE CARRERA
// =========================
else if (tipo === "Jefes de Carrera") {

    contenido.innerHTML = `

        <div class="panel-opciones">

            <h2>JEFES DE CARRERA</h2>

            <p>
                Seleccione el tipo de consulta.
            </p>

            <div class="opciones-grid">

                <div class="opcion-card" id="jefes-general">

                    <i class="fa-solid fa-list"></i>

                    <h3>General</h3>

                </div>

                <div class="opcion-card" id="jefes-carrera">

                    <i class="fa-solid fa-school"></i>

                    <h3>Por Carrera</h3>

                </div>

            </div>

        </div>

    `;

    setTimeout(() => {

        document
            .getElementById("jefes-general")
            .addEventListener("click", cargarJefes);

        document
            .getElementById("jefes-carrera")
            .addEventListener("click", cargarJefesCarrera);

    },100);

}
// =========================
// DIRECTIVOS
// =========================
else if (tipo === "Directivos") {

    contenido.innerHTML = `

        <div class="panel-opciones">

            <h2>DIRECTIVOS</h2>

            <p>
                Visualizar todos los directivos registrados.
            </p>

            <div class="opciones-grid">

                <div class="opcion-card" id="directivos-general">

                    <i class="fa-solid fa-users"></i>

                    <h3>Mostrar Directivos</h3>

                </div>

            </div>

        </div>

    `;

    setTimeout(() => {

        document
            .getElementById("directivos-general")
            .addEventListener("click", cargarDirectivos);

    },100);

}
// =========================
// PSICÓLOGOS
// =========================
else if (tipo === "Psicólogos") {

    contenido.innerHTML = `

        <div class="panel-opciones">

            <h2>PSICÓLOGOS</h2>

            <p>
                Visualizar todos los psicólogos registrados.
            </p>

            <div class="opciones-grid">

                <div class="opcion-card" id="psicologos-general">

                    <i class="fa-solid fa-user-doctor"></i>

                    <h3>Mostrar Psicólogos</h3>

                </div>

            </div>

        </div>

    `;

    setTimeout(() => {

        document
            .getElementById("psicologos-general")
            .addEventListener("click", cargarPsicologos);

    },100);

}
        });
        
    });

    // ======================================
    // DOCENTES - GENERAL
    // ======================================
    function cargarDocentes() {

        fetch("ajax/docentes.php?modo=general")
            .then(res => res.json())
            .then(data => {

                let html = `
                    <h2>DOCENTES - GENERAL</h2>

                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>No Empleado</th>
                                <th>Nombre</th>
                                <th>Apellido P</th>
                                <th>Apellido M</th>
                                <th>Carrera</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                data.forEach(d => {

                    html += `
                        <tr>
                            <td>${d.no_empleado}</td>
                            <td>${d.nombre}</td>
                            <td>${d.apellido_p}</td>
                            <td>${d.apellido_m}</td>
                            <td>${d.carrera}</td>
                        </tr>
                    `;

                });

                html += `
        </tbody>
    </table>

    <br>

    <button
        class="btn-csv"
        onclick="window.location='ajax/exportar_csv.php?tipo=docentes&modo=general'">

        <i class="fa-solid fa-file-csv"></i>

        Descargar CSV

    </button>
`;

                contenido.innerHTML = html;

            });

    }

    // ======================================
    // DOCENTES - POR CARRERA
    // ======================================
    function cargarDocentesCarrera() {

        fetch("ajax/docentes.php?modo=carrera")
            .then(res => res.json())
            .then(data => {

                let carreras = {};

                data.forEach(d => {

                    if (!carreras[d.carrera]) {
                        carreras[d.carrera] = [];
                    }

                    carreras[d.carrera].push(d);

                });

                let html = `<h2>DOCENTES - POR CARRERA</h2>`;

                for (let carrera in carreras) {

                    html += `
                        <h3 style="margin-top:30px;color:#0B5ED7;">
                            ${carrera}
                        </h3>

                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th>No Empleado</th>
                                    <th>Nombre</th>
                                    <th>Apellido P</th>
                                    <th>Apellido M</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    carreras[carrera].forEach(d => {

                        html += `
                            <tr>
                                <td>${d.no_empleado}</td>
                                <td>${d.nombre}</td>
                                <td>${d.apellido_p}</td>
                                <td>${d.apellido_m}</td>
                            </tr>
                        `;

                    });

                    html += `
        </tbody>
    </table>

    <br>

    <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=docentes&modo=carrera&carrera=${encodeURIComponent(carrera)}'">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

    <hr>
`;

                }

                contenido.innerHTML = html;

            });

    }

    // ======================================
    // TUTORADOS - POR CARRERA
    // ======================================
    function cargarTutoradosCarrera(){

    fetch("ajax/tutorados.php?modo=carrera")

    .then(res=>res.json())

    .then(data=>{

        let carreras={};

        data.forEach(t=>{

            if(!carreras[t.carrera]){

                carreras[t.carrera]=[];

            }

            carreras[t.carrera].push(t);

        });

        let html="<h2>TUTORADOS POR CARRERA</h2>";

        for(let carrera in carreras){

            html+=`

            <h3 style="margin-top:35px;color:#0B5ED7;">
                ${carrera}
            </h3>

            <table class="tabla">

            <thead>

            <tr>

                <th>Matrícula</th>
                <th>Nombre</th>
                <th>Apellido P</th>
                <th>Apellido M</th>
                <th>Grupo</th>
                <th>Tutor</th>
                <th>No. Empleado</th>

            </tr>

            </thead>

            <tbody>

            `;

            carreras[carrera].forEach(t=>{

                html+=`

                <tr>

                    <td>${t.matricula}</td>

                    <td>${t.nombre}</td>

                    <td>${t.apellido_p}</td>

                    <td>${t.apellido_m}</td>

                    <td>${t.grupo}</td>

                    <td>

                        ${t.tutor_nombre ?? ""}

                        ${t.tutor_apellido_p ?? ""}

                        ${t.tutor_apellido_m ?? ""}

                    </td>

                    <td>${t.no_empleado ?? ""}</td>

                </tr>

                `;

            });

            html+=`

            </tbody>

            </table>

            <br>

            <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=tutorados&modo=carrera&carrera=${encodeURIComponent(carrera)}'">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

            <hr>

            `;

        }

        contenido.innerHTML=html;

    });

}

    // ======================================
    // TUTORADOS - POR TUTOR
    // ======================================
    function cargarTutoradosTutor() {

        fetch("ajax/tutorados.php?modo=tutor")

        .then(res => res.json())

        .then(data => {

            let tutores = {};

            data.forEach(t => {

                let llave = t.id_usuario;

                if (!tutores[llave]) {

                    tutores[llave] = {

                        nombre:
                            t.tutor_nombre + " " +
                            t.tutor_apellido_p + " " +
                            t.tutor_apellido_m,

                        empleado: t.no_empleado,

                        carrera: t.carrera,

                        alumnos: []

                    };

                }

                tutores[llave].alumnos.push(t);

            });

            let html = "<h2>TUTORADOS POR TUTOR</h2>";

            for (let id in tutores) {

                let tutor = tutores[id];

                html += `

                <h3 style="margin-top:35px;color:#0B5ED7;">
                    ${tutor.nombre}
                </h3>

                <p>

                    <strong>No. Empleado:</strong>
                    ${tutor.empleado}

                    &nbsp;&nbsp;&nbsp;&nbsp;

                    <strong>Carrera:</strong>
                    ${tutor.carrera}

                </p>

                <table class="tabla">

                    <thead>

                        <tr>

                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Apellido P</th>
                            <th>Apellido M</th>
                            <th>Grupo</th>

                        </tr>

                    </thead>

                    <tbody>

                `;

                tutor.alumnos.forEach(a => {

                    html += `

                    <tr>

                        <td>${a.matricula}</td>

                        <td>${a.nombre}</td>

                        <td>${a.apellido_p}</td>

                        <td>${a.apellido_m}</td>

                        <td>${a.grupo}</td>

                    </tr>

                    `;

                });

                html += `

                    </tbody>

                </table>

                <br>

                <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=tutorados&modo=tutor&id='+${id}">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

                <hr>

                `;

            }

            contenido.innerHTML = html;

        });

    }

    // ======================================
// TUTORADOS - POR GRUPO
// ======================================
function cargarTutoradosGrupo() {

    fetch("ajax/tutorados.php?modo=grupo")

    .then(res => res.json())

    .then(data => {

        let grupos = {};

        data.forEach(t => {

            if (!grupos[t.grupo]) {

                grupos[t.grupo] = [];

            }

            grupos[t.grupo].push(t);

        });

        let html = "<h2>TUTORADOS POR GRUPO</h2>";

        for (let grupo in grupos) {

            html += `

                <h3 style="margin-top:35px;color:#0B5ED7;">
                    Grupo ${grupo}
                </h3>

                <table class="tabla">

                    <thead>

                        <tr>

                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Apellido P</th>
                            <th>Apellido M</th>
                            <th>Carrera</th>
                            <th>Tutor</th>
                            <th>No. Empleado</th>

                        </tr>

                    </thead>

                    <tbody>

            `;

            grupos[grupo].forEach(t => {

                html += `

                    <tr>

                        <td>${t.matricula}</td>

                        <td>${t.nombre}</td>

                        <td>${t.apellido_p}</td>

                        <td>${t.apellido_m}</td>

                        <td>${t.carrera}</td>

                        <td>

                            ${t.tutor_nombre ?? ""}

                            ${t.tutor_apellido_p ?? ""}

                            ${t.tutor_apellido_m ?? ""}

                        </td>

                        <td>${t.no_empleado ?? ""}</td>

                    </tr>

                `;

            });

            html += `

                    </tbody>

                </table>

                <br>

                <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=tutorados&modo=grupo&grupo='+encodeURIComponent('${grupo}')">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

                <hr>

            `;

        }

        contenido.innerHTML = html;

    });

}
// ======================================
// TUTORES - GENERAL
// ======================================
function cargarTutores() {

    fetch("ajax/tutores.php?modo=general")
        .then(res => res.json())
        .then(data => {

            let html = `
                <h2>TUTORES - GENERAL</h2>

                <table class="tabla">
                    <thead>
                        <tr>
                            <th>No Empleado</th>
                            <th>Nombre</th>
                            <th>Apellido P</th>
                            <th>Apellido M</th>
                            <th>Carrera</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach(t => {

                html += `
                    <tr>
                        <td>${t.no_empleado}</td>
                        <td>${t.nombre}</td>
                        <td>${t.apellido_p}</td>
                        <td>${t.apellido_m}</td>
                        <td>${t.carrera}</td>
                    </tr>
                `;

            });

            html += `
                    </tbody>
                </table>

                <br>

                <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=tutores&modo=general'">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>
            `;

            contenido.innerHTML = html;

        });

}
// ======================================
// TUTORES - POR CARRERA
// ======================================
function cargarTutoresCarrera() {

    fetch("ajax/tutores.php?modo=carrera")
        .then(res => res.json())
        .then(data => {

            let carreras = {};

            data.forEach(t => {

                if (!carreras[t.carrera]) {

                    carreras[t.carrera] = [];

                }

                carreras[t.carrera].push(t);

            });

            let html = `<h2>TUTORES - POR CARRERA</h2>`;

            for (let carrera in carreras) {

                html += `

                    <h3 style="margin-top:35px;color:#0B5ED7;">
                        ${carrera}
                    </h3>

                    <table class="tabla">

                        <thead>

                            <tr>

                                <th>No Empleado</th>
                                <th>Nombre</th>
                                <th>Apellido P</th>
                                <th>Apellido M</th>

                            </tr>

                        </thead>

                        <tbody>
                `;

                carreras[carrera].forEach(t => {

                    html += `

                        <tr>

                            <td>${t.no_empleado}</td>

                            <td>${t.nombre}</td>

                            <td>${t.apellido_p}</td>

                            <td>${t.apellido_m}</td>

                        </tr>

                    `;

                });

                html += `

                        </tbody>

                    </table>

                    <br>

                    <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=tutores&modo=carrera&carrera='+encodeURIComponent('${carrera}')">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

                    <hr>

                `;

            }

            contenido.innerHTML = html;

        });

}
// ======================================
// COORDINADORES - GENERAL
// ======================================

function cargarCoordinadores(){

    fetch("ajax/coordinadores.php?modo=general")

    .then(res=>res.json())

    .then(data=>{

        let html=`

        <h2>COORDINADORES - GENERAL</h2>

        <table class="tabla">

        <thead>

        <tr>

            <th>No Empleado</th>
            <th>Nombre</th>
            <th>Apellido P</th>
            <th>Apellido M</th>
            <th>Carrera</th>

        </tr>

        </thead>

        <tbody>

        `;

        data.forEach(c=>{

            html+=`

            <tr>

                <td>${c.no_empleado}</td>

                <td>${c.nombre}</td>

                <td>${c.apellido_p}</td>

                <td>${c.apellido_m}</td>

                <td>${c.carrera}</td>

            </tr>

            `;

        });

        html+=`

        </tbody>

        </table>

        <br>

        <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=coordinadores&modo=general'">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

        `;

        contenido.innerHTML=html;

    });

}
// ======================================
// COORDINADORES - POR CARRERA
// ======================================

function cargarCoordinadoresCarrera(){

    fetch("ajax/coordinadores.php?modo=carrera")

    .then(res=>res.json())

    .then(data=>{

        let carreras={};

        data.forEach(c=>{

            if(!carreras[c.carrera]){

                carreras[c.carrera]=[];

            }

            carreras[c.carrera].push(c);

        });

        let html="<h2>COORDINADORES POR CARRERA</h2>";

        for(let carrera in carreras){

            html+=`

            <h3 style="margin-top:35px;color:#0B5ED7;">

                ${carrera}

            </h3>

            <table class="tabla">

            <thead>

            <tr>

                <th>No Empleado</th>
                <th>Nombre</th>
                <th>Apellido P</th>
                <th>Apellido M</th>

            </tr>

            </thead>

            <tbody>

            `;

            carreras[carrera].forEach(c=>{

                html+=`

                <tr>

                    <td>${c.no_empleado}</td>

                    <td>${c.nombre}</td>

                    <td>${c.apellido_p}</td>

                    <td>${c.apellido_m}</td>

                </tr>

                `;

            });

            html+=`

            </tbody>

            </table>

            <br>

            <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=coordinadores&modo=carrera&carrera='+encodeURIComponent('${carrera}')">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

            <hr>

            `;

        }

        contenido.innerHTML=html;

    });

}
// ======================================
// JEFES DE CARRERA - GENERAL
// ======================================

function cargarJefes(){

    fetch("ajax/jefes_carrera.php?modo=general")

    .then(res=>res.json())

    .then(data=>{

        let html=`

        <h2>JEFES DE CARRERA - GENERAL</h2>

        <table class="tabla">

        <thead>

        <tr>

            <th>No Empleado</th>
            <th>Nombre</th>
            <th>Apellido P</th>
            <th>Apellido M</th>
            <th>Carrera</th>

        </tr>

        </thead>

        <tbody>

        `;

        data.forEach(j=>{

            html+=`

            <tr>

                <td>${j.no_empleado}</td>

                <td>${j.nombre}</td>

                <td>${j.apellido_p}</td>

                <td>${j.apellido_m}</td>

                <td>${j.carrera}</td>

            </tr>

            `;

        });

        html+=`

        </tbody>

        </table>

        <br>

        <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=jefes&modo=general'">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

        `;

        contenido.innerHTML=html;

    });

}
// ======================================
// JEFES DE CARRERA - POR CARRERA
// ======================================

function cargarJefesCarrera(){

    fetch("ajax/jefes_carrera.php?modo=carrera")

    .then(res=>res.json())

    .then(data=>{

        let carreras={};

        data.forEach(j=>{

            if(!carreras[j.carrera]){

                carreras[j.carrera]=[];

            }

            carreras[j.carrera].push(j);

        });

        let html="<h2>JEFES DE CARRERA POR CARRERA</h2>";

        for(let carrera in carreras){

            html+=`

            <h3 style="margin-top:35px;color:#0B5ED7;">

                ${carrera}

            </h3>

            <table class="tabla">

            <thead>

            <tr>

                <th>No Empleado</th>
                <th>Nombre</th>
                <th>Apellido P</th>
                <th>Apellido M</th>

            </tr>

            </thead>

            <tbody>

            `;

            carreras[carrera].forEach(j=>{

                html+=`

                <tr>

                    <td>${j.no_empleado}</td>

                    <td>${j.nombre}</td>

                    <td>${j.apellido_p}</td>

                    <td>${j.apellido_m}</td>

                </tr>

                `;

            });

            html+=`

            </tbody>

            </table>

            <br>

            <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=jefes&modo=carrera&carrera='+encodeURIComponent('${carrera}')">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

            <hr>

            `;

        }

        contenido.innerHTML=html;

    });

}
// ======================================
// DIRECTIVOS
// ======================================

function cargarDirectivos(){

    fetch("ajax/directivos.php")

    .then(res=>res.json())

    .then(data=>{

        let html=`

        <h2>DIRECTIVOS</h2>

        <table class="tabla">

            <thead>

                <tr>

                    <th>No Empleado</th>

                    <th>Nombre</th>

                    <th>Apellido P</th>

                    <th>Apellido M</th>

                </tr>

            </thead>

            <tbody>

        `;

        data.forEach(d=>{

            html+=`

                <tr>

                    <td>${d.no_empleado}</td>

                    <td>${d.nombre}</td>

                    <td>${d.apellido_p}</td>

                    <td>${d.apellido_m}</td>

                </tr>

            `;

        });

        html+=`

            </tbody>

        </table>

        <br>

        <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=directivos'">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

        `;

        contenido.innerHTML=html;

    });

}
// ======================================
// PSICÓLOGOS
// ======================================

function cargarPsicologos(){

    fetch("ajax/psicologos.php")

    .then(res=>res.json())

    .then(data=>{

        let html=`

        <h2>PSICÓLOGOS</h2>

        <table class="tabla">

            <thead>

                <tr>

                    <th>No Empleado</th>

                    <th>Nombre</th>

                    <th>Apellido P</th>

                    <th>Apellido M</th>

                    <th>Correo Institucional</th>

                </tr>

            </thead>

            <tbody>

        `;

        data.forEach(p=>{

            html+=`

                <tr>

                    <td>${p.no_empleado}</td>

                    <td>${p.nombre}</td>

                    <td>${p.apellido_p}</td>

                    <td>${p.apellido_m}</td>

                    <td>${p.correo_institucional}</td>

                </tr>

            `;

        });

        html+=`

            </tbody>

        </table>

        <br>

        <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=psicologos'">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

        `;

        contenido.innerHTML=html;

    });

}
/*=========================================
    BUSCADOR GENERAL
=========================================*/

const txtBuscar=document.getElementById("txtBuscar");

const btnBuscar=document.getElementById("btnBuscar");

btnBuscar.addEventListener("click",buscarGeneral);

txtBuscar.addEventListener("keypress",function(e){

    if(e.key==="Enter"){

        buscarGeneral();

    }

});

function buscarGeneral(){

    let texto=txtBuscar.value.trim();

    if(texto===""){

        alert("Ingrese un dato para buscar.");

        txtBuscar.focus();

        return;

    }

    fetch("ajax/buscador.php?buscar="+encodeURIComponent(texto))

    .then(res=>res.json())

    .then(data=>{

        mostrarBusqueda(data,texto);

    });

}
/*=========================================
    MOSTRAR RESULTADOS
=========================================*/

function mostrarBusqueda(data,busqueda){

    let html=`

    <h2>

        Resultado de la búsqueda

    </h2>

    <p>

        Búsqueda realizada:

        <strong>${busqueda}</strong>

    </p>

    `;

    if(data.length===0){

        html+=`

        <div class="mensaje">

            <i class="fa-solid fa-circle-xmark"></i>

            <h2>

                No se encontraron resultados

            </h2>

        </div>

        `;

        contenido.innerHTML=html;

        return;

    }

    html+=`

    <table class="tabla">

    <thead>

    <tr>

        <th>Perfil</th>

        <th>Matrícula / Empleado</th>

        <th>Nombre</th>

        <th>Carrera</th>

        <th>Grupo</th>

        <th>Tutor</th>

        <th>Correo</th>

    </tr>

    </thead>

    <tbody>

    `;

    data.forEach(r=>{

        html+=`

        <tr>

            <td>${r.perfil}</td>

            <td>${r.identificador}</td>

            <td>

                ${r.nombre}

                ${r.apellido_p}

                ${r.apellido_m}

            </td>

            <td>${r.carrera}</td>

            <td>${r.grupo}</td>

            <td>${r.tutor}</td>

            <td>${r.correo}</td>

        </tr>

        `;

    });

    html+=`

    </tbody>

    </table>

    <br>

    <button
class="btn-csv"
onclick="window.location='ajax/exportar_csv.php?tipo=busqueda&buscar=${encodeURIComponent(busqueda)}'">

<i class="fa-solid fa-file-csv"></i>

Descargar CSV

</button>

    `;

    contenido.innerHTML=html;

}
});