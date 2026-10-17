-- =====================================================================
--  MIGRACIÓN 002 · CANALIZACIÓN DE ASESORÍAS + NOTIFICACIONES
--  Base de datos: PIT_V4   (requiere haber aplicado la 001)
--
--  Ejecutar UNA vez (phpMyAdmin > pit_v4 > SQL). Es segura de repetir:
--  no duplica ni borra datos.
--
--  Crea:
--    usuarios.correo              correo de contacto (opcional) para los avisos
--    asesorias_canalizaciones     una canalización de asesoría por alumno y materia
--    asesorias_seguimiento        notas de seguimiento del profesor asesor
--    notificaciones               bandeja interna (campanita del portal)
--    correos_pendientes           cola de correos (un fallo de correo NO cancela nada)
--  Y los permisos nuevos del módulo (catálogo + permisos por defecto de cada rol).
--
--  Flujo:
--    1. El TUTOR canaliza a un alumno suyo a asesoría en una materia   -> PENDIENTE
--         avisa a: jefe(s) de carrera de la carrera del alumno, alumno, administradores
--    2. El JEFE DE CARRERA asigna profesor, fecha, hora y lugar          -> ASIGNADA
--         avisa a: alumno, administradores, tutor (y al profesor asignado)
--    3. El PROFESOR asignado registra el seguimiento                     -> EN_SEGUIMIENTO / FINALIZADA
-- =====================================================================

-- ---------------------------------------------------------------------
--  Correo de contacto de cada usuario (opcional)
-- ---------------------------------------------------------------------
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS correo VARCHAR(150) NULL DEFAULT NULL;

-- Los psicólogos ya tienen correo institucional: se aprovecha si aún no hay uno
UPDATE usuarios u
JOIN administradores_psicologos ap ON ap.id_usuario = u.id_usuario
SET u.correo = ap.correo_institucional
WHERE (u.correo IS NULL OR u.correo = '') AND ap.correo_institucional <> '';

-- ---------------------------------------------------------------------
--  Canalizaciones de asesoría académica
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS asesorias_canalizaciones (
    id_asesoria       INT NOT NULL AUTO_INCREMENT,
    id_tutorado       INT NOT NULL,                    -- tutorados.id_tutorado
    id_tutor          INT NOT NULL,                    -- usuarios.id_usuario del tutor que canaliza
    id_materia        INT NOT NULL,                    -- materias.id_materia
    motivo            TEXT NULL,
    estado            ENUM('PENDIENTE','ASIGNADA','EN_SEGUIMIENTO','FINALIZADA') NOT NULL DEFAULT 'PENDIENTE',
    id_jefe           INT NULL,                        -- usuarios.id_usuario de quien asignó
    id_profesor       INT NULL,                        -- usuarios.id_usuario del profesor asesor
    fecha_asesoria    DATE NULL,
    hora_inicio       TIME NULL,
    hora_fin          TIME NULL,
    lugar             VARCHAR(150) NULL,
    fecha_creacion    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_asignacion  DATETIME NULL,
    fecha_cierre      DATETIME NULL,
    PRIMARY KEY (id_asesoria),
    KEY idx_as_estado (estado, fecha_creacion),
    KEY idx_as_tutor (id_tutor),
    KEY idx_as_profesor (id_profesor, estado),
    KEY idx_as_tutorado (id_tutorado, id_materia),
    CONSTRAINT fk_as_tutorado FOREIGN KEY (id_tutorado) REFERENCES tutorados (id_tutorado) ON DELETE CASCADE,
    CONSTRAINT fk_as_materia  FOREIGN KEY (id_materia)  REFERENCES materias (id_materia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS asesorias_seguimiento (
    id_seguimiento  INT NOT NULL AUTO_INCREMENT,
    id_asesoria     INT NOT NULL,
    id_usuario      INT NOT NULL,                      -- quien registró la nota
    nota            TEXT NOT NULL,
    estado          ENUM('EN_SEGUIMIENTO','FINALIZADA') NOT NULL,
    fecha           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_seguimiento),
    KEY idx_seg_asesoria (id_asesoria, fecha),
    CONSTRAINT fk_seg_asesoria FOREIGN KEY (id_asesoria) REFERENCES asesorias_canalizaciones (id_asesoria) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
--  Notificaciones internas (campanita) y cola de correos
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notificaciones (
    id_notificacion  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_usuario       INT NOT NULL,                     -- destinatario
    tipo             VARCHAR(40)  NOT NULL,
    titulo           VARCHAR(150) NOT NULL,
    mensaje          VARCHAR(600) NOT NULL,
    url              VARCHAR(255) NOT NULL DEFAULT '', -- ruta relativa al portal
    leida            TINYINT(1)   NOT NULL DEFAULT 0,
    fecha            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_notificacion),
    KEY idx_not_usuario (id_usuario, leida, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS correos_pendientes (
    id_correo        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_notificacion  BIGINT UNSIGNED NULL,
    destinatario     VARCHAR(150) NOT NULL,
    asunto           VARCHAR(200) NOT NULL,
    cuerpo_html      MEDIUMTEXT   NOT NULL,
    cuerpo_texto     MEDIUMTEXT   NOT NULL,
    estado           ENUM('PENDIENTE','ENVIADO','ERROR') NOT NULL DEFAULT 'PENDIENTE',
    intentos         TINYINT UNSIGNED NOT NULL DEFAULT 0,
    error            VARCHAR(255) NULL,
    fecha_creacion   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_envio      DATETIME NULL,
    PRIMARY KEY (id_correo),
    KEY idx_cor_estado (estado, intentos, id_correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
--  Permisos nuevos (mantener igual que config/permisos.php)
-- ---------------------------------------------------------------------
INSERT IGNORE INTO permisos (clave, descripcion, modulo) VALUES
 ('asesorias.canalizar',   'Canalizar a un alumno tutorado a asesoría académica', 'Asesorías'),
 ('asesorias.asignar',     'Asignar profesor, fecha y hora a las asesorías',      'Asesorías'),
 ('asesorias.seguimiento', 'Dar seguimiento a las asesorías asignadas',           'Asesorías'),
 ('asesorias.ver_todas',   'Consultar todas las asesorías (solo lectura)',        'Asesorías');

INSERT IGNORE INTO rol_permiso (id_tipo, id_permiso)
SELECT t.id_tipo, p.id_permiso
FROM tipos t
JOIN permisos p ON (
       (t.nombre = 'TUTOR'        AND p.clave IN ('asesorias.canalizar','asesorias.seguimiento'))
    OR (t.nombre = 'DOCENTE'      AND p.clave IN ('asesorias.seguimiento'))
    OR (t.nombre = 'COORDINADOR'  AND p.clave IN ('asesorias.seguimiento'))
    OR (t.nombre = 'JEFE_CARRERA' AND p.clave IN ('asesorias.asignar'))
    OR (t.nombre = 'DIRECTIVO'    AND p.clave IN ('asesorias.ver_todas'))
);
