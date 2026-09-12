/*=========================================
    ELEMENTOS
=========================================*/

const tipoUsuario = document.getElementById("tipo_usuario");

const datosComunes = document.getElementById("datos_comunes");

const formDocente = document.getElementById("form_docente");
const formJefe = document.getElementById("form_jefe");
const formDirectivo = document.getElementById("form_directivo");
const formPsicologo = document.getElementById("form_psicologo");
const formTutorado = document.getElementById("form_tutorado");

const password = document.getElementById("password");
const confirmarPassword = document.getElementById("confirmar_password");

const fortaleza = document.getElementById("fortaleza");
const mensajeCoincide = document.getElementById("mensajeCoincide");


/*=========================================
    OCULTAR TODOS
=========================================*/

function ocultarFormularios()
{
    formDocente.style.display = "none";
    formJefe.style.display = "none";
    formDirectivo.style.display = "none";
    formPsicologo.style.display = "none";
    formTutorado.style.display = "none";

    formDocente.classList.remove("fadeIn");
    formJefe.classList.remove("fadeIn");
    formDirectivo.classList.remove("fadeIn");
    formPsicologo.classList.remove("fadeIn");
    formTutorado.classList.remove("fadeIn");
}


/*=========================================
    CAMBIO DE TIPO
=========================================*/

tipoUsuario.addEventListener("change", function()
{
    ocultarFormularios();

    if(this.value === "")
    {
        datosComunes.style.display = "none";
        return;
    }

    datosComunes.style.display = "block";

    switch(this.value)
    {
        case "DOCENTE":

            formDocente.style.display = "block";
            formDocente.classList.add("fadeIn");

        break;

        case "JEFE_CARRERA":

            formJefe.style.display = "block";
            formJefe.classList.add("fadeIn");

        break;

        case "DIRECTIVO":

            formDirectivo.style.display = "block";
            formDirectivo.classList.add("fadeIn");

        break;

        case "PSICOLOGO":

            formPsicologo.style.display = "block";
            formPsicologo.classList.add("fadeIn");

        break;

        case "TUTORADO":

            formTutorado.style.display = "block";
            formTutorado.classList.add("fadeIn");

        break;
    }

});


/*=========================================
    FORTALEZA CONTRASEÑA
=========================================*/

password.addEventListener("keyup", function()
{
    let valor = this.value;

    let tieneMayuscula = /[A-Z]/.test(valor);
    let tieneMinuscula = /[a-z]/.test(valor);
    let tieneNumero = /[0-9]/.test(valor);
    let longitud = valor.length >= 8;

    let puntos = 0;

    if(tieneMayuscula) puntos++;
    if(tieneMinuscula) puntos++;
    if(tieneNumero) puntos++;
    if(longitud) puntos++;

    fortaleza.classList.remove(
        "debil",
        "media",
        "fuerte"
    );

    if(valor.length === 0)
    {
        fortaleza.innerHTML =
            "Fortaleza de la contraseña";

        return;
    }

    if(puntos <= 2)
    {
        fortaleza.classList.add("debil");

        fortaleza.innerHTML =
            "█░░░░ Débil";
    }
    else if(puntos === 3)
    {
        fortaleza.classList.add("media");

        fortaleza.innerHTML =
            "███░░ Media";
    }
    else
    {
        fortaleza.classList.add("fuerte");

        fortaleza.innerHTML =
            "█████ Fuerte";
    }

});


/*=========================================
    CONFIRMAR CONTRASEÑA
=========================================*/

function validarCoincidencia()
{
    let pass1 = password.value;
    let pass2 = confirmarPassword.value;

    mensajeCoincide.classList.remove(
        "correcto",
        "incorrecto"
    );

    if(pass2.length === 0)
    {
        mensajeCoincide.innerHTML = "";
        return;
    }

    if(pass1 === pass2)
    {
        mensajeCoincide.classList.add("correcto");

        mensajeCoincide.innerHTML =
            "✓ Las contraseñas coinciden";
    }
    else
    {
        mensajeCoincide.classList.add("incorrecto");

        mensajeCoincide.innerHTML =
            "✗ Las contraseñas no coinciden";
    }
}

