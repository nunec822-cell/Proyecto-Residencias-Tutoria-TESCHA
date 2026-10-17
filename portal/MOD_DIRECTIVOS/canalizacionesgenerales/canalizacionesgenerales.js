/*=========================================================
    CANALIZACIONES GENERALES
    DIRECTIVOS
    SIST V.4.0 - PIT V.4.0
=========================================================*/

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /*=================================================
            BUSCADOR DE GRUPOS
        =================================================*/

        const buscarGrupo =
            document.getElementById("buscarGrupo");

        const grupos =
            document.querySelectorAll(".grupo-card");

        const sinResultados =
            document.getElementById("sinResultados");


        if (buscarGrupo) {

            buscarGrupo.addEventListener(
                "input",
                function () {

                    const texto =
                        this.value
                            .toLowerCase()
                            .trim();

                    let encontrados = 0;


                    grupos.forEach(
                        function (grupo) {

                            const nombre =
                                grupo.dataset.grupo
                                || "";

                            if (
                                nombre.includes(texto)
                            ) {

                                grupo.style.display =
                                    "";

                                encontrados++;

                            } else {

                                grupo.style.display =
                                    "none";

                            }

                        }
                    );


                    if (sinResultados) {

                        if (
                            encontrados === 0 &&
                            texto !== ""
                        ) {

                            sinResultados.classList.add(
                                "mostrar"
                            );

                        } else {

                            sinResultados.classList.remove(
                                "mostrar"
                            );

                        }

                    }

                }
            );

        }



        /*=================================================
            BUSCADOR DE ALUMNOS
        =================================================*/

        const buscarAlumno =
            document.getElementById("buscarAlumno");

        const alumnos =
            document.querySelectorAll(".alumno-card");

        const sinResultadosAlumnos =
            document.getElementById(
                "sinResultadosAlumnos"
            );


        if (buscarAlumno) {

            buscarAlumno.addEventListener(
                "input",
                function () {

                    const texto =
                        this.value
                            .toLowerCase()
                            .trim();

                    let encontrados = 0;


                    alumnos.forEach(
                        function (alumno) {

                            const datos =
                                alumno.dataset.alumno
                                || "";

                            if (
                                datos.includes(texto)
                            ) {

                                alumno.style.display =
                                    "";

                                encontrados++;

                            } else {

                                alumno.style.display =
                                    "none";

                            }

                        }
                    );


                    if (sinResultadosAlumnos) {

                        if (
                            encontrados === 0 &&
                            texto !== ""
                        ) {

                            sinResultadosAlumnos.classList.add(
                                "mostrar"
                            );

                        } else {

                            sinResultadosAlumnos.classList.remove(
                                "mostrar"
                            );

                        }

                    }

                }
            );

        }


    }
);