# Canalización de asesorías + notificaciones

Flujo implementado (independiente de la canalización psicológica, tabla `canalizaciones`):

| Paso | Quién | Qué hace | Estado | Se avisa a (campanita + correo) |
|---|---|---|---|---|
| 1 | **Tutor** | Canaliza a un tutorado SUYO a asesoría en una materia de su carrera | PENDIENTE | jefe(s) de carrera de la carrera del alumno · alumno · administradores |
| 2 | **Jefe de carrera** | Asigna profesor, fecha, hora de inicio/fin y lugar | ASIGNADA | alumno · administradores · tutor · **profesor asignado** |
| 3 | **Profesor asignado** | Registra notas de seguimiento y finaliza | EN_SEGUIMIENTO → FINALIZADA | (sin aviso; lo ven tutor, jefe y administrador en su pantalla) |

Pantallas: Tutor `MOD_TUTOR/asesorias` · Jefe `MOD_JEFESCARRERA/asesorias` · Profesor `MOD_DOCENTE/asesorias` ·
Administrador `MOD_ADMIN/asesorias` (solo lectura) y `MOD_ADMIN/correos` (capturar correos y ver la cola) ·
Todos: campanita en la barra superior → `portal/notificaciones`.

## Instalación (en este orden)
1. `database/migraciones/001_permisos.sql` (si aún no está) y luego **`002_asesorias_notificaciones.sql`** sobre `pit_v4`. Se pueden repetir sin riesgo.
2. Copiar los archivos del paquete encima del proyecto.
3. Capturar correos: *Administrador → Correos de avisos* (o el campo "Correo" al registrar). **Sin correo, la persona recibe solo la campanita.**
4. Activar el envío: copiar `config/correo.local.example.php` como `config/correo.local.php` y poner el SMTP
   (Gmail: puerto 587, `tls`, contraseña de aplicación). Mientras no se active, los correos esperan en la cola (`correos_pendientes`) y salen solos al configurarlo.

## Reglas importantes
* Quién es "jefe de carrera de la carrera del alumno": `usuario_tipo` = JEFE_CARRERA y `personal_academico.carrera` = `tutorados.carrera`. Si no hay jefe registrado, se avisa solo al administrador y el tutor ve un aviso.
* Las carreras se comparan sin acentos ni "EN" (`Ingeniería Electromecánica` = `INGENIERIA EN ELECTROMECANICA`), porque `materias` las guarda distinto que `tutorados`.
* Un alumno no puede tener dos asesorías abiertas de la misma materia.
* El correo nunca cancela el trabajo: se guarda todo en una transacción y los correos se envían después; si fallan se reintentan (máx. 5).
* No se envían contraseñas por correo.

## Tablas nuevas (002)
`asesorias_canalizaciones`, `asesorias_seguimiento`, `notificaciones`, `correos_pendientes` y la columna `usuarios.correo`.
Permisos nuevos: `asesorias.canalizar` (TUTOR), `asesorias.asignar` (JEFE_CARRERA), `asesorias.seguimiento` (DOCENTE/TUTOR/COORDINADOR), `asesorias.ver_todas` (DIRECTIVO). ADMIN tiene todos.

## Reintentar correos
*Administrador → Correos de avisos → Enviar pendientes ahora* reintenta la cola desde el portal.

## Pruebas
`docs/pruebas/asesorias/` (necesitan servidor local, Python, Playwright y la BD de pruebas): `reset.sh`, `e2e_navegador.py` (44 comprobaciones del flujo completo) y `abuso_http.py` (autorización y validaciones).
Resultado de la última corrida con la BD real (`pit_v4.sql`): 44/44 + 22/22, con PHP 8.3 y con el comportamiento de mysqli de PHP 8.0.

## Pendiente
La canalización psicológica ya está hecha: ver `docs/PSICOLOGIA.md`.
