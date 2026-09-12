<?php

include("../base_sist/conect_sist.php");

$sql="
SELECT *
FROM modelo_educativo
LIMIT 1
";

$res=mysqli_query($conexion,$sql);

$modelo=mysqli_fetch_assoc($res);

/* SUMAR DESCARGA */

mysqli_query(
    $conexion,
    "
    UPDATE modelo_educativo
    SET descargas = descargas + 1
    "
);

$archivo="images/".$modelo['pdf'];

header('Content-Type: application/pdf');
header(
'Content-Disposition: attachment; filename="'.basename($archivo).'"'
);

readfile($archivo);

exit;