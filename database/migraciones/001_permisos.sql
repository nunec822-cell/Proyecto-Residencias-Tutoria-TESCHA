-- =====================================================================
--  MIGRACIÓN 001 · PERMISOS Y SEGURIDAD DE ACCESO
--  Base de datos: PIT_V4
--
--  Ejecutar UNA vez (phpMyAdmin > PIT_V4 > SQL, o línea de comandos).
--  Es seguro volver a ejecutarla: no duplica ni borra datos.
--
--  Crea:
--    permisos          catálogo de permisos (clave única)
--    rol_permiso       qué permisos tiene cada rol (tabla `tipos`)
--    usuario_permiso   permisos concedidos a una persona en particular
--    login_intentos    registro de intentos fallidos de inicio de sesión
--
--  Mientras esta migración NO se ejecute, el sistema funciona con el
--  catálogo por defecto de config/permisos.php.
-- =====================================================================

CREATE TABLE IF NOT EXISTS permisos (
    id_permiso   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    clave        VARCHAR(80)  NOT NULL,
    descripcion  VARCHAR(255) NOT NULL DEFAULT '',
    modulo       VARCHAR(60)  NOT NULL DEFAULT '',
    PRIMARY KEY (id_permiso),
    UNIQUE KEY uq_permisos_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS rol_permiso (
    id_tipo     INT NOT NULL,
    id_permiso  INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_tipo, id_permiso),
    KEY idx_rp_permiso (id_permiso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS usuario_permiso (
    id_usuario  INT NOT NULL,
    id_permiso  INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_usuario, id_permiso),
    KEY idx_up_permiso (id_permiso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS login_intentos (
    id       BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario  VARCHAR(100) NOT NULL,
    ip       VARCHAR(45)  NOT NULL DEFAULT '',
    fecha    DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_li_usuario (usuario, fecha),
    KEY idx_li_ip (ip, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
--  Catálogo de permisos (mantener igual que config/permisos.php)
-- ---------------------------------------------------------------------
INSERT IGNORE INTO permisos (clave, descripcion, modulo) VALUES
 ('usuarios.consultar',         'Consultar y exportar el directorio de usuarios', 'Administración'),
 ('usuarios.gestionar',         'Registrar y editar usuarios',                    'Administración'),
 ('grupos.asignar',             'Asignar grupos y materias a docentes',           'Administración'),
 ('avisos.gestionar',           'Crear, editar y eliminar avisos internos',       'Avisos'),
 ('contenidos.gestionar',       'Administrar los contenidos del sitio público',   'Contenidos'),
 ('permisos.gestionar',         'Administrar permisos de roles y usuarios',       'Administración'),
 ('anexo14.crear',              'Capturar reportes del Anexo 14',                 'Canalizaciones'),
 ('anexo14.consultar',          'Consultar reportes del Anexo 14',                'Canalizaciones'),
 ('canalizaciones.tomar',       'Tomar y dar seguimiento a canalizaciones',       'Canalizaciones'),
 ('canalizaciones.ver_todas',   'Ver canalizaciones de todas las carreras',       'Canalizaciones'),
 ('canalizaciones.ver_carrera', 'Ver canalizaciones de su carrera',               'Canalizaciones'),
 ('coordinacion.ver',           'Panel de coordinación',                          'Coordinación'),
 ('psicologia.citas',           'Gestionar citas y agenda de psicología',         'Psicología'),
 ('psicologia.avisos',          'Gestionar avisos de psicología',                 'Psicología'),
 ('tutorado.panel',             'Panel del tutorado',                             'Tutorados');

-- ---------------------------------------------------------------------
--  Permisos por defecto de cada rol (mantener igual que config/permisos.php)
--  ADMIN no necesita filas: tiene todos los permisos siempre.
-- ---------------------------------------------------------------------
INSERT IGNORE INTO rol_permiso (id_tipo, id_permiso)
SELECT t.id_tipo, p.id_permiso
FROM tipos t
JOIN permisos p ON (
       (t.nombre = 'DOCENTE'      AND p.clave IN ('anexo14.crear','anexo14.consultar'))
    OR (t.nombre = 'TUTOR'        AND p.clave IN ('anexo14.crear','anexo14.consultar','canalizaciones.tomar'))
    OR (t.nombre = 'COORDINADOR'  AND p.clave IN ('anexo14.crear','anexo14.consultar','coordinacion.ver'))
    OR (t.nombre = 'DIRECTIVO'    AND p.clave IN ('canalizaciones.ver_todas'))
    OR (t.nombre = 'JEFE_CARRERA' AND p.clave IN ('canalizaciones.ver_carrera'))
    OR (t.nombre = 'PSICOLOGO'    AND p.clave IN ('psicologia.citas','psicologia.avisos'))
    OR (t.nombre = 'TUTORADO'     AND p.clave IN ('tutorado.panel'))
);
