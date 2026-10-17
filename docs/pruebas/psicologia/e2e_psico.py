import re, subprocess, datetime
from playwright.sync_api import sync_playwright
B='http://127.0.0.1:8080/sist/portal'; PW='Prueba2026x'; fallos=[]
def q(sql): return subprocess.check_output(['mysql','-uroot','--default-character-set=utf8mb4','PIT_V4','-N','-e',sql]).decode().strip()
def nom(i): return q(f'select usuario from usuarios where id_usuario={i}')
JORGE=q('select id_tutorado from tutorados where id_usuario=5')
def t(n,ok,x=''):
    print(('  ✓ ' if ok else '  ✗ ')+n+(' '+str(x)[:300] if not ok else '')); 
    if not ok: fallos.append(n)
def ses(br,i):
    ctx=br.new_context(viewport={'width':1280,'height':900}); pg=ctx.new_page(); errs=[]; pg.on('pageerror',lambda e:errs.append(str(e)))
    pg.goto(B+'/login.php'); pg.fill('input[name=usuario]',nom(i)); pg.fill('input[name=password]',PW); pg.click('button[type=submit], input[type=submit]'); pg.wait_for_load_state()
    t(f'login usuario {i}','login.php' not in pg.url,pg.url); return ctx,pg,errs
SEC=['SECRETO-AVISO','SECRETO-MOTIVO','SECRETO-NOTA']
with sync_playwright() as p:
    br=p.chromium.launch(executable_path='/opt/pw-browsers/chromium',args=['--no-sandbox'])
    print('== profesor avisa')
    ctx,pg,e=ses(br,7); pg.goto(B+'/MOD_DOCENTE/psicologia/index.php')
    t('enlace en sidebar', pg.locator('a[href$="MOD_DOCENTE/psicologia/index.php"]').count()>0)
    pg.select_option('select[name=id_tutorado]',value=JORGE); pg.fill('textarea[name=motivo]','Llora en clase SECRETO-AVISO <b>x</b>')
    pg.click('form[action*="avisar"] button[type=submit]'); pg.wait_for_load_state()
    t('aviso enviado', pg.locator('.as-msg.ok').count()==1, pg.content()[:200]); t('sin JS err',not e,e); ctx.close()
    print('== tutor canaliza')
    ctx,pg,e=ses(br,6); pg.goto(B+'/MOD_TUTOR/psicologia/index.php')
    t('sidebar tutor muestra enlace', pg.locator('a[href$="MOD_TUTOR/psicologia/index.php"]').count()>0)
    t('ya no hay selector de psicólogo', pg.locator('select[name=id_psicologo]').count()==0)
    t('tutor ve lo que observó el profesor','SECRETO-AVISO' in pg.content())
    t('HTML escapado','<b>x</b>' not in pg.content())
    pg.locator('form[action="acciones/canalizar.php"]').first.locator('textarea[name=motivo]').fill('Ansiedad SECRETO-MOTIVO')
    pg.locator('form[action="acciones/canalizar.php"]').first.locator('button[type=submit]').click(); pg.wait_for_load_state()
    t('canalizada', pg.locator('.as-msg.ok').count()==1, pg.locator('.as-msg').all_inner_texts()); ctx.close()
    ID=q('select id_psico from psico_canalizaciones limit 1')
    print('== alumno/jefe/admin ven sin sensibles')
    for u,url in [(5,'/MOD_TUTORADO/psicologia/index.php'),(2,'/MOD_JEFESCARRERA/psicologia/index.php'),(16,'/MOD_ADMIN/psicologia/index.php')]:
        ctx,pg,e=ses(br,u); pg.goto(B+url); c=pg.content()
        t(f'u{u} ve el caso', 'Folio '+ID in c and 'Canalizada' in c, c[:200]); t(f'u{u} sin sensibles', not any(s in c for s in SEC)); 
        pg.goto(B+'/notificaciones/index.php'); c=pg.content(); t(f'u{u} bandeja con aviso', 'psicol' in c.lower() and not any(s in c for s in SEC)); ctx.close()
    ctx,pg,e=ses(br,7); pg.goto(B+'/MOD_DOCENTE/psicologia/index.php'); c=pg.content(); t('profesor ve su aviso, no el motivo del tutor','SECRETO-AVISO' in c and 'SECRETO-MOTIVO' not in c); ctx.close()
    print('== psicólogos')
    ctx,pg,e=ses(br,23); pg.goto(B+'/MOD_PSICOLOGOS/seguimiento/index.php'); t('psic 23 ve caso por tomar con motivo','SECRETO-MOTIVO' in pg.content() and 'Tomar este caso' in pg.content())
    ctx2,pg2,e2=ses(br,4); pg2.goto(B+'/MOD_PSICOLOGOS/seguimiento/index.php')
    t('sidebar psicólogo enlace', pg2.locator('a[href$="MOD_PSICOLOGOS/seguimiento/index.php"]').count()>0)
    pg2.click('text=Tomar este caso'); pg2.wait_for_load_state(); t('psic 4 toma', pg2.locator('.as-msg.ok').count()==1, pg2.locator('.as-msg').all_inner_texts())
    pg.reload(); t('23 ya no lo ve por tomar','Tomar este caso' not in pg.content() and 'SECRETO-MOTIVO' not in pg.content())
    # 23 intenta tomar por POST directo
    tok=pg.locator('input[name*=csrf]').first.get_attribute('value') if pg.locator('input[name*=csrf]').count() else ''
    mañana=(datetime.date.today()+datetime.timedelta(days=1)).isoformat()
    f=pg2.locator('form[action="acciones/programar_sesion.php"]')
    f.locator('input[name=fecha]').fill(mañana); f.locator('input[name=hora_inicio]').fill('10:00'); f.locator('input[name=hora_fin]').fill('11:00'); f.locator('input[name=lugar]').fill('Cubículo de psicología')
    f.locator('button[type=submit]').click(); pg2.wait_for_load_state(); t('sesión programada', pg2.locator('.as-msg.ok').count()==1, pg2.locator('.as-msg').all_inner_texts())
    pg2.screenshot(path='shots/psico_psicologo.png',full_page=True)
    # cerrar sesión de mañana como realizada: debe fallar
    fc=pg2.locator('form[action="acciones/cerrar_sesion.php"]').first
    fc.locator('select[name=estado]').select_option('REALIZADA'); fc.locator('input[name=observacion]').fill('Asistió'); fc.locator('button[type=submit]').click(); pg2.wait_for_load_state()
    t('realizada en fecha futura rechazada', pg2.locator('.as-msg.error').count()==1)
    # mover sesión a hoy y cerrar con nota
    q(f"update psico_sesiones set fecha=CURDATE() where id_psico={ID}")
    pg2.goto(B+'/MOD_PSICOLOGOS/seguimiento/index.php'); fc=pg2.locator('form[action="acciones/cerrar_sesion.php"]').first
    fc.locator('select[name=estado]').select_option('REALIZADA'); fc.locator('input[name=observacion]').fill('Asistió; continúa el proceso'); fc.locator('textarea[name=nota_privada]').fill('Refiere ansiedad SECRETO-NOTA'); fc.locator('button[type=submit]').click(); pg2.wait_for_load_state()
    t('sesión cerrada', pg2.locator('.as-msg.ok').count()==1, pg2.locator('.as-msg').all_inner_texts()); t('psic ve su nota privada','SECRETO-NOTA' in pg2.content())
    ctx.close()
    ctx3,pg3,e3=ses(br,23); pg3.goto(B+'/MOD_PSICOLOGOS/seguimiento/index.php?id='+ID); t('otro psicólogo no ve nota ni motivo','SECRETO' not in pg3.content()); ctx3.close()
    ctx2.close()
    print('== visibilidad del historial')
    for u,url in [(5,'/MOD_TUTORADO/psicologia/index.php'),(2,'/MOD_JEFESCARRERA/psicologia/index.php'),(16,'/MOD_ADMIN/psicologia/index.php'),(6,'/MOD_TUTOR/psicologia/index.php')]:
        ctx,pg,e=ses(br,u); pg.goto(B+url); c=pg.content()
        t(f'u{u} historial con lugar/hora/obs','Cubículo de psicología' in c and '10:00' in c and 'Asistió; continúa el proceso' in c, c[-600:])
        t(f'u{u} sin nota privada'+(' ni motivos del profesor' if u!=6 else ''), 'SECRETO-NOTA' not in c and (u==6 or 'SECRETO-MOTIVO' not in c)); t('sin JS err',not e,e); ctx.close()
    print('== permisos cruzados')
    for u,url in [(5,'/MOD_PSICOLOGOS/seguimiento/index.php'),(2,'/MOD_PSICOLOGOS/seguimiento/index.php'),(20,'/MOD_TUTOR/psicologia/index.php'),(5,'/MOD_DOCENTE/psicologia/index.php'),(7,'/MOD_TUTOR/psicologia/index.php'),(6,'/MOD_JEFESCARRERA/psicologia/index.php'),(4,'/MOD_ADMIN/psicologia/index.php')]:
        ctx,pg,e=ses(br,u); r=pg.goto(B+url); t(f'u{u} denegado a {url}', r.status==403 or 'login.php' in pg.url, r.status); ctx.close()
    br.close()
print('FALLOS' if fallos else 'TODO OK', fallos)
