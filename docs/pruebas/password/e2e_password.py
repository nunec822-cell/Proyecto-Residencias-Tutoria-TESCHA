import subprocess, time
from playwright.sync_api import sync_playwright
B='http://127.0.0.1:8080/sist/portal'; PW='Prueba2026x'; fallos=[]
def q(sql): return subprocess.check_output(['mysql','-uroot','--default-character-set=utf8mb4','PIT_V4','-N','-e',sql]).decode().strip()
def nom(i): return q(f'select usuario from usuarios where id_usuario={i}')
def t(n,ok,x=''):
    print(('  ✓ ' if ok else '  ✗ ')+n+(' '+str(x)[:300] if not ok else ''))
    if not ok: fallos.append(n)
def login(br,usuario,pw):
    ctx=br.new_context(); pg=ctx.new_page(); pg.goto(B+'/login.php'); pg.fill('input[name=usuario]',usuario); pg.fill('input[name=password]',pw)
    pg.click('button[type=submit], input[type=submit]'); pg.wait_for_load_state(); return ctx,pg
suf=str(int(time.time()))[-6:]; NU='USUARIO PRUEBA '+suf; TMP='Temporal2026x'
with sync_playwright() as p:
    br=p.chromium.launch(executable_path='/opt/pw-browsers/chromium',args=['--no-sandbox'])
    print('== admin registra un tutorado')
    ctx,pg=login(br,nom(16),PW)
    r=pg.request.post(B+'/MOD_ADMIN/registrar/guardar_usuario.php',form={'tipo_usuario':'TUTORADO','usuario':NU,'password':TMP,'activo':'1','matricula':'PW'+suf,'alu_nombre':'Luis','alu_apellido_p':'P','alu_apellido_m':'M','alu_carrera':'X','grupo':'1A','id_tutor':'6'},max_redirects=0)
    idn=q(f"select id_usuario from usuarios where usuario='{NU}'")
    t('usuario creado', idn!='', r.status)
    t('queda marcado debe_cambiar_password=1', q(f'select debe_cambiar_password from usuarios where id_usuario={idn}')=='1')
    ctx.close()
    print('== primer inicio de sesión')
    ctx,pg=login(br,NU,TMP)
    t('login lleva a cambiar_password', 'cambiar_password.php' in pg.url, pg.url)
    t('aviso de contraseña temporal', 'temporal' in pg.content().lower())
    for url in ['/MOD_TUTORADO/Inicio_tutorado.php','/notificaciones/index.php','/MOD_TUTORADO/psicologia/index.php']:
        pg.goto(B+url); t('bloqueado: '+url, 'cambiar_password.php' in pg.url, pg.url)
    # intentos de cambio inválidos
    def intento(a,n,c):
        pg.goto(B+'/cambiar_password.php'); pg.fill('#actual',a); pg.fill('#nueva',n); pg.fill('#confirmar',c)
        pg.click('button[type=submit]'); pg.wait_for_load_state(); return pg.locator('.err').all_inner_texts()
    e=intento('Incorrecta1','Nueva2026xx','Nueva2026xx'); t('actual incorrecta rechazada', e and 'actual' in e[0], e)
    e=intento(TMP,'Nueva2026xx','Otra2026xxx'); t('confirmación distinta rechazada', e and 'confirmación' in e[0], e)
    e=intento(TMP,TMP,TMP); t('igual a la temporal rechazada', e and 'diferente' in e[0], e)
    e=intento(TMP,'corta1','corta1'); t('muy corta rechazada', e and '8' in e[0], e)
    e=intento(TMP,'sololetrasxx','sololetrasxx'); t('sin números rechazada', e and 'números' in e[0], e)
    e=intento(TMP,'12345678901','12345678901'); t('sin letras rechazada', e and 'letras' in e[0], e)
    t('sigue obligado', q(f'select debe_cambiar_password from usuarios where id_usuario={idn}')=='1')
    # POST sin CSRF
    r=pg.request.post(B+'/cambiar_password.php',form={'actual':TMP,'nueva':'Nueva2026xx','confirmar':'Nueva2026xx'}); 
    t('sin token CSRF no cambia', q(f'select debe_cambiar_password from usuarios where id_usuario={idn}')=='1')
    # cambio correcto
    e=intento(TMP,'MiClave2026x','MiClave2026x'); t('cambio correcto entra al portal', 'cambiar_password.php' not in pg.url and 'login.php' not in pg.url, (pg.url,e))
    t('marca quitada', q(f'select debe_cambiar_password from usuarios where id_usuario={idn}')=='0')
    pg.goto(B+'/notificaciones/index.php'); t('ya puede navegar', 'cambiar_password.php' not in pg.url)
    ctx.close()
    print('== después')
    ctx,pg=login(br,NU,TMP); t('la contraseña temporal ya no sirve', 'login.php' in pg.url); ctx.close()
    ctx,pg=login(br,NU,'MiClave2026x'); t('entra con la nueva directo al portal', 'cambiar_password' not in pg.url and 'login.php' not in pg.url, pg.url)
    pg.goto(B+'/cambiar_password.php'); t('cambio voluntario disponible (sin aviso de temporal)', 'temporal' not in pg.content().lower() and pg.locator('#actual').count()==1)
    ctx.close()
    print('== admin restablece la contraseña')
    ctx,pg=login(br,nom(16),PW)
    r=pg.request.post(B+'/MOD_ADMIN/editar/guardar_cambios.php',form={'id_usuario':idn,'usuario':NU,'activo':'1','password':'Restablecida2026x','tipo_usuario':'TUTORADO'},max_redirects=0)
    t('restablecida => vuelve a obligar', q(f'select debe_cambiar_password from usuarios where id_usuario={idn}')=='1', r.status)
    # admin edita su propia cuenta
    r=pg.request.post(B+'/MOD_ADMIN/editar/guardar_cambios.php',form={'id_usuario':'16','usuario':nom(16),'activo':'1','password':'Prueba2026x','tipo_usuario':'ADMIN'},max_redirects=0)
    t('admin que edita su propia contraseña NO queda obligado', q('select debe_cambiar_password from usuarios where id_usuario=16')=='0')
    ctx.close()
    print('== usuarios existentes no afectados')
    ctx,pg=login(br,nom(6),PW); t('usuario sin marca entra normal', 'cambiar_password' not in pg.url and 'login.php' not in pg.url, pg.url); ctx.close()
    br.close()
q(f"delete from usuarios where usuario='{NU}'")
print('FALLOS' if fallos else 'TODO OK', fallos)
