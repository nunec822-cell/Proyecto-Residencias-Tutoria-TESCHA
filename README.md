# SIST — Sistema Institucional de Tutorías (v4.1)

Sitio público + Portal Institucional de Tutorías (PIT) con roles: administrador,
directivo, jefe de carrera, coordinador, docente, tutor, psicólogo y tutorado.
PHP + MySQL/MariaDB, pensado para XAMPP (Apache + PHP 8).

## Instalación rápida

1. Copia esta carpeta dentro de `htdocs` (el nombre de la carpeta no importa: la URL base se detecta sola).
2. Crea las bases de datos `PIT_V4` y `gestor_contenidos` e importa tus datos.
3. Aplica las migraciones (una sola vez cada una, son seguras de repetir) sobre la base `PIT_V4`:
   `database/migraciones/001_permisos.sql` y `database/migraciones/002_asesorias_notificaciones.sql`.
4. Credenciales de base de datos: copia `config/local.example.php` como `config/local.php`
   y ajusta. (Sin ese archivo se usan los valores de XAMPP: `root` sin contraseña.)
5. Abre `http://localhost/<carpeta>/portal/login.php`.

## Estructura

```
sist/
├── index.php, vista_*/          Sitio público
├── includes/, datos_footer/     Piezas del sitio público (header, footer, menú)
├── portal/                      Portal PIT
│   ├── login.php, validar_login.php, cerrar_sesion.php, sesion.php
│   ├── MOD_<ROL>/               Un módulo por rol (ADMIN, DOCENTE, TUTOR, …)
│   ├── includes_pit/            Header y sidebars del portal
│   └── base_pit/                Conexión a PIT_V4 (usa config/)
├── vista_contenidos/            CMS del sitio público (solo administrador)
├── config/                      app.php (URL/rutas) · database.php · permisos.php
├── core/                        bootstrap, auth, permisos, db, helpers  ← el "motor"
├── database/migraciones/        SQL versionado
├── docs/                        Guías + plantilla_modulo/ + pruebas/
└── assets/                      Imágenes y recursos compartidos
```

## Lo esencial para programar aquí

* **Todo archivo PHP empieza igual** (ver `docs/CONVENCIONES.md`):
  ```php
  require_once __DIR__ . '/../../core/bootstrap.php';   // ajusta los ../ a la profundidad
  requerir_permiso('modulo.accion');                    // quién puede entrar
  ```
* **Permisos, no roles**: `requerir_permiso('clave')`. Los roles agrupan permisos y se
  administran en el portal: *Administrador → Permisos*.
* **Consultas preparadas siempre** (`prepare` + `bind_param`) y **listados paginados**
  (`sist_paginacion`). **Imprime con `e()`**. **Formularios POST con `sist_csrf_campo()`**.
* **Módulo nuevo**: copia `docs/plantilla_modulo/` (listado + formulario + acción + API, ya probada).

## Más documentación

* `docs/CONVENCIONES.md` — cómo crear un módulo nuevo paso a paso.
* `docs/ASESORIAS.md` — flujo de asesorías + notificaciones (campanita y correo), instalación y pruebas.
* `docs/CAMBIOS_V4.1.md` — qué se cambió, qué se probó y **qué quedó pendiente**.
* `docs/pruebas/` — pruebas de humo repetibles del control de acceso.
