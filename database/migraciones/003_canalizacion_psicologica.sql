-- =====================================================================
--  MIGRACIÓN 003 · CANALIZACIÓN PSICOLÓGICA (flujo con avisos)
--  Base de datos: PIT_V4   (requiere haber aplicado la 001 y la 002)
--
--  Ejecutar UNA vez (phpMyAdmin > pit_v4 > SQL). Es segura de repetir.
--  NO toca las tablas existentes (canalizaciones, citas_psicologia, anexo14…):
--  es un flujo nuevo, aparte.
--
--  Flujo:
--    1. El PROFESOR de una materia avisa al TUTOR que un alumno necesita atención
--       psicológica                                                       -> AVISO
--    2. El TUTOR hace la canalización (o la descarta -> DESCARTADA)       -> CANALIZADA
--         avisa a: TODOS los psicólogos, alumno, administradores, jefe(s) de carrera
--         y al profesor que avisó.  El tutor también puede canalizar sin aviso previo.
--    3. Un PSICÓLOGO toma el caso (el primero que lo toma)                -> EN_ATENCION
--         avisa a: alumno y tutor
--    4. El psicólogo PROGRAMA cada sesión con fecha, horario y lugar
--         avisa a: alumno y tutor.  Después registra cómo fue la sesión.
--    5. El psicólogo concluye el proceso                                  -> CONCLUIDA
--
--  CONFIDENCIALIDAD (se cumple en el código y en la estructura):
--    * motivo / motivo_aviso: solo los ve el psicólogo, el tutor que canalizó
--      y (su propio aviso) el profesor que avisó. Nunca van en avisos ni correos.
--    * La NOTA PRIVADA de cada sesión vive en una tabla aparte (psico_notas_privadas)
--      y solo la lee el psicólogo que la escribió.
--    * El HISTORIAL (psico_sesiones: fecha, horario, lugar, estado y una observación
--      general sin datos sensibles) lo ven el alumno, el tutor, el jefe de carrera
--      de su carrera y el administrador.
-- =====================================================================

CREATE TABLE IF NOT EXISTS psico_canalizaciones (
    id_psico            INT NOT NULL AUTO_INCREMENT,
    id_tutorado         INT NOT NULL,                  -- tutorados.id_tutorado
    id_tutor            INT NOT NULL,                  -- usuarios.id_usuario del tutor del alumno
    id_profesor_aviso   INT NULL,                      -- usuarios.id_usuario del profesor que avisó (si lo hubo)
    id_psicologo        INT NULL,                      -- usuarios.id_usuario del psicólogo que tomó el caso
    motivo_aviso        TEXT NULL,                     -- SENSIBLE: lo que escribió el profesor
    motivo              TEXT NULL,                     -- SENSIBLE: lo que escribió el tutor para el psicólogo
    motivo_descarte     VARCHAR(300) NULL,             -- solo lo ve el tutor
    estado              ENUM('AVISO','CANALIZADA','EN_ATENCION','CONCLUIDA','DESCARTADA') NOT NULL DEFAULT 'AVISO',
    fecha_aviso         DATETIME NULL,
    fecha_canalizacion  DATETIME NULL,
    fecha_toma          DATETIME NULL,
    fecha_cierre        DATETIME NULL,
    fecha_creacion      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_psico),
    KEY idx_psi_estado (estado, fecha_creacion),
    KEY idx_psi_tutor (id_tutor, estado),
    KEY idx_psi_psicologo (id_psicologo, estado),
    KEY idx_psi_profesor (id_profesor_aviso),
    KEY idx_psi_tutorado (id_tutorado, estado),
    CONSTRAINT fk_psi_tutorado FOREIGN KEY (id_tutorado) REFERENCES tutorados (id_tutorado) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sesiones: programadas por el psicólogo (fecha, horario y lugar) y luego cerradas con una
-- observación general. Es lo que pueden ver alumno, tutor, jefe de carrera y administrador.
CREATE TABLE IF NOT EXISTS psico_sesiones (
    id_sesion       INT NOT NULL AUTO_INCREMENT,
    id_psico        INT NOT NULL,
    id_psicologo    INT NOT NULL,                      -- usuarios.id_usuario de quien atiende
    fecha           DATE NOT NULL,
    hora_inicio     TIME NOT NULL,
    hora_fin        TIME NOT NULL,
    lugar           VARCHAR(150) NOT NULL,
    estado          ENUM('PROGRAMADA','REALIZADA','NO_ASISTIO','CANCELADA') NOT NULL DEFAULT 'PROGRAMADA',
    observacion     VARCHAR(500) NULL,                 -- SIN datos sensibles (ej. "asistió, continúa el proceso")
    fecha_creacion  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre    DATETIME NULL,
    PRIMARY KEY (id_sesion),
    KEY idx_ses_psico (id_psico, fecha, hora_inicio),
    KEY idx_ses_agenda (id_psicologo, fecha, estado),
    CONSTRAINT fk_ses_psico FOREIGN KEY (id_psico) REFERENCES psico_canalizaciones (id_psico) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Nota privada de cada sesión: SOLO la lee el psicólogo que la escribió
CREATE TABLE IF NOT EXISTS psico_notas_privadas (
    id_nota     INT NOT NULL AUTO_INCREMENT,
    id_sesion   INT NOT NULL,
    nota        TEXT NOT NULL,
    PRIMARY KEY (id_nota),
    UNIQUE KEY uq_pnota_ses (id_sesion),
    CONSTRAINT fk_pnota_ses FOREIGN KEY (id_sesion) REFERENCES psico_sesiones (id_sesion) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
--  Permisos nuevos (mantener igual que config/permisos.php)
-- ---------------------------------------------------------------------
INSERT IGNORE INTO permisos (clave, descripcion, modulo) VALUES
 ('psicologia.avisar',          'Avisar al tutor que un alumno necesita atención psicológica', 'Psicología'),
 ('psicologia.canalizar',       'Canalizar a un tutorado a atención psicológica',               'Psicología'),
 ('psicologia.atender',         'Tomar casos, programar y registrar sesiones (psicólogo)',      'Psicología'),
 ('psicologia.ver_seguimiento', 'Ver el historial de seguimiento psicológico (sin datos sensibles)', 'Psicología'),
 ('psicologia.mi_seguimiento',  'Ver su propio historial de seguimiento psicológico',           'Psicología');

INSERT IGNORE INTO rol_permiso (id_tipo, id_permiso)
SELECT t.id_tipo, p.id_permiso
FROM tipos t
JOIN permisos p ON (
       (t.nombre = 'DOCENTE'      AND p.clave IN ('psicologia.avisar'))
    OR (t.nombre = 'TUTOR'        AND p.clave IN ('psicologia.avisar','psicologia.canalizar'))
    OR (t.nombre = 'COORDINADOR'  AND p.clave IN ('psicologia.avisar'))
    OR (t.nombre = 'PSICOLOGO'    AND p.clave IN ('psicologia.atender'))
    OR (t.nombre = 'JEFE_CARRERA' AND p.clave IN ('psicologia.ver_seguimiento'))
    OR (t.nombre = 'TUTORADO'     AND p.clave IN ('psicologia.mi_seguimiento'))
);
