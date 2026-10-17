# Canalización psicológica (SIST v4.3)

Flujo nuevo, **independiente** de las tablas `canalizaciones`/anexo 14 y `citas_psicologia`.

## Flujo
1. **Profesor** (de un grupo del alumno) avisa al tutor: *Mi menú → Avisar atención psicológica*. → `AVISO`. Se notifica al tutor.
2. **Tutor** canaliza (o descarta) desde *Canalizar a psicología*; también puede canalizar directo a un tutorado suyo. → `CANALIZADA`.
   Se avisa (campanita + correo) a: alumno, **todos los psicólogos**, administradores, jefe de carrera de la carrera del alumno y al profesor que avisó.
   Ya no se elige psicólogo.
3. **Psicólogo** toma el caso en *Canalizaciones* (el primero que lo toma queda como responsable; los demás ya no lo ven). → `EN_ATENCION`. Avisa a alumno y tutor.
4. El psicólogo **programa cada sesión con fecha, horario y lugar** (avisa a alumno y tutor; no permite empalmes ni fechas pasadas) y luego la cierra:
   Realizada / No asistió / Cancelada, con una **observación general** y, si quiere, una **nota privada**.
5. El psicólogo **concluye** el proceso (no con sesiones programadas pendientes). → `CONCLUIDA`.

## Qué ve cada quien
| | Motivos | Historial (fecha, horario, lugar, estado, observación) | Nota privada |
|---|---|---|---|
| Psicólogo del caso | sí | sí | **solo él** |
| Otro psicólogo | solo mientras el caso no se toma | no | no |
| Tutor | el suyo y el del profesor | sí | no |
| Profesor que avisó | solo su aviso | no | no |
| Alumno, jefe de carrera, administrador | **no** | sí | no |

Ni los avisos ni los correos llevan motivos ni observaciones. La nota privada está en una tabla aparte (`psico_notas_privadas`).

## Instalación
1. Tener aplicadas 001 y 002. Ejecutar `database/migraciones/003_canalizacion_psicologica.sql` en `pit_v4` (se puede repetir).
2. Copiar los archivos del paquete encima del proyecto.
3. Los psicólogos y demás necesitan correo capturado para recibir email (sin correo, solo campanita).

## Tablas y permisos
`psico_canalizaciones`, `psico_sesiones`, `psico_notas_privadas`.
Permisos: `psicologia.avisar` (DOCENTE/TUTOR/COORDINADOR), `psicologia.canalizar` (TUTOR), `psicologia.atender` (PSICOLOGO), `psicologia.ver_seguimiento` (JEFE_CARRERA), `psicologia.mi_seguimiento` (TUTORADO). El administrador ve todo (sin notas privadas ni motivos).

## Código
`core/psicologia.php` (toda la lógica, transacciones), `portal/includes_pit/psico_vista.php`, pantallas en `MOD_DOCENTE|MOD_TUTOR|MOD_PSICOLOGOS/seguimiento|MOD_JEFESCARRERA|MOD_ADMIN|MOD_TUTORADO` bajo `psicologia/`.

## Pruebas
`docs/pruebas/psicologia/`: `psico_core.php` (59 comprobaciones de la lógica; `OFF=1` simula mysqli de PHP 8.0) y `e2e_psico.py` (navegador, privacidad y permisos).
