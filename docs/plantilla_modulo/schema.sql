-- Tabla de ejemplo de la plantilla. Cambia el nombre por el de tu módulo.
CREATE TABLE IF NOT EXISTS plantilla_registros (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo     VARCHAR(150) NOT NULL,
    estado     VARCHAR(20)  NOT NULL DEFAULT 'activo',
    creado_en  DATETIME     NOT NULL,
    PRIMARY KEY (id),
    KEY idx_estado (estado)          -- índice en las columnas por las que se filtra o se ordena
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
