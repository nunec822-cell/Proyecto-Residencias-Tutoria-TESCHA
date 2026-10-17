/*=========================================================
    CANALIZACIONES POR CARRERA
    JEFE DE CARRERA
    SIST V.4.0 - PIT V.4.0
=========================================================*/

document.addEventListener("DOMContentLoaded", function () {

    /*=====================================================
        BUSCADOR DE GRUPOS
    =====================================================*/

    const buscarGrupo = document.getElementById("buscarGrupo");
    const gruposGrid = document.getElementById("gruposGrid");
    const sinResultados = document.getElementById("sinResultados");

    if (buscarGrupo && gruposGrid) {

        const grupos = gruposGrid.querySelectorAll(".grupo-card");

        buscarGrupo.addEventListener("input", function () {

            const texto = normalizarTexto(this.value);

            let encontrados = 0;

            grupos.forEach(function (grupo) {

                const nombreGrupo =
                    normalizarTexto(
                        grupo.getAttribute("data-grupo") || ""
                    );

                if (
                    texto === "" ||
                    nombreGrupo.includes(texto)
                ) {

                    grupo.style.display = "";

                    encontrados++;

                } else {

                    grupo.style.display = "none";
                }

            });


            if (sinResultados) {

                if (encontrados === 0 && texto !== "") {
                    sinResultados.classList.add("visible");
                } else {
                    sinResultados.classList.remove("visible");
                }

            }

        });

    }


    /*=====================================================
        BUSCADOR DE ALUMNOS
    =====================================================*/

    const buscarAlumno =
        document.getElementById("buscarAlumno");

    const tablaAlumnos =
        document.getElementById("tablaAlumnos");

    const sinResultadosAlumnos =
        document.getElementById("sinResultadosAlumnos");

    if (buscarAlumno && tablaAlumnos) {

        const filas =
            tablaAlumnos.querySelectorAll(".fila-alumno");

        buscarAlumno.addEventListener("input", function () {

            const texto =
                normalizarTexto(this.value);

            let encontrados = 0;

            filas.forEach(function (fila) {

                const busqueda =
                    normalizarTexto(
                        fila.getAttribute("data-busqueda") || ""
                    );

                if (
                    texto === "" ||
                    busqueda.includes(texto)
                ) {

                    fila.style.display = "";

                    encontrados++;

                } else {

                    fila.style.display = "none";
                }

            });


            if (sinResultadosAlumnos) {

                if (
                    encontrados === 0 &&
                    texto !== ""
                ) {

                    sinResultadosAlumnos.classList.add(
                        "visible"
                    );

                } else {

                    sinResultadosAlumnos.classList.remove(
                        "visible"
                    );
                }

            }

        });

    }


    /*=====================================================
        NORMALIZAR TEXTO
    =====================================================*/

    function normalizarTexto(texto) {

        return texto
            .toString()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();

    }


    /*=====================================================
        EVITAR DOBLE CLIC EN TARJETAS
    =====================================================*/

    const tarjetasGrupo =
        document.querySelectorAll(".grupo-card");

    tarjetasGrupo.forEach(function (tarjeta) {

        tarjeta.addEventListener("click", function () {

            tarjeta.classList.add("cargando");

        });

    });


    /*=====================================================
        ANIMACIÓN SUAVE DE ENTRADA
    =====================================================*/

    const elementosAnimados =
        document.querySelectorAll(
            ".resumen-card, .grupo-card, .tabla-contenedor"
        );

    elementosAnimados.forEach(function (elemento, indice) {

        elemento.style.opacity = "0";
        elemento.style.transform = "translateY(8px)";

        setTimeout(function () {

            elemento.style.transition =
                "opacity .35s ease, transform .35s ease";

            elemento.style.opacity = "1";
            elemento.style.transform = "translateY(0)";

        }, indice * 40);

    });

});