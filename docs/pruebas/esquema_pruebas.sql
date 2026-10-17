-- Esquema MÍNIMO solo para las pruebas de humo (NO es el esquema real del sistema).
-- Úsalo en una base de pruebas, nunca en la de producción.
--   mysql -uroot -e "CREATE DATABASE PIT_V4; CREATE DATABASE gestor_contenidos;"
--   mysql -uroot PIT_V4 < docs/pruebas/esquema_pruebas.sql
--   mysql -uroot PIT_V4 < database/migraciones/001_permisos.sql
--   mysql -uroot gestor_contenidos -e "CREATE TABLE tema_sidebar (tema VARCHAR(50)); INSERT INTO tema_sidebar VALUES ('azul');"
-- Cuentas: ADMIN1 / Admin2026x · TUT1 (tutorado) / Tutorado2026x · INACT (inactiva) / Inactivo2026x
CREATE TABLE IF NOT EXISTS tipos (id_tipo INT PRIMARY KEY AUTO_INCREMENT, nombre VARCHAR(50));
CREATE TABLE IF NOT EXISTS usuarios (id_usuario INT PRIMARY KEY AUTO_INCREMENT, usuario VARCHAR(100), password VARCHAR(255), activo TINYINT DEFAULT 1);
CREATE TABLE IF NOT EXISTS usuario_tipo (id_usuario INT, id_tipo INT, activo TINYINT DEFAULT 1);
INSERT INTO tipos (nombre) VALUES ('ADMIN'),('DIRECTIVO'),('JEFE_CARRERA'),('PSICOLOGO'),('TUTORADO'),('DOCENTE'),('TUTOR'),('COORDINADOR');
INSERT INTO usuarios (usuario,password,activo) VALUES ('ADMIN1','$2y$10$2p5oi15.daBKnrfeUhRvzOw7dxkJZnz9lVkBDgg3hdZ6kx/l0q.rW',1),('TUT1','$2y$10$mO.gje6ESgzDNrK1CeHNHuBRWWd4FWELq3zJYsbTUEuOnOHXFJvbG',1),('INACT','$2y$10$Xe5Pb4uaNtgJvnB2.mFUleIqckNuTPhORPbkiSyJrFuYdhH1Bnv8C',0);
INSERT INTO usuario_tipo VALUES (1,1,1),(2,5,1),(3,5,1);
