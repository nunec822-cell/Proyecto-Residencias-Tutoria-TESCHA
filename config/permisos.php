<?php
/*=========================================================
    SIST - CATÁLOGO DE PERMISOS (valores por defecto)

    El código verifica PERMISOS (tiene_permiso / requerir_permiso),
    no roles. Los roles de la tabla `tipos` agrupan permisos.

    - Este archivo es el catálogo base y sirve de respaldo si las
      tablas de permisos aún no existen en la base de datos.
    - Una vez aplicada database/migraciones/001_permisos.sql, la
      base de datos manda y se administra desde:
      portal/MOD_ADMIN/permisos/
    - ADMIN siempre tiene todos los permisos.

    Para un módulo nuevo: agrega su permiso aquí, en el SQL de
    migración, y llama requerir_permiso('modulo.accion').
=========================================================*/

return [

    /* clave => [descripción, módulo] */
    'catalogo' => [
        'usuarios.consultar'        => ['Consultar y exportar el directorio de usuarios', 'Administración'],
        'usuarios.gestionar'        => ['Registrar y editar usuarios', 'Administración'],
        'grupos.asignar'            => ['Asignar grupos y materias a docentes', 'Administración'],
        'avisos.gestionar'          => ['Crear, editar y eliminar avisos internos', 'Avisos'],
        'contenidos.gestionar'      => ['Administrar los contenidos del sitio público', 'Contenidos'],
        'permisos.gestionar'        => ['Administrar permisos de roles y usuarios', 'Administración'],
        'anexo14.crear'             => ['Capturar reportes del Anexo 14', 'Canalizaciones'],
        'anexo14.consultar'         => ['Consultar reportes del Anexo 14', 'Canalizaciones'],
        'canalizaciones.tomar'      => ['Tomar y dar seguimiento a canalizaciones', 'Canalizaciones'],
        'canalizaciones.ver_todas'  => ['Ver canalizaciones de todas las carreras', 'Canalizaciones'],
        'canalizaciones.ver_carrera'=> ['Ver canalizaciones de su carrera', 'Canalizaciones'],
        'coordinacion.ver'          => ['Panel de coordinación', 'Coordinación'],
        'psicologia.citas'          => ['Gestionar citas y agenda de psicología', 'Psicología'],
        'psicologia.avisos'         => ['Gestionar avisos de psicología', 'Psicología'],
        'tutorado.panel'            => ['Panel del tutorado', 'Tutorados'],
        'asesorias.canalizar'       => ['Canalizar a un alumno tutorado a asesoría académica', 'Asesorías'],
        'asesorias.asignar'         => ['Asignar profesor, fecha y hora a las asesorías', 'Asesorías'],
        'asesorias.seguimiento'     => ['Dar seguimiento a las asesorías asignadas', 'Asesorías'],
        'asesorias.ver_todas'       => ['Consultar todas las asesorías (solo lectura)', 'Asesorías'],
        'psicologia.avisar'         => ['Avisar al tutor que un alumno necesita atención psicológica', 'Psicología'],
        'psicologia.canalizar'      => ['Canalizar a un tutorado a atención psicológica', 'Psicología'],
        'psicologia.atender'        => ['Atender canalizaciones y registrar sesiones (psicólogo)', 'Psicología'],
        'psicologia.ver_seguimiento'=> ['Ver el historial de seguimiento psicológico (sin datos sensibles)', 'Psicología'],
        'psicologia.mi_seguimiento' => ['Ver su propio historial de seguimiento psicológico', 'Psicología'],
    ],

    /* ROL (tipos.nombre) => permisos por defecto */
    'roles' => [
        'DOCENTE'      => ['anexo14.crear', 'anexo14.consultar', 'asesorias.seguimiento', 'psicologia.avisar'],
        'TUTOR'        => ['anexo14.crear', 'anexo14.consultar', 'canalizaciones.tomar', 'asesorias.canalizar', 'asesorias.seguimiento', 'psicologia.avisar', 'psicologia.canalizar'],
        'COORDINADOR'  => ['anexo14.crear', 'anexo14.consultar', 'coordinacion.ver', 'asesorias.seguimiento', 'psicologia.avisar'],
        'DIRECTIVO'    => ['canalizaciones.ver_todas', 'asesorias.ver_todas'],
        'JEFE_CARRERA' => ['canalizaciones.ver_carrera', 'asesorias.asignar', 'psicologia.ver_seguimiento'],
        'PSICOLOGO'    => ['psicologia.citas', 'psicologia.avisos', 'psicologia.atender'],
        'TUTORADO'     => ['tutorado.panel', 'psicologia.mi_seguimiento'],
    ],
];
