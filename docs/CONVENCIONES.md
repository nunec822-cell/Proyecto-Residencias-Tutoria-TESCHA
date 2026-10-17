# Cómo crear un módulo nuevo

Ejemplo: el módulo de **asesorías** o de **canalización psicológica**.

## 1. Define sus permisos
Elige claves `modulo.accion` (ej.: `asesorias.solicitar`, `asesorias.asignar`,
`psicologia.expediente`, `psicologia.estatus`).

* Agrégalas en `config/permisos.php` (catálogo y permisos por defecto de cada rol).
* Agrégalas en una migración nueva: `database/migraciones/002_<modulo>.sql`
  (mismo formato que `001_permisos.sql`: `INSERT IGNORE INTO permisos…` y `rol_permiso`).
* Aplica la migración. Luego se pueden ajustar desde *Administrador → Permisos*.

## 2. Copia la plantilla
`docs/plantilla_modulo/`  →  `portal/MOD_<ROL>/<nombre_modulo>/`

```
<nombre_modulo>/
├── index.php          pantalla (listado paginado + formulario)
├── acciones/          archivos que MODIFICAN datos (solo POST + CSRF)
├── api/               endpoints que devuelven JSON (para fetch)
├── assets/            modulo.css y modulo.js propios
└── schema.sql         tablas del módulo (pásalas a database/migraciones/)
```

## 3. Reglas de oro (la plantilla ya las cumple)
1. Primera línea útil: `bootstrap` + `requerir_permiso(...)`. **Sin excepciones, tampoco en endpoints AJAX.**
2. Acciones que modifican datos: solo `POST`, `sist_requerir_csrf()`, validar, `prepare()`, redirigir.
3. Nunca armes SQL con variables: `WHERE id = ?` + `bind_param`. Para ids de la URL: `(int)`.
4. Listados: `COUNT(*)` + `sist_paginacion()` + `LIMIT ? OFFSET ?`. Índice en las columnas que filtras/ordenas.
5. Imprimir datos: `<?= e($valor) ?>`.
6. Archivos subidos: `sist_nombre_subida()` para el nombre y `sist_mover_subida($tmp, $destino)`
   (valida tipo real, extensión y nombre). No uses `move_uploaded_file` directo.
7. No guardes datos sensibles de más. Para información de salud/psicológica: acceso solo por permiso
   específico, y los roles que no son psicólogo ven únicamente un **estatus** (nunca el expediente).
8. CSS/JS del módulo dentro de su carpeta `assets/`, con clases prefijadas con el nombre del módulo.

## 4. Enlázalo
Agrega el `<li>` en el sidebar del rol (`portal/includes_pit/sidebar_<rol>.php`) y, si quieres que
solo lo vea quien tiene permiso: `<?php if (tiene_permiso('modulo.ver')): ?> … <?php endif; ?>`.

## Funciones disponibles (core/)
| Función | Para qué |
|---|---|
| `requerir_login()`, `requerir_rol('X')`, `requerir_permiso('k')` | Proteger el archivo |
| `tiene_permiso('k')`, `tiene_rol('X')` | Mostrar/ocultar partes de una pantalla |
| `e($v)` | Escapar al imprimir |
| `sist_csrf_campo()`, `sist_requerir_csrf()` | Protección de formularios |
| `sist_paginacion($total, $porPagina)` | Paginar listados |
| `sist_json($datos, $codigo)` | Responder JSON |
| `sist_nombre_subida()`, `sist_mover_subida()` | Subir archivos de forma segura |
| `sist_esc()`, `sist_int()` | Solo para código antiguo con SQL armado a mano |
| `PORTAL_URL`, `BASE_URL`, `SIST_ROOT` | Rutas (nunca escribas `/SIST V.4.0/...`) |