password.addEventListener(
    "keyup",
    validarCoincidencia
);

confirmarPassword.addEventListener(
    "keyup",
    validarCoincidencia
);


/*=========================================
    VALIDAR ANTES DE ENVIAR
=========================================*/

document
.getElementById("formRegistro")
.addEventListener("submit", function(e)
{
    let pass = password.value;

    let tieneMayuscula = /[A-Z]/.test(pass);
    let tieneMinuscula = /[a-z]/.test(pass);
    let tieneNumero = /[0-9]/.test(pass);
    let longitud = pass.length >= 8;

    if(
        !tieneMayuscula ||
        !tieneMinuscula ||
        !tieneNumero ||
        !longitud
    )
    {
        e.preventDefault();

        alert(
            "La contraseña debe contener:\n\n" +
            "✓ Al menos 8 caracteres\n" +
            "✓ Una letra mayúscula\n" +
            "✓ Una letra minúscula\n" +
            "✓ Un número"
        );

        return;
    }

    if(
        password.value !==
        confirmarPassword.value
    )
    {
        e.preventDefault();

        alert(
            "Las contraseñas no coinciden."
        );

        return;
    }
});


/*=========================================
    OCULTAR ALERTA
=========================================*/

setTimeout(() => {

    const alerta =
        document.querySelector(".alerta-exito, .mensaje-error");

    if(alerta){

        alerta.style.opacity = "0";

        setTimeout(() => {

            alerta.remove();

        },500);

    }

},4000);
/*=========================================
VISTA PREVIA
=========================================*/

const btnPreview =
document.getElementById("btnVistaPrevia");

