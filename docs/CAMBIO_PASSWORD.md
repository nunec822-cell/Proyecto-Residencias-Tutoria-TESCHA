# Cambio de contraseña obligatorio (SIST v4.4)

El administrador registra al usuario con una contraseña inicial (temporal). En su **primer inicio de sesión** el sistema lo lleva a `portal/cambiar_password.php` y no le deja usar ninguna otra pantalla hasta que elija la suya.

* Se activa solo (`usuarios.debe_cambiar_password = 1`) al **registrar** un usuario y cuando el administrador le **restablece** la contraseña en *Editar usuario*. Si el administrador cambia su propia contraseña no se le obliga.
* Reglas de la nueva: 8 a 72 caracteres, letras y números, distinta de la actual y de su usuario. Pide la contraseña actual (temporal) y usa el límite de intentos del login.
* Cualquier usuario puede entrar a `cambiar_password.php` cuando quiera (cambio voluntario).
* Migración: `database/migraciones/004_cambio_password_obligatorio.sql` (segura de repetir). Sin ella el portal funciona como antes. Los usuarios que ya existen NO quedan obligados; el archivo trae un UPDATE opcional (comentado) para obligarlos a todos.
* Código: `core/password.php`, `requerir_login()` en `core/auth.php` (bloquea el portal mientras la marca esté activa), `portal/validar_login.php`.
* Pruebas: `docs/pruebas/password/e2e_password.py`.

Nota de seguridad pendiente: el sistema guarda las contraseñas asignadas en texto plano en `archivos/credenciales_usuarios.csv` (para entregarlas). Tras el cambio, ese archivo ya no refleja la contraseña real del usuario; conviene borrarlo después de entregar las credenciales.
