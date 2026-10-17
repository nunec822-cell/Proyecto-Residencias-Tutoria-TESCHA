#!/bin/bash
# Pruebas de humo del control de acceso. Requiere: servidor web con el proyecto, base de pruebas (ver esquema_pruebas.sql) y curl.
# Uso:  SIST_URL=http://localhost/sist bash docs/pruebas/humo.sh   (la ruta /sist depende del nombre de tu carpeta)
# Pruebas de humo del núcleo. Uso: t.sh
B=${SIST_URL:-http://127.0.0.1:8080/sist}/portal
P=0; F=0
ok(){ if [ "$2" = "$3" ]; then echo "  ✓ $1"; P=$((P+1)); else echo "  ✗ $1  (esperado $2, obtenido $3)"; F=$((F+1)); fi; }
code(){ curl -s -o /dev/null -m 10 -w '%{http_code}' "$@"; }
loc(){ curl -s -o /dev/null -m 10 -D - "$@" | grep -i '^location:' | tr -d '\r' | sed 's/^[Ll]ocation: //'; }
login(){ rm -f /tmp/cj_$1; curl -s -o /dev/null -m 10 -c /tmp/cj_$1 -b /tmp/cj_$1 -d "usuario=$2&password=$3" $B/validar_login.php -D /tmp/h_$1; grep -i '^location' /tmp/h_$1 | tr -d '\r' | sed 's/^[Ll]ocation: //'; }
echo "== 1. Páginas públicas del portal cargan =="
ok "login.php carga" 200 $(code $B/login.php)
echo "== 2. Endpoints que ANTES estaban abiertos, sin sesión =="
for u in MOD_ADMIN/registrar/descargar_credenciales.php MOD_ADMIN/buscar/ajax/tutorados.php MOD_ADMIN/asignar_grupos/obtener_grupos.php "MOD_ADMIN/buscar/ajax/consultas.php?tipo=x" MOD_ADMIN/registrar/obtener_tutores.php MOD_ADMIN/nuevo_aviso.php; do
  ok "navegación → redirige al login: $u" "$(echo $B/login.php)" "$(echo $B/../portal/login.php | sed 's#/sist/portal/../portal#/sist/portal#')"  >/dev/null
  l=$(loc "$B/$u"); ok "sin sesión → /sist/portal/login.php: $u" "/sist/portal/login.php" "$l"
  ok "fetch/AJAX sin sesión → 401: $u" 401 $(code -H 'Sec-Fetch-Dest: empty' "$B/$u")
done
for u in subir_galeria.php guardar_inicio.php eliminar_imagen.php; do
  ok "CMS sin sesión → 401: vista_contenidos/$u" 401 $(code -H 'Sec-Fetch-Dest: empty' -X POST "${SIST_URL:-http://127.0.0.1:8080/sist}/vista_contenidos/$u")
done
echo "== 3. Login =="
ok "contraseña incorrecta → vuelve a login.php" "login.php" "$(login x ADMIN1 mala)"
ok "usuario inactivo (con clave correcta) → login.php" "login.php" "$(login x INACT Inactivo2026x)"
ok "ADMIN entra → inicioadmin" "MOD_ADMIN/inicioadmin.php" "$(login adm ADMIN1 Admin2026x)"
ok "TUTORADO entra → su inicio" "MOD_TUTORADO/Inicio_tutorado.php" "$(login tut TUT1 Tutorado2026x)"
echo "== 4. Cookie de sesión =="
ck=$(grep -i '^set-cookie' /tmp/h_adm | head -1)
echo "$ck" | grep -qi httponly && ok "cookie HttpOnly" 1 1 || ok "cookie HttpOnly" 1 0
echo "$ck" | grep -qi 'samesite=lax' && ok "cookie SameSite=Lax" 1 1 || ok "cookie SameSite=Lax" 1 0
echo "== 5. Control por rol/permiso (sesión de TUTORADO) =="
for u in MOD_ADMIN/buscar/ajax/tutorados.php MOD_ADMIN/registrar/descargar_credenciales.php MOD_ADMIN/permisos/index.php MOD_ADMIN/asignar_grupos/obtener_grupos.php; do
  ok "TUTORADO no accede a $u" 403 $(code -b /tmp/cj_tut -H 'Sec-Fetch-Dest: empty' "$B/$u")
done
echo "== 6. ADMIN (todos los permisos) =="
ok "ADMIN descarga credenciales sin pendientes → 404 (no 403)" 404 $(code -b /tmp/cj_adm "$B/MOD_ADMIN/registrar/descargar_credenciales.php")
ok "ADMIN abre pantalla de permisos" 200 $(code -b /tmp/cj_adm "$B/MOD_ADMIN/permisos/index.php")
echo; echo "RESULTADO: $P correctas, $F fallidas"
