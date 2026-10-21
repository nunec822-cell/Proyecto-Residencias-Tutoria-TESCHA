-- =====================================================================
--  MIGRACIÓN 005 · DATOS EXTRA DE CANALIZACIÓN (registro desde el psicólogo)
--  Base de datos: PIT_V4   (requiere haber aplicado la 003)
--
--  Agrega a psico_canalizaciones los datos que captura el psicólogo en
--  MOD_PSICOLOGOS/nueva_canalizacion.php y que alimentan las gráficas del
--  inicio del psicólogo. Todas las columnas son opcionales, así que las
--  canalizaciones que hacen los tutores siguen funcionando igual.
--
--  CONFIDENCIALIDAD: las observaciones del psicólogo van en su propia columna
--  (observaciones_psicologo), que sist_psico_listar() solo lee con el perfil
--  'psicologo'. El tutor ve el motivo general (categoria_motivo), no las notas.
--
--  Ejecutar UNA vez (phpMyAdmin > pit_v4 > SQL). Es segura de repetir (MariaDB).
-- =====================================================================

ALTER TABLE psico_canalizaciones
    ADD COLUMN IF NOT EXISTS origen                ENUM('TUTOR','PSICOLOGO') NOT NULL DEFAULT 'TUTOR' AFTER estado,
    ADD COLUMN IF NOT EXISTS observaciones_psicologo TEXT     NULL AFTER motivo,
    ADD COLUMN IF NOT EXISTS categoria_motivo      VARCHAR(60)  NULL AFTER motivo,
    ADD COLUMN IF NOT EXISTS generacion            VARCHAR(20)  NULL AFTER categoria_motivo,
    ADD COLUMN IF NOT EXISTS semestre              TINYINT      NULL AFTER generacion,
    ADD COLUMN IF NOT EXISTS genero                VARCHAR(10)  NULL AFTER semestre,
    ADD COLUMN IF NOT EXISTS edad                  TINYINT      NULL AFTER genero,
    ADD COLUMN IF NOT EXISTS requiere_familiar     TINYINT(1)   NOT NULL DEFAULT 0 AFTER edad,
    ADD COLUMN IF NOT EXISTS requiere_depresion    TINYINT(1)   NOT NULL DEFAULT 0 AFTER requiere_familiar,
    ADD COLUMN IF NOT EXISTS requiere_psiquiatrico TINYINT(1)   NOT NULL DEFAULT 0 AFTER requiere_depresion;