if(btnPreview){

btnPreview.addEventListener("click",()=>{

    let tipo =
    document.getElementById(
        "tipo_usuario"
    ).value;

    let usuario =
    document.querySelector(
        '[name="usuario"]'
    ).value;

    let pass =
    document.querySelector(
        '[name="password"]'
    ).value;

    let estado =
    document.querySelector(
        '[name="activo"]'
    ).value == "1"
    ? "ACTIVO"
    : "INACTIVO";

    let html = `
        <h2>Vista Previa del Registro</h2>
        <hr>

        <div class="preview-item">
            <strong>Tipo:</strong> ${tipo}
        </div>

        <div class="preview-item">
            <strong>Usuario:</strong> ${usuario}
        </div>

        <div class="preview-item">
            <strong>Contraseña:</strong> ${pass}
        </div>

        <div class="preview-item">
            <strong>Estado:</strong> ${estado}
        </div>
    `;

    /*=====================================
    DOCENTE
    =====================================*/

    if(tipo === "DOCENTE"){

        let roles = [];

        document
        .querySelectorAll(
            'input[name="roles[]"]:checked'
        )
        .forEach((item)=>{

            roles.push(item.value);

        });

        html += `

        <hr>

        <div class="preview-item">
            <strong>No. Empleado:</strong>
            ${
            document.querySelector(
            '[name="doc_no_empleado"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Nombre:</strong>
            ${
            document.querySelector(
            '[name="doc_nombre"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="doc_apellido_p"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="doc_apellido_m"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Carrera:</strong>
            ${
            document.querySelector(
            '[name="doc_carrera"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Roles:</strong>
            DOCENTE
            ${
            roles.length
            ? ", " + roles.join(", ")
            : ""
            }
        </div>

        `;
    }

    /*=====================================
    JEFE DE CARRERA
    =====================================*/

    if(tipo === "JEFE_CARRERA"){

        html += `

        <hr>

        <div class="preview-item">
            <strong>No. Empleado:</strong>
            ${
            document.querySelector(
            '[name="jefe_no_empleado"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Nombre:</strong>
            ${
            document.querySelector(
            '[name="jefe_nombre"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="jefe_apellido_p"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="jefe_apellido_m"]'
            ).value
            }
        </div>

        `;
    }

    /*=====================================
    DIRECTIVO
    =====================================*/

    if(tipo === "DIRECTIVO"){

        html += `

        <hr>

        <div class="preview-item">
            <strong>No. Empleado:</strong>
            ${
            document.querySelector(
            '[name="dir_no_empleado"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Nombre:</strong>
            ${
            document.querySelector(
            '[name="dir_nombre"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="dir_apellido_p"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="dir_apellido_m"]'
            ).value
            }
        </div>

        `;
    }

    /*=====================================
    PSICOLOGO
    =====================================*/

    if(tipo === "PSICOLOGO"){

        html += `

        <hr>

        <div class="preview-item">
            <strong>No. Empleado:</strong>
            ${
            document.querySelector(
            '[name="psi_no_empleado"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Nombre:</strong>
            ${
            document.querySelector(
            '[name="psi_nombre"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="psi_apellido_p"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="psi_apellido_m"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Correo:</strong>
            ${
            document.querySelector(
            '[name="correo_institucional"]'
            ).value
            }
        </div>

        `;
    }

    /*=====================================
    TUTORADO
    =====================================*/

    if(tipo === "TUTORADO"){

        let tutorSelect =
        document.querySelector(
            '[name="id_tutor"]'
        );

        let tutorNombre =
        tutorSelect.options[
            tutorSelect.selectedIndex
        ].text;

        html += `

        <hr>

        <div class="preview-item">
            <strong>Matrícula:</strong>
            ${
            document.querySelector(
            '[name="matricula"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Nombre:</strong>
            ${
            document.querySelector(
            '[name="alu_nombre"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="alu_apellido_p"]'
            ).value
            }
            ${
            document.querySelector(
            '[name="alu_apellido_m"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Carrera:</strong>
            ${
            document.querySelector(
            '[name="alu_carrera"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Grupo:</strong>
            ${
            document.querySelector(
            '[name="grupo"]'
            ).value
            }
        </div>

        <div class="preview-item">
            <strong>Tutor:</strong>
            ${tutorNombre}
        </div>

        `;
    }

    document.getElementById(
        "contenidoPreview"
    ).innerHTML = html;

    document.getElementById(
        "modalPreview"
    ).style.display = "flex";

});

}

/*=========================================
CERRAR PREVIEW
=========================================*/

document
.getElementById("cerrarPreview")
.addEventListener("click",()=>{

    document.getElementById(
        "modalPreview"
    ).style.display = "none";

});

/*=========================================
ACEPTAR PREVIEW
=========================================*/

document
.getElementById("aceptarPreview")
.addEventListener("click",()=>{

    document.getElementById(
        "modalPreview"
    ).style.display = "none";

    document.getElementById(
        "btnConfirmar"
    ).click();

});

/*=========================================
MOSTRAR BOTÓN VISTA PREVIA
=========================================*/

if(tipoUsuario){

tipoUsuario.addEventListener("change",()=>{

    const btn =
    document.getElementById(
        "btnVistaPrevia"
    );

    if(tipoUsuario.value !== ""){

        btn.style.display =
        "inline-block";

    }else{

        btn.style.display =
        "none";

    }

});

}
const carrera = document.getElementById("alu_carrera");

if(carrera){

    carrera.addEventListener("change", function(){

        fetch(
            "obtener_tutores.php?carrera=" +
            encodeURIComponent(this.value)
        )

        .then(response => response.json())

        .then(data => {

            let select =
            document.getElementById("id_tutor");

            select.innerHTML =
            '<option value="">Seleccione Tutor</option>';

            data.forEach(function(tutor){

                select.innerHTML += `
                    <option value="${tutor.id_usuario}">
                        ${tutor.tutor}
                    </option>
                `;

            });

        });

    });

}