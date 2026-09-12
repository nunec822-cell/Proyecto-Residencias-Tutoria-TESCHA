<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pagina_actual = $_SERVER['PHP_SELF'];
$base_url = "/SIST V.4.0/PIT_V.4.0";
?>
<header class="topbar-directivos">
    <div class="topbar-left">
        <button type="button" id="btnSidebar" class="btn-menu" aria-label="Abrir menú">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-logo">
            <div class="logo-icon">
                <i class="fas fa-building-columns"></i>
            </div>
            <div>
                <h2>Dirección Institucional</h2>
                <span>Portal Institucional de Tutorías</span>
            </div>
        </div>
    </div>
    <div class="topbar-right">

        <!-- CAMPANA GLOBAL -->
        <div id="campanaGlobal" style="position:relative; cursor:pointer; margin-right:18px;">
          <i class="fas fa-bell" style="font-size:22px; color:#333;"></i>
          <span id="contadorNotif" style="position:absolute; top:-8px; right:-8px; background:red; color:white; font-size:11px; padding:2px 6px; border-radius:50%; display:none;">0</span>
          <div id="listaNotif" style="display:none; position:absolute; right:0; top:32px; background:white; width:320px; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,0.3); z-index:9999; max-height:400px; overflow-y:auto; color:#333; text-align:left;"></div>
        </div>

        <div class="usuario-info">
            <i class="fas fa-user-tie"></i>
            <span>
                <?php echo isset($_SESSION["usuario"]) ? htmlspecialchars($_SESSION["usuario"]) : "Directivo"; ?>
            </span>
        </div>
        <a href="<?= $base_url ?>/cerrar_sesion.php" class="btn-salir">
            <i class="fas fa-right-from-bracket"></i>
            Cerrar sesión
        </a>
    </div>
</header>
<div id="sidebarOverlay" class="sidebar-overlay"></div>
<aside id="sidebarDirectivos" class="sidebar-directivos">
    <div class="sidebar-header">
        <div class="sidebar-avatar">
            <i class="fas fa-building-columns"></i>
        </div>
        <div>
            <h3>Directivos</h3>
            <span>Panel Principal</span>
        </div>
    </div>
    <nav class="sidebar-menu">
        <ul>
            <li>
                <a href="<?= $base_url ?>/MOD_DIRECTIVOS/inicio.php" class="<?= (basename($pagina_actual) === 'inicio.php' && strpos($pagina_actual,'/MOD_DIRECTIVOS/canalizacionesgenerales/') === false) ? 'activo' : ''; ?>">
                    <i class="fas fa-house"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li>
                <a href="<?= $base_url ?>/MOD_DIRECTIVOS/canalizacionesgenerales/index.php" class="<?= (strpos($pagina_actual,'/MOD_DIRECTIVOS/canalizacionesgenerales/') !== false) ? 'activo' : ''; ?>">
                    <i class="fas fa-user-graduate"></i>
                    <span>Canalizaciones de alumnos académicas</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <i class="fas fa-landmark"></i>
        <div>
            <strong>Dirección Institucional</strong>
            <small>TESCHA</small>
        </div>
    </div>
</aside>
<link rel="stylesheet" href="<?= $base_url ?>/includes_pit/sidebar_directivos.css">
<script src="<?= $base_url ?>/includes_pit/sidebar_directivos.js"></script>
<script>
function cargarNotificaciones(){
  fetch('<?= $base_url ?>/MOD_NOTIFICACIONES/ajax_notificaciones.php')
  .then(r=>r.json())
  .then(data=>{
    const cont = document.getElementById('contadorNotif');
    const lista = document.getElementById('listaNotif');
    if(data.conteo > 0){
      cont.style.display='block';
      cont.innerText = data.conteo;
    } else {
      cont.style.display='none';
    }
    let html = '';
    if(data.todas && data.todas.length > 0){
      data.todas.forEach(n=>{
        html += `<div style="padding:12px; border-bottom:1px solid #eee; cursor:pointer;" onclick="marcarLeida(${n.id})">
          <b>${n.titulo}</b><br>
          <small>${n.mensaje}</small><br>
          <small style="color:gray;">${n.fecha_creacion || ''}</small>
        </div>`;
      });
    } else {
      html = '<div style="padding:15px; text-align:center;">Sin notificaciones</div>';
    }
    lista.innerHTML = html;
  });
}
function marcarLeida(id){
  let fd = new FormData();
  fd.append('accion','marcar');
  fd.append('id',id);
  fetch('<?= $base_url ?>/MOD_NOTIFICACIONES/ajax_notificaciones.php',{method:'POST',body:fd})
  .then(()=>cargarNotificaciones());
}
document.getElementById('campanaGlobal').onclick = function(){
  let lista = document.getElementById('listaNotif');
  lista.style.display = lista.style.display=='none' || lista.style.display=='' ?'block':'none';
}
cargarNotificaciones();
setInterval(cargarNotificaciones, 10000);
</script>