<?php

include("../../../base_pit/conect_pit.php");

$tipo = $_GET['tipo'] ?? '';
$modo = $_GET['modo'] ?? '';
$id = $_GET['id'] ?? '';
$grupo = $_GET['grupo'] ?? '';

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=".$tipo."_".$modo.".csv");

$output = fopen("php://output","w");

/* UTF8 para Excel */
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

switch($tipo){

    /*=====================================
        DOCENTES
    =====================================*/

    case "docentes":
        $carrera = $_GET['carrera'] ?? '';

        fputcsv($output,[
            "No Empleado",
            "Nombre",
            "Apellido P",
            "Apellido M",
            "Carrera"
        ]);

        if($modo=="carrera" && $carrera!=""){

    $sql="SELECT

            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m,
            pa.carrera

        FROM personal_academico pa

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=pa.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE

            tp.nombre='DOCENTE'

            AND pa.carrera='".$conn->real_escape_string($carrera)."'

        ORDER BY

            pa.nombre";

}else{

    $sql="SELECT

            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m,
            pa.carrera

        FROM personal_academico pa

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=pa.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE tp.nombre='DOCENTE'

        ORDER BY

            pa.carrera,
            pa.nombre";

}

        $res=$conn->query($sql);

        while($fila=$res->fetch_assoc()){

            fputcsv($output,$fila);

        }

    break;
        /*=====================================
        TUTORADOS
        =====================================*/

    case "tutorados":
        $carrera = $_GET['carrera'] ?? '';
        fputcsv($output,[
            "Matrícula",
            "Nombre",
            "Apellido P",
            "Apellido M",
            "Grupo",
            "Carrera",
            "Tutor",
            "No Empleado Tutor"
        ]);

        if($modo=="carrera"){

    if($carrera!=""){

        $sql="SELECT

                t.matricula,
                t.nombre,
                t.apellido_p,
                t.apellido_m,
                t.grupo,
                t.carrera,

                CONCAT(
                    IFNULL(pa.nombre,''),
                    ' ',
                    IFNULL(pa.apellido_p,''),
                    ' ',
                    IFNULL(pa.apellido_m,'')
                ) AS tutor,

                pa.no_empleado

            FROM tutorados t

            LEFT JOIN personal_academico pa
                ON pa.id_usuario=t.id_tutor

            WHERE

                t.activo=1

                AND t.carrera='".$conn->real_escape_string($carrera)."'

            ORDER BY

                t.grupo,
                t.apellido_p,
                t.nombre";

    }else{

        $sql="SELECT

                t.matricula,
                t.nombre,
                t.apellido_p,
                t.apellido_m,
                t.grupo,
                t.carrera,

                CONCAT(
                    IFNULL(pa.nombre,''),
                    ' ',
                    IFNULL(pa.apellido_p,''),
                    ' ',
                    IFNULL(pa.apellido_m,'')
                ) AS tutor,

                pa.no_empleado

            FROM tutorados t

            LEFT JOIN personal_academico pa
                ON pa.id_usuario=t.id_tutor

            WHERE t.activo=1

            ORDER BY

                t.carrera,
                t.grupo,
                t.apellido_p,
                t.nombre";

    }

}

        elseif($modo=="tutor"){

            $sql="SELECT

        t.matricula,
        t.nombre,
        t.apellido_p,
        t.apellido_m,
        t.grupo,
        t.carrera,

        CONCAT(
            pa.nombre,
            ' ',
            pa.apellido_p,
            ' ',
            pa.apellido_m
        ) AS tutor,

        pa.no_empleado

    FROM tutorados t

    INNER JOIN personal_academico pa
        ON pa.id_usuario=t.id_tutor

    WHERE

        t.activo=1

        AND pa.id_usuario=".$id."

    ORDER BY

        t.grupo,
        t.apellido_p,
        t.nombre";

        }

        elseif($modo=="grupo"){

    $sql="SELECT

            t.matricula,
            t.nombre,
            t.apellido_p,
            t.apellido_m,
            t.grupo,
            t.carrera,

            CONCAT(
                IFNULL(pa.nombre,''),
                ' ',
                IFNULL(pa.apellido_p,''),
                ' ',
                IFNULL(pa.apellido_m,'')
            ) AS tutor,

            pa.no_empleado

        FROM tutorados t

        LEFT JOIN personal_academico pa
            ON pa.id_usuario=t.id_tutor

        WHERE

            t.activo=1

            AND t.grupo='".$conn->real_escape_string($grupo)."'

        ORDER BY

            t.apellido_p,
            t.nombre";

}

else{

    $sql="SELECT

            t.matricula,
            t.nombre,
            t.apellido_p,
            t.apellido_m,
            t.grupo,
            t.carrera,

            CONCAT(
                IFNULL(pa.nombre,''),
                ' ',
                IFNULL(pa.apellido_p,''),
                ' ',
                IFNULL(pa.apellido_m,'')
            ) AS tutor,

            pa.no_empleado

        FROM tutorados t

        LEFT JOIN personal_academico pa
            ON pa.id_usuario=t.id_tutor

        WHERE t.activo=1

        ORDER BY

            t.grupo,
            t.apellido_p,
            t.nombre";

}

        $res=$conn->query($sql);

        while($fila=$res->fetch_assoc()){

            fputcsv($output,$fila);

        }

    break;
        /*=====================================
        DIRECTIVOS
        =====================================*/

    case "directivos":

        fputcsv($output,[
            "No Empleado",
            "Nombre",
            "Apellido P",
            "Apellido M"
        ]);

        $sql="SELECT

                d.no_empleado,
                d.nombre,
                d.apellido_p,
                d.apellido_m

            FROM directivos d

            WHERE d.activo=1

            ORDER BY

                d.nombre,
                d.apellido_p";

        $res=$conn->query($sql);

        while($fila=$res->fetch_assoc()){

            fputcsv($output,$fila);

        }

    break;
        /*=====================================
        JEFES DE CARRERA
        =====================================*/

    case "jefes":

        fputcsv($output,[
            "No Empleado",
            "Nombre",
            "Apellido P",
            "Apellido M",
            "Carrera"
        ]);

        if($modo=="carrera" && !empty($_GET['carrera'])){

    $carrera = $conn->real_escape_string($_GET['carrera']);

    $sql="SELECT

            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m,
            pa.carrera

        FROM personal_academico pa

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=pa.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE

            tp.nombre='JEFE_CARRERA'

            AND pa.carrera='$carrera'

        ORDER BY

            pa.nombre";

}else{

    $sql="SELECT

            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m,
            pa.carrera

        FROM personal_academico pa

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=pa.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE

            tp.nombre='JEFE_CARRERA'

        ORDER BY

            pa.carrera,
            pa.nombre";

}

        $res=$conn->query($sql);

        while($fila=$res->fetch_assoc()){

            fputcsv($output,$fila);

        }

    break;
    /*=====================================
    TUTORES
    =====================================*/

    case "tutores":

        fputcsv($output,[
            "No Empleado",
            "Nombre",
            "Apellido P",
            "Apellido M",
            "Carrera"
        ]);

        if($modo=="carrera" && !empty($_GET['carrera'])){

    $carrera = $conn->real_escape_string($_GET['carrera']);

    $sql="SELECT

            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m,
            pa.carrera

        FROM personal_academico pa

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=pa.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE

            tp.nombre='TUTOR'

            AND pa.carrera='$carrera'

        ORDER BY

            pa.nombre";

}else{

    $sql="SELECT

            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m,
            pa.carrera

        FROM personal_academico pa

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=pa.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE

            tp.nombre='TUTOR'

        ORDER BY

            pa.carrera,
            pa.nombre";

}
        $res=$conn->query($sql);

        while($fila=$res->fetch_assoc()){

            fputcsv($output,$fila);

        }

    break;
    /*=====================================
    COORDINADORES
=====================================*/

case "coordinadores":

    fputcsv($output,[
        "No Empleado",
        "Nombre",
        "Apellido P",
        "Apellido M",
        "Carrera"
    ]);

if($modo=="carrera" && !empty($_GET['carrera'])){

    $carrera = $conn->real_escape_string($_GET['carrera']);

    $sql="SELECT

            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m,
            pa.carrera

        FROM personal_academico pa

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=pa.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE

            tp.nombre='COORDINADOR'

            AND pa.carrera='$carrera'

        ORDER BY

            pa.nombre";

}else{

    $sql="SELECT

            pa.no_empleado,
            pa.nombre,
            pa.apellido_p,
            pa.apellido_m,
            pa.carrera

        FROM personal_academico pa

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=pa.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE

            tp.nombre='COORDINADOR'

        ORDER BY

            pa.carrera,
            pa.nombre";

}

    $res=$conn->query($sql);

    while($fila=$res->fetch_assoc()){

        fputcsv($output,$fila);

    }

break;
/*=====================================
    PSICÓLOGOS
=====================================*/

case "psicologos":

    fputcsv($output,[
        "No Empleado",
        "Nombre",
        "Apellido P",
        "Apellido M",
        "Correo Institucional"
    ]);

    $sql="SELECT

            ap.no_empleado,
            ap.nombre,
            ap.apellido_p,
            ap.apellido_m,
            ap.correo_institucional

        FROM administradores_psicologos ap

        INNER JOIN usuario_tipo ut
            ON ut.id_usuario=ap.id_usuario

        INNER JOIN tipos tp
            ON tp.id_tipo=ut.id_tipo

        WHERE tp.nombre='PSICOLOGO'

        ORDER BY

            ap.nombre,
            ap.apellido_p";

    $res=$conn->query($sql);

    while($fila=$res->fetch_assoc()){

        fputcsv($output,$fila);

    }

break;
/*=====================================
    BÚSQUEDA GENERAL
=====================================*/

case "busqueda":

    $buscar = trim($_GET['buscar'] ?? '');

    $buscar = "%".$buscar."%";

    fputcsv($output,[
        "Perfil",
        "Identificador",
        "Nombre",
        "Carrera",
        "Grupo",
        "Tutor",
        "Correo"
    ]);

    $sql="

    SELECT

    'TUTORADO' AS perfil,

    t.matricula AS identificador,

    CONCAT(
        t.nombre,' ',
        t.apellido_p,' ',
        t.apellido_m
    ) AS nombre,

    t.carrera,

    t.grupo,

    CONCAT(
        IFNULL(pa.nombre,''),' ',
        IFNULL(pa.apellido_p,''),' ',
        IFNULL(pa.apellido_m,'')
    ) AS tutor,

    '' AS correo

    FROM tutorados t

    LEFT JOIN personal_academico pa
        ON pa.id_usuario=t.id_tutor

    WHERE

    t.activo=1

    AND(

        t.matricula LIKE ?

        OR t.nombre LIKE ?

        OR t.apellido_p LIKE ?

        OR t.apellido_m LIKE ?

        OR t.carrera LIKE ?

        OR t.grupo LIKE ?

    )

    UNION ALL

    SELECT

    tp.nombre,

    pa.no_empleado,

    CONCAT(
        pa.nombre,' ',
        pa.apellido_p,' ',
        pa.apellido_m
    ),

    pa.carrera,

    '',

    '',

    ''

    FROM personal_academico pa

    INNER JOIN usuario_tipo ut
        ON ut.id_usuario=pa.id_usuario

    INNER JOIN tipos tp
        ON tp.id_tipo=ut.id_tipo

    WHERE

    pa.activo=1

    AND tp.nombre<>'ADMIN'

    AND(

        pa.no_empleado LIKE ?

        OR pa.nombre LIKE ?

        OR pa.apellido_p LIKE ?

        OR pa.apellido_m LIKE ?

        OR pa.carrera LIKE ?

    )

    UNION ALL

    SELECT

    'DIRECTIVO',

    d.no_empleado,

    CONCAT(
        d.nombre,' ',
        d.apellido_p,' ',
        d.apellido_m
    ),

    '',

    '',

    '',

    ''

    FROM directivos d

    WHERE

    d.activo=1

    AND(

        d.no_empleado LIKE ?

        OR d.nombre LIKE ?

        OR d.apellido_p LIKE ?

        OR d.apellido_m LIKE ?

    )

    UNION ALL

    SELECT

    'PSICOLOGO',

    ap.no_empleado,

    CONCAT(
        ap.nombre,' ',
        ap.apellido_p,' ',
        ap.apellido_m
    ),

    '',

    '',

    '',

    ap.correo_institucional

    FROM administradores_psicologos ap

    INNER JOIN usuario_tipo ut
        ON ut.id_usuario=ap.id_usuario

    INNER JOIN tipos tp
        ON tp.id_tipo=ut.id_tipo

    WHERE

    tp.nombre='PSICOLOGO'

    AND ap.activo=1

    AND(

        ap.no_empleado LIKE ?

        OR ap.nombre LIKE ?

        OR ap.apellido_p LIKE ?

        OR ap.apellido_m LIKE ?

        OR ap.correo_institucional LIKE ?

    )

    ORDER BY

    perfil,nombre";

    $stmt=$conn->prepare($sql);

    $stmt->bind_param(

    "ssssssssssssssssssss",

    $buscar,$buscar,$buscar,$buscar,$buscar,$buscar,

    $buscar,$buscar,$buscar,$buscar,$buscar,

    $buscar,$buscar,$buscar,$buscar,

    $buscar,$buscar,$buscar,$buscar,$buscar

    );

    $stmt->execute();

    $res=$stmt->get_result();

    while($fila=$res->fetch_assoc()){

        fputcsv($output,$fila);

    }

break;
}

fclose($output);

exit();

?>ni 