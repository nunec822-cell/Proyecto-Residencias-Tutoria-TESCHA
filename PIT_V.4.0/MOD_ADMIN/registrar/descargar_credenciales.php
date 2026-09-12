<?php

$archivo =
"../../archivos/credenciales_usuarios.csv";

if(!file_exists($archivo))
{
    die(
        "No existe el archivo de credenciales."
    );
}

header('Content-Type: text/csv');
header(
'Content-Disposition: attachment; filename="credenciales_usuarios.csv"'
);

header('Pragma: no-cache');
header('Expires: 0');

readfile($archivo);

exit();

?>