# Cambios de la versión 4.1 (reorganización, seguridad y permisos)

> **Actualización 15-oct-2026:** se agregó el flujo de **asesorías académicas con notificaciones** (campanita + correo).
> Ver `docs/ASESORIAS.md` y la migración `database/migraciones/002_asesorias_notificaciones.sql`.

Fecha de esta revisión: 6-oct-2026. Se partió de `SIST V.4.0` sin reescribir el sistema.

## 1. Qué se hizo

### Estructura
* Carpeta `PIT_V.4.0` → `portal`; `MOD_CORDINADOR` → `MOD_COORDINADOR`; `indexloguin.php` → `login.php`.
  Todas las referencias se actualizaron (verificado, ver §2).
* Nuevas carpetas: `config/` (rutas, base de datos, permisos), `core/` (motor), `database/migraciones/`, `docs/`.
* **Una sola definición de rutas.** Antes había 18 `$base_url` distintos y ~34 rutas `/SIST V.4.0/...`
  escritas a mano; ahora todo sale de `BASE_URL` / `PORTAL_URL` (detectadas solas). Ya no depende del nombre de la carpeta.
* **Una sola configuración de base de datos** (`config/database.php`, sobreescribible con `config/local.php` o variables
  de entorno). Las credenciales ya no están dentro de los archivos de conexión.
* `core/bootstrap.php` se carga en los 151 archivos PHP del proyecto (sustituye a los `include` frágiles de `../`
  como forma de obtener funciones comunes).
* 81 bloques repetidos de verificación de rol (`if (!in_array("X", $_SESSION["roles"])) {header…; exit;}`)
  → una línea: `requerir_rol('X');`.
* 5 imágenes duplicadas entre `assets/` y `portal/assets_pit/` consolidadas en `assets/`.
* Archivo vacío `obtener_avisos.php` eliminado.
* Plantilla de módulo nuevo probada: `docs/plantilla_modulo/`.

### Permisos (nuevo)
* Se verifican **permisos** (`requerir_permiso('usuarios.gestionar')`), no roles. Catálogo en `config/permisos.php`.
* Pantalla *Administrador → Permisos*: permisos por rol y permisos adicionales por usuario (agregar y quitar).
* Tablas en `database/migraciones/001_permisos.sql` (si aún no se aplica, el sistema usa el catálogo por defecto).
* Los permisos se cargan una vez al iniciar sesión (sin consultas extra por petición). Cambios → surten efecto en el
  siguiente inicio de sesión de la persona. ADMIN siempre tiene todos.

### Seguridad
* **40 endpoints sin protección real** (unos sin sesión y otros con sesión pero sin verificar rol) ahora exigen sesión y permiso (alta/edición de usuarios, descarga de
  credenciales, buscadores y exportación del admin, avisos, y todo el CMS de contenidos).
  Las peticiones fetch/AJAX reciben 401/403 en JSON; las páginas redirigen al login.
* **Contraseñas en texto plano**: ya no se escriben en ningún archivo. Tras registrar usuarios se descargan **una sola
  vez** desde la sesión del administrador y se borran. Se eliminó `archivos/credenciales_usuarios.csv`.
* **Subida de archivos**: los 10 puntos sin validación (CMS y avisos del admin) usan `sist_mover_subida()` (valida
  extensión, tipo real, nombre y tamaño) y nombres saneados. Carpetas de subidas con `.htaccess` que impide ejecutar scripts.
* **Inyección SQL**: 73 variables que llegaban de `$_GET/$_POST` a consultas armadas a mano se escapan o convierten
  a entero al recibirlas; `exportar_csv.php` tenía un `id` numérico sin comillas (inyección real), corregido.
  Nombres de archivo subidos saneados (iban directo al SQL).
* **Login**: regeneración del id de sesión, cookie HttpOnly + SameSite, expiración por inactividad (1 h),
  límite de 5 intentos / 15 min por usuario y 25 por IP (requiere la tabla de la migración), no distingue
  "usuario inexistente" de "contraseña incorrecta", `display_errors` apagado.
* **Contraseña repetida**: se retiró la comprobación que recorría **todos** los usuarios con `password_verify`
  (lenta y revelaba contraseñas ajenas); ahora se exige mínimo 8 caracteres con letras y números.
* `eliminar_imagen.php` y los borrados de avisos/instituciones: `basename()` contra recorrido de rutas.
* `.htaccess`: bloqueo de `config/`, `core/`, `database/`, `docs/`, cabeceras de seguridad. `.gitignore` nuevo.

