<?php

include("../base_sist/conect_sist.php");

$sql="
SELECT *
FROM modelo_educativo
LIMIT 1
";

$res=mysqli_query($conexion,$sql);

$modelo=mysqli_fetch_assoc($res);

?>

<!DOCTYPE html>
<html>
<head>

<title>Modelo Educativo</title>

<style>

body{
    margin:0;
}

iframe{
    width:100%;
    height:100vh;
    border:none;
}

</style>

</head>

<body>

<iframe
src="images/<?php echo $modelo['pdf']; ?>"
>
</iframe>

</body>
</html>