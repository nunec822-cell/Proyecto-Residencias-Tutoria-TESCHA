import re, requests, datetime, subprocess
B='http://127.0.0.1:8080/sist/portal'; PW='Prueba2026x'
def sql(q): return subprocess.check_output(['mysql','-uroot','PIT_V4','-N','-e',q]).decode().strip()
def login(u):
    s=requests.Session(); s.post(B+'/validar_login.php', data={'usuario':u,'password':PW}, allow_redirects=False); return s
def csrf(html):
    m=re.search(r'<input[^>]*type="hidden"[^>]*name="([^"]*csrf[^"]*)"[^>]*value="([^"]+)"', html, re.I) or re.search(r'<input[^>]*name="([^"]*csrf[^"]*)"[^>]*value="([^"]+)"', html, re.I)
    return {m.group(1): m.group(2)}
fallos=[]
def t(n, ok, x=''):
    print(('  ✓ ' if ok else '  ✗ ')+n+('' if ok else ' '+str(x)))
    if not ok: fallos.append(n)
sql("DELETE FROM asesorias_seguimiento; DELETE FROM asesorias_canalizaciones; ALTER TABLE asesorias_canalizaciones AUTO_INCREMENT=1;")
SAUL=sql("select id_tutorado from tutorados where id_usuario=20")      # tutorado de CORTES (6)
MONTSE=sql("select id_tutorado from tutorados where id_usuario=19")    # tutorado de otro tutor (7)
INFORM=sql("select id_tutorado from tutorados where id_usuario=9")     # Informática, tutor 8
tutor=login('CORTES VERGARA LEONARDO'); h=tutor.get(B+'/MOD_TUTOR/asesorias/index.php').text; tok=csrf(h)
def crear(s,tok,idt,idm,motivo='x'):
    r=s.post(B+'/MOD_TUTOR/asesorias/acciones/guardar.php', data={**tok,'id_tutorado':idt,'id_materia':idm,'motivo':motivo}, allow_redirects=False)
    return r.headers.get('Location','')
print('== Tutor')
loc=crear(tutor,tok,MONTSE,10); t('tutor NO puede canalizar a alumno de otro tutor', 'error=' in loc and not sql("select count(*) from asesorias_canalizaciones")!='0', loc)
loc=crear(tutor,tok,SAUL,12); t('materia de otra carrera rechazada', 'error=' in loc, loc)
loc=crear(tutor,tok,'0','0'); t('datos vacíos rechazados', 'error=' in loc, loc)
loc=crear(tutor,tok,SAUL,'1 OR 1=1'); t('id_materia con SQL no inyecta (se castea a int)', 'error=' in loc, loc)
loc=crear(tutor,tok,SAUL,11,'M'*1001); t('motivo > 1000 rechazado', 'error=' in loc, loc)
loc=crear(tutor,tok,SAUL,11,'duda'); t('alta válida', 'ok=' in loc, loc)
n=sql("select count(*) from asesorias_canalizaciones"); t('solo 1 asesoría creada', n=='1', n)
print('== Jefe de otra carrera (Administración, user 11) no puede asignar una de ISC')
jo=login('GONZALES ESPINOZA MARTIN'); t('jefe de otra carrera NO ve la asesoría en su bandeja', 'Folio' not in jo.get(B+'/MOD_JEFESCARRERA/asesorias/index.php').text and 'BASE DE DATOS' not in jo.get(B+'/MOD_JEFESCARRERA/asesorias/index.php').text); hh=jo.get(B+'/notificaciones/index.php').text; tk=csrf(hh)
fut=(datetime.date.today()+datetime.timedelta(days=2)).isoformat()
def asignar(s,tk,prof=17,fecha=fut,hi='10:00',hf='11:00'):
    r=s.post(B+'/MOD_JEFESCARRERA/asesorias/acciones/asignar.php', data={**tk,'id_asesoria':1,'id_profesor':prof,'fecha':fecha,'hora_inicio':hi,'hora_fin':hf,'lugar':'A'}, allow_redirects=False)
    return r.headers.get('Location','')
loc=asignar(jo,tk); t('jefe de otra carrera rechazado', 'error=' in loc, loc); t('sigue PENDIENTE', sql("select estado from asesorias_canalizaciones where id_asesoria=1")=='PENDIENTE')
print('== Jefe correcto: validaciones')
je=login('ROMERO SANCHEZ IVAN'); hh=je.get(B+'/MOD_JEFESCARRERA/asesorias/index.php').text; tk=csrf(hh)
t('hora fin <= inicio rechazada', 'error=' in asignar(je,tk,hf='09:00'))
t('fecha pasada rechazada', 'error=' in asignar(je,tk,fecha='2020-01-01'))
t('fecha inválida rechazada', 'error=' in asignar(je,tk,fecha='2026-02-31'))
t('profesor que no es docente (alumno 5) rechazado', 'error=' in asignar(je,tk,prof=5))
t('profesor inexistente rechazado', 'error=' in asignar(je,tk,prof=99999))
t('nada cambió tras los rechazos', sql("select estado from asesorias_canalizaciones where id_asesoria=1")=='PENDIENTE')
loc=asignar(je,tk); t('asignación válida', 'ok=' in loc, loc)
loc=asignar(je,tk,prof=7); t('doble asignación rechazada (ya atendida)', 'error=' in loc, loc)
t('profesor sigue siendo el primero (17)', sql("select id_profesor from asesorias_canalizaciones where id_asesoria=1")=='17')
n=sql("select count(*) from notificaciones where tipo='ASESORIA_ASIGNADA' and id_usuario=5"); t('el alumno recibió UN solo aviso de asignación', n=='1', n)
print('== Seguimiento: solo el profesor asignado')
otro=login('TACUBEÑO CRUZ HORACIO'); ho=otro.get(B+'/notificaciones/index.php').text; tko=csrf(ho)
r=otro.post(B+'/MOD_DOCENTE/asesorias/acciones/seguimiento.php', data={**tko,'id_asesoria':1,'nota':'intruso'}, allow_redirects=False)
t('otro docente no puede dar seguimiento', 'error=' in r.headers.get('Location','') and sql("select count(*) from asesorias_seguimiento")=='0', r.headers.get('Location',''))
print('\nFALLOS:', fallos if fallos else 'ninguno')