### Errores previos corregidos
* Redirecciones a `../../index.php` (no existe) en canalizaciones de Directivos y Jefes → ahora `login.php`.
* `coordinador.js` se pedía en `js/coordinador.js` (la ruta real es la raíz del módulo).

## 2. Qué se verificó (y cómo)
| Verificación | Resultado |
|---|---|
| `php -l` en todos los PHP | 0 errores |
| Verificador de referencias (`include`, `href`, `src`, `action`, `Location`, `url()`), comparado con el original | 0 enlaces rotos nuevos |
| Pruebas de humo con servidor + MariaDB (`docs/pruebas/humo.sh`) | 34/34 |
| Migración 001 ejecutada dos veces | sin error, sin duplicados |
| Permisos en BD: concesión individual, CSRF, efecto tras re-login | correcto |
| Límite de intentos de login | bloquea al 6.º intento, aun con clave correcta |
| Subida segura (`.php`, `.png` con PHP dentro, doble extensión, `..\`, `.svg`, `.htaccess`…) | 11/11 |
| Plantilla de módulo ejecutada completa (listado, CSRF, JSON, XSS, permisos) | correcto |

**No se pudo probar** (no se tiene tu base de datos real): las pantallas existentes con sus datos reales,
la apariencia visual, y el `.htaccess` en tu servidor. Haz una pasada completa por cada rol antes de publicar.

## 3. Pendiente (no se hizo, y por qué)
1. **Esquema de la base de datos**: falta `database/esquema.sql`. Expórtalo de phpMyAdmin (solo estructura).
   Sin él no se pueden revisar índices (causa frecuente de lentitud con muchos usuarios).
2. **Rotar contraseñas y limpiar git.** `credenciales_usuarios.csv` sigue en el *historial* del repositorio
   (y las contraseñas viajaron a GitHub). Cambia esas contraseñas y purga el historial (`git filter-repo` o BFG).
3. **Sidebars (6) y headers (2)**: siguen separados por rol; unificarlos exige probar la apariencia en pantalla.
4. **Avisos (3 tablas)**, **Directivos/Jefes de carrera (módulos gemelos)**, **`obtener_grupos` ×6**, **`exportar_csv` ×3**:
   fusionarlos requiere el esquema real y migrar datos. Está diseñado; falta ejecutarlo con la BD a la vista.
5. **SQL**: lo hecho es un parche en el punto de entrada, no `prepare()`. Convertir el resto progresivamente
   (la heurística pudo no ver patrones poco comunes: revisa `grep -rn "mysqli_query\|->query(" portal vista_*`).
6. **CSRF** solo en pantallas nuevas y en `solicitud_cita`. Siguen existiendo borrados por GET
   (`eliminar_asignacion.php?id=`, `cerrar.php?id=`, `eliminar_aviso.php?id=`…): pasarlos a POST + token.
7. **XSS**: las pantallas existentes imprimen muchos datos sin `e()` (~950 `echo` vs ~195 `htmlspecialchars`).
8. **Subidas fuera del web root** (`storage/`) con descarga controlada: las evidencias de alumnos siguen siendo
   accesibles por URL (nombres aleatorios). Requiere revisar cómo guarda la BD las rutas.
9. **`solicitud_cita`** (formulario público): se dejó público a propósito (tiene CSRF); conviene limitar peticiones.
10. **Archivos que ya estaban rotos**: `MOD_DIRECTIVOS/inicio.php` pide `directivos.js` (no existe) y
    `MOD_TUTORADO/cita_psicologia_alumno/index.php` pide `js/cita_psicologia.js` (no existe).
11. **Cambiar contraseña / recuperarla**: no existe esa función.
12. Mover los módulos a una carpeta `modulos/` quedó fuera: sin pruebas en pantalla era el cambio más riesgoso
    y de menor beneficio inmediato.

## 4. Si algo falla
* **Error 500 en `assets/` o carpetas de subidas**: tu Apache no permite `Options`/`RemoveHandler` en `.htaccess`
  (`AllowOverride`). Borra ese `.htaccess` y bloquea la ejecución desde la configuración del servidor.
* **"No tiene permisos" para un usuario que antes entraba**: revisa sus permisos en *Permisos* (o aplica la
  migración 001, que siembra los permisos por defecto de cada rol) y que vuelva a iniciar sesión.
* **Error de conexión**: revisa `config/local.php` (host, usuario, contraseña, nombres de las dos bases).
