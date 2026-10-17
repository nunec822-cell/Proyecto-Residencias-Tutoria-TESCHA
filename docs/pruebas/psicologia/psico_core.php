<?php
$_SERVER['DOCUMENT_ROOT']='/home/claude/sist_v4'; $_SERVER['HTTP_HOST']='localhost';
if (getenv('OFF')) { mysqli_report(MYSQLI_REPORT_OFF); }
require '/home/claude/sist_v4/sist/core/bootstrap.php';
if (getenv('OFF')) { mysqli_report(MYSQLI_REPORT_OFF); }
$c = sist_conectar('pit'); $f=0;
function t($n,$ok,$x=''){ global $f; echo ($ok?'  ✓ ':'  ✗ ').$n.($ok?'':' '.json_encode($x,JSON_UNESCAPED_UNICODE))."\n"; if(!$ok)$f++; }
$c->query("DELETE FROM psico_canalizaciones"); $c->query("DELETE FROM notificaciones"); $c->query("DELETE FROM correos_pendientes");
$JORGE=(int)sist_db_valor($c,"select id_tutorado from tutorados where id_usuario=5");
$MONTSE=(int)sist_db_valor($c,"select id_tutorado from tutorados where id_usuario=19");
t('listo()', sist_psico_listo($c));
$r=sist_psico_avisar($c,7,$JORGE,'Llora en clase SECRETO-AVISO'); t('profesor 7 avisa', $r['ok'], $r); $id=$r['id']??0;
$r=sist_psico_avisar($c,7,$JORGE,'otra vez'); t('aviso duplicado rechazado', !$r['ok']);
$r=sist_psico_avisar($c,17,$JORGE,'ajeno'); t('profesor ajeno rechazado', !$r['ok']);
$r=sist_psico_canalizar($c,7,$id,0,'motivo'); t('otro tutor no canaliza el aviso', !$r['ok'], $r);
$r=sist_psico_canalizar($c,6,$id,0,'  '); t('motivo vacío rechazado', !$r['ok']);
$r=sist_psico_canalizar($c,6,$id,0,'Ansiedad SECRETO-MOTIVO'); t('tutor canaliza el aviso', $r['ok'], $r);
$r=sist_psico_canalizar($c,6,$id,0,'otra vez'); t('doble canalización rechazada', !$r['ok']);
$r=sist_psico_canalizar($c,6,0,$MONTSE,'no es mío'); t('alumno de otro tutor rechazado', !$r['ok']);
$por=[]; foreach(sist_db_filas($c,"select id_usuario,mensaje from notificaciones where tipo='PSICO_CANALIZADA' order by id_usuario") as $x){ $por[$x['id_usuario']]=$x['mensaje']; }
$psis=sist_usuarios_por_rol($c,'PSICOLOGO'); $esp=array_unique(array_merge([2,5,7,16],$psis)); sort($esp);
t('avisó a alumno, TODOS los psicólogos, admin, jefe y profesor', array_keys($por)==array_values($esp), [array_keys($por),$esp]);
t('ningún aviso contiene el motivo', !array_filter($por,fn($m)=>strpos($m,'SECRETO')!==false));
t('ningún correo contiene el motivo', (int)sist_db_valor($c,"select count(*) from correos_pendientes where cuerpo_texto like '%SECRETO%' or cuerpo_html like '%SECRETO%'")===0);
t('aún sin psicólogo asignado', sist_db_valor($c,"select id_psicologo from psico_canalizaciones where id_psico=$id")===null);
$pool=sist_psico_listar($c,'psicologo',['estado'=>'CANALIZADA','sin_psicologo'=>1],50,0)['filas']; t('aparece en casos por tomar', count($pool)==1);
// programar sin tomar
$man=date('Y-m-d',strtotime('+1 day'));
$r=sist_psico_programar_sesion($c,4,$id,$man,'10:00','11:00','Cubículo'); t('no se programa sin tomar el caso', !$r['ok'], $r);
// tomar
$r=sist_psico_tomar($c,6,$id); t('quien no es psicólogo no toma', !$r['ok']);
$r=sist_psico_tomar($c,4,$id); t('psicólogo 4 toma el caso', $r['ok'], $r);
$r=sist_psico_tomar($c,23,$id); t('segundo psicólogo no puede (ya tomado)', !$r['ok'], $r);
t('responsable = 4', (int)sist_db_valor($c,"select id_psicologo from psico_canalizaciones where id_psico=$id")===4);
$n0=(int)sist_db_valor($c,"select count(*) from notificaciones");
// programar validaciones
$r=sist_psico_programar_sesion($c,23,$id,$man,'10:00','11:00','Cubículo'); t('otro psicólogo no programa', !$r['ok']);
$r=sist_psico_programar_sesion($c,4,$id,date('Y-m-d',strtotime('-1 day')),'10:00','11:00','Cubículo'); t('fecha pasada rechazada', !$r['ok']);
$r=sist_psico_programar_sesion($c,4,$id,'2026-02-30','10:00','11:00','Cubículo'); t('fecha inválida rechazada', !$r['ok']);
$r=sist_psico_programar_sesion($c,4,$id,$man,'11:00','10:00','Cubículo'); t('fin antes de inicio rechazado', !$r['ok']);
$r=sist_psico_programar_sesion($c,4,$id,$man,'25:00','26:00','Cubículo'); t('hora inválida rechazada', !$r['ok']);
$r=sist_psico_programar_sesion($c,4,$id,$man,'10:00','11:00','  '); t('sin lugar rechazado', !$r['ok']);
$r=sist_psico_programar_sesion($c,4,$id,$man,'10:00','11:00','Cubículo de psicología'); t('programa sesión', $r['ok'], $r);
$r=sist_psico_programar_sesion($c,4,$id,$man,'10:30','11:30','Otro'); t('empalme rechazado', !$r['ok'], $r);
$r=sist_psico_programar_sesion($c,4,$id,$man,'11:00','12:00','Cubículo'); t('sesión contigua permitida', $r['ok'], $r);
$m=sist_db_filas($c,"select id_usuario,mensaje from notificaciones where tipo='PSICO_SESION' order by id_notificacion");
t('avisó a alumno(5) y tutor(6) con fecha, hora y lugar', count($m)==4 && strpos($m[0]['mensaje'],'10:00')!==false && strpos($m[0]['mensaje'],'Cubículo de psicología')!==false && in_array((int)$m[0]['id_usuario'],[5,6]), $m);
t('avisos de sesión no llevan motivo', !array_filter($m,fn($x)=>strpos($x['mensaje'],'SECRETO')!==false));
$ses=sist_db_filas($c,"select id_sesion from psico_sesiones order by id_sesion"); $s1=(int)$ses[0]['id_sesion']; $s2=(int)$ses[1]['id_sesion'];
// cerrar
$r=sist_psico_cerrar_sesion($c,4,$s1,'REALIZADA','Asistió','nota'); t('sesión futura no se marca realizada', !$r['ok']);
$r=sist_psico_cerrar_sesion($c,23,$s1,'CANCELADA','x',''); t('otro psicólogo no cierra', !$r['ok']);
$r=sist_psico_cerrar_sesion($c,4,$s1,'CANCELADA','',''); t('observación obligatoria', !$r['ok']);
$r=sist_psico_cerrar_sesion($c,4,$s1,'CANCELADA','Reprogramo','nota'); t('nota privada solo en realizada', !$r['ok']);
$r=sist_psico_cerrar_sesion($c,4,$s1,'RARO','x',''); t('estado inválido rechazado', !$r['ok']);
$r=sist_psico_cerrar_sesion($c,4,$s1,'CANCELADA','Se reprograma',''); t('cancela sesión', $r['ok'], $r);
$r=sist_psico_cerrar_sesion($c,4,$s1,'CANCELADA','otra vez',''); t('no se cierra dos veces', !$r['ok']);
t('cancelación avisó a alumno y tutor', (int)sist_db_valor($c,"select count(*) from notificaciones where titulo like 'Se canceló%'")==2);
$r=sist_psico_concluir($c,4,$id); t('no concluye con sesión programada pendiente', !$r['ok'], $r);
// sesión de hoy para realizada: insertar directo
$hoy=date('Y-m-d'); $c->query("INSERT INTO psico_sesiones (id_psico,id_psicologo,fecha,hora_inicio,hora_fin,lugar) VALUES ($id,4,'$hoy','08:00','09:00','Sala')"); $s3=(int)$c->insert_id;
$r=sist_psico_cerrar_sesion($c,4,$s3,'REALIZADA','Asistió; continúa el proceso','Paciente refiere ansiedad SECRETO-NOTA'); t('cierra realizada con nota privada', $r['ok'], $r);
$r=sist_psico_cerrar_sesion($c,4,$s2,'CANCELADA','No se llevará a cabo',''); t('cancela la otra', $r['ok'], $r);
t('nota privada la lee el psicólogo 4', count(sist_psico_notas_privadas($c,$id,4))==1);
t('nota privada NO la lee el 23', count(sist_psico_notas_privadas($c,$id,23))==0);
$h=sist_psico_historial($c,$id); t('historial 3 sesiones', count($h)==3);
t('historial sin notas ni motivos', strpos(json_encode($h),'SECRETO')===false && !array_key_exists('nota',$h[0]) && !array_key_exists('motivo',$h[0]));
foreach(['publico'] as $pf){ $l=sist_psico_listar($c,$pf,['id'=>$id],5,0)['filas']; t('listado público sin motivos', strpos(json_encode($l),'SECRETO')===false && !array_key_exists('motivo',$l[0])); }
$l=sist_psico_listar($c,'profesor',['id'=>$id],5,0)['filas']; t('profesor solo ve su aviso', strpos(json_encode($l),'SECRETO-MOTIVO')===false && strpos(json_encode($l),'SECRETO-AVISO')!==false);
$r=sist_psico_concluir($c,23,$id); t('otro psicólogo no concluye', !$r['ok']);
$r=sist_psico_concluir($c,4,$id); t('psicólogo concluye', $r['ok'], $r);
$r=sist_psico_concluir($c,4,$id); t('no concluye dos veces', !$r['ok']);
$r=sist_psico_programar_sesion($c,4,$id,$man,'15:00','16:00','x'); t('no programa tras concluir', !$r['ok']);
t('ningún correo con secreto', (int)sist_db_valor($c,"select count(*) from correos_pendientes where cuerpo_texto like '%SECRETO%' or cuerpo_html like '%SECRETO%'")===0);
t('ninguna notificación con secreto', (int)sist_db_valor($c,"select count(*) from notificaciones where mensaje like '%SECRETO%' or titulo like '%SECRETO%'")===0);
// canalización directa + carrera de toma
$r=sist_psico_canalizar($c,6,0,$JORGE,'directo SECRETO-MOTIVO'); t('canalización directa (nuevo proceso tras concluir)', $r['ok'], $r);
$d=$r['id']; 
$a=sist_psico_tomar($c,23,$d); $b=sist_psico_tomar($c,4,$d); t('el primero gana, el segundo no', $a['ok'] && !$b['ok']);
$r=sist_psico_descartar($c,6,$d,'x'); t('no se descarta un caso ya canalizado', !$r['ok'], $r);
echo $f? "FALLOS: $f\n":"TODO OK\n";
