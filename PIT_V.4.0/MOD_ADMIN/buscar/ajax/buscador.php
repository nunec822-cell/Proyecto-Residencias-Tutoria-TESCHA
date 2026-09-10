<?php

include("../../../base_pit/conect_pit.php");

header('Content-Type: application/json');

$buscar = trim($_GET['buscar'] ?? '');

if($buscar==""){

    echo json_encode([]);
    exit();

}

$buscar = "%".$buscar."%";

$sql = "

/*=========================================================
TUTORADOS
=========================================================*/

SELECT

'TUTORADO' AS perfil,

t.matricula AS identificador,

t.nombre,

t.apellido_p,

t.apellido_m,

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

/*=========================================================
PERSONAL ACADÉMICO
=========================================================*/

SELECT

tp.nombre AS perfil,

pa.no_empleado,

pa.nombre,

pa.apellido_p,

pa.apellido_m,

pa.carrera,

'' AS grupo,

'' AS tutor,

'' AS correo

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

/*=========================================================
DIRECTIVOS
=========================================================*/

SELECT

'DIRECTIVO',

d.no_empleado,

d.nombre,

d.apellido_p,

d.apellido_m,

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

/*=========================================================
PSICÓLOGOS
=========================================================*/

SELECT

'PSICOLOGO',

ap.no_empleado,

ap.nombre,

ap.apellido_p,

ap.apellido_m,

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

perfil,

apellido_p,

nombre

";

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

$datos=[];

while($fila=$res->fetch_assoc()){

    $datos[]=$fila;

}

echo json_encode($datos);

?>