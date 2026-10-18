<?php
require_once("../sesion.php");
if (!in_array("PSICOLOGO", $_SESSION["roles"])) { header("Location: ../indexloguin.php"); exit(); }
require_once("../base_pit/conect_pit.php");

if($_SERVER['REQUEST_METHOD']=='POST'){
    $matricula = $_POST['matricula'];
    $carrera = $_POST['carrera'];
    $generacion = $_POST['generacion'];
    $semestre = $_POST['semestre'];
    $genero = $_POST['genero'];
    $edad = (int)$_POST['edad'];
    $motivo = $_POST['motivo_aviso'];
    $fam = isset($_POST['requiere_familiar']) ? 1 : 0;
    $dep = isset($_POST['requiere_depresion']) ? 1 : 0;
    $psi = isset($_POST['requiere_psiquiatrico']) ? 1 : 0;

    $sql = "INSERT INTO psico_canalizaciones (matricula,carrera,generacion,semestre,genero,edad,motivo_aviso,requiere_familiar,requiere_depresion,requiere_psiquiatrico,fecha_creacion) VALUES (?,?,?,?,?,?,?,?,?,?,NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisiiii",$matricula,$carrera,$generacion,$semestre,$genero,$edad,$motivo,$fam,$dep,$psi);
    $stmt->execute();
    header("Location: inicio.php"); exit();
}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Nueva Canalización</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#f4f7fc} .card{border-radius:16px; border:none; box-shadow:0 4px 14px rgba(0,0,0,.05)}</style>
</head><body>
<?php include("../includes_pit/sidebar_psicologos.php"); ?>
<div class="separador"></div>
<div class="contenedor-principal" style="padding:20px; max-width:700px; margin:auto">
<a href="inicio.php" class="btn btn-sm btn-light mb-3">← Volver al inicio</a>
<div class="card p-4">
<h5 class="fw-bold">➕ Nueva Canalización</h5>
<p class="text-muted" style="font-size:12px">Agrega un estudiante y se actualizarán las gráficas automáticamente</p>
<form method="POST">
<div class="row g-3">
<div class="col-md-6"><label>Matrícula *</label><input type="text" name="matricula" class="form-control" required></div>
<div class="col-md-6"><label>Carrera *</label>
<select name="carrera" class="form-control" required>
<option value="">-- Selecciona --</option>
<option>Ingeniería Electromecánica</option>
<option>Ingeniería Electrónica</option>
<option>Ingeniería Industrial</option>
<option>Ingeniería Informática</option>
<option>Ingeniería en Sistemas Computacionales</option>
<option>Ingeniería en Administración</option>
</select>
</div>
<div class="col-md-4"><label>Generación</label><input type="text" name="generacion" class="form-control" placeholder="2024-2028"></div>
<div class="col-md-2"><label>Sem</label><input type="number" name="semestre" class="form-control"></div>
<div class="col-md-3"><label>Género</label><select name="genero" class="form-control"><option value="M">M</option><option value="F">F</option><option value="Otro">Otro</option></select></div>
<div class="col-md-3"><label>Edad</label><input type="number" name="edad" class="form-control"></div>
<div class="col-md-12"><label>Motivo</label><select name="motivo_aviso" class="form-control"><option>Bajo rendimiento</option><option>Ansiedad</option><option>Depresión</option><option>Problemas familiares</option><option>Conducta</option></select></div>
<div class="col-md-12">Apoyo: <input type="checkbox" name="requiere_familiar"> Familiar <input type="checkbox" name="requiere_depresion"> Depresión <input type="checkbox" name="requiere_psiquiatrico"> Psiquiátrico</div>
<div class="col-12"><button class="btn btn-primary w-100">Guardar</button></div>
</div>
</form>
</div>
</div>
</body></html>