#!/bin/bash
# deja la BD de prueba lista para correr e2e.py desde cero
H=$(php -r 'echo password_hash("Prueba2026x", PASSWORD_DEFAULT);')
mysql -uroot PIT_V4 -e "
SET FOREIGN_KEY_CHECKS=0;
DELETE FROM asesorias_seguimiento; DELETE FROM asesorias_canalizaciones; DELETE FROM notificaciones; DELETE FROM correos_pendientes; DELETE FROM login_intentos;
ALTER TABLE asesorias_canalizaciones AUTO_INCREMENT=1;
SET FOREIGN_KEY_CHECKS=1;
UPDATE usuarios SET password='$H', activo=1 WHERE id_usuario IN (2,3,5,6,7,8,11,16,17,20);"
