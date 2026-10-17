<?php
/*=========================================================
    SIST - FUNCIONES DE USO COMÚN
=========================================================*/

/* Escapa texto para imprimirlo en HTML (evita XSS) */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* Escapa texto para usarlo ENTRE COMILLAS dentro de una consulta SQL.
   Preferir siempre consultas preparadas; esto es el respaldo para
   el código antiguo que arma el SQL con variables. */
function sist_esc($valor): string
{
    $valor = (string) $valor;
    $c     = $GLOBALS['sist_ultima_conexion'] ?? null;

    if ($c instanceof mysqli) {
        return $c->real_escape_string($valor);
    }

    return addcslashes($valor, "\\'\"\0\n\r\x1a");
}

/* Convierte a entero (para ids en consultas) */
function sist_int($valor): int
{
    return (int) $valor;
}

/* Respuesta JSON y fin de la ejecución */
function sist_json($datos, int $estado = 200): void
{
    if (!headers_sent()) {
        http_response_code($estado);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function sist_redirigir(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* URL de un recurso del portal: sist_url_portal('MOD_ADMIN/inicioadmin.php') */
function sist_url_portal(string $ruta = ''): string
{
    return PORTAL_URL . '/' . ltrim($ruta, '/');
}

/* ¿La petición espera una respuesta de datos y no una página? */
function sist_es_peticion_ajax(): bool
{
    $dest = $_SERVER['HTTP_SEC_FETCH_DEST'] ?? '';
    if ($dest !== '') {
        return !in_array($dest, ['document', 'iframe', 'frame'], true);
    }

    if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
        return true;
    }

    return stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
}

function sist_log(string $mensaje): void
{
    error_log('[SIST] ' . $mensaje);
}

/*---------------------------------------------------------
    CSRF
    Formularios:   <?= sist_csrf_campo() ?>
    Endpoints POST: sist_requerir_csrf();
---------------------------------------------------------*/
function sist_csrf_token(): string
{
    sist_iniciar_sesion();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function sist_csrf_campo(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(sist_csrf_token()) . '">';
}

function sist_csrf_validar(?string $token = null): bool
{
    sist_iniciar_sesion();

    $token = $token ?? ($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));

    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

function sist_requerir_csrf(): void
{
    if (!sist_csrf_validar()) {
        sist_denegar(419, 'La sesión del formulario expiró. Recarga la página e inténtalo de nuevo.');
    }
}

/*---------------------------------------------------------
    SUBIDA DE ARCHIVOS SEGURA
    Misma firma que move_uploaded_file($tmp, $destino), pero:
      - valida extensión permitida (imágenes y PDF)
      - valida el tipo REAL del archivo (no solo el nombre)
      - rechaza nombres peligrosos (.php, .., rutas, dobles extensiones)
      - limita el tamaño
    Devuelve false si el archivo no es válido (y no lo guarda).
---------------------------------------------------------*/
function sist_mover_subida(string $tmp, string $destino): bool
{
    $prueba = defined('SIST_TESTING');

    if (!$prueba && !is_uploaded_file($tmp)) {
        return false;
    }

    /* El último tramo del destino, tal como llega, no puede traer barras
       invertidas ni '..' (en Windows permitirían salir de la carpeta) */
    $pos    = strrpos($destino, '/');
    $ultimo = ($pos === false) ? $destino : substr($destino, $pos + 1);

    if (strpos($ultimo, '\\') !== false || strpos($ultimo, '..') !== false) {
        sist_log('Subida rechazada (ruta sospechosa): ' . $ultimo);
        return false;
    }

    $nombre = basename(str_replace('\\', '/', $destino));

    if ($nombre === '' || $nombre[0] === '.' || preg_match('/[\x00-\x1f\/\\\\]/', $nombre)) {
        sist_log('Subida rechazada (nombre inválido): ' . $nombre);
        return false;
    }

    $partes = explode('.', strtolower($nombre));
    array_shift($partes);

    if (empty($partes)) {
        return false;
    }

    $peligrosas = ['php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8', 'pht', 'phps',
                   'cgi', 'pl', 'py', 'sh', 'exe', 'bat', 'cmd', 'js', 'html', 'htm', 'svg',
                   'htaccess', 'shtml', 'asp', 'aspx', 'jsp'];

    foreach ($partes as $parte) {
        if (in_array($parte, $peligrosas, true)) {
            sist_log('Subida rechazada (extensión peligrosa): ' . $nombre);
            return false;
        }
    }

    $ext = end($partes);

    $imagenes = ['jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'],
                 'png' => ['image/png'], 'gif' => ['image/gif'], 'webp' => ['image/webp']];
    $docs     = ['pdf' => ['application/pdf']];
    $permitidas = $imagenes + $docs;

    if (!isset($permitidas[$ext])) {
        sist_log('Subida rechazada (extensión no permitida): ' . $nombre);
        return false;
    }

    if (!is_file($tmp) || filesize($tmp) > SIST_MAX_SUBIDA || filesize($tmp) === 0) {
        return false;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);

    if (!in_array($mime, $permitidas[$ext], true)) {
        sist_log('Subida rechazada (tipo real no coincide): ' . $nombre . ' => ' . $mime);
        return false;
    }

    if (isset($imagenes[$ext]) && @getimagesize($tmp) === false) {
        return false;
    }

    $carpeta = dirname($destino);
    if (!is_dir($carpeta) && !mkdir($carpeta, 0755, true)) {
        return false;
    }

    return $prueba ? copy($tmp, $destino) : move_uploaded_file($tmp, $destino);
}

/* Sanea el nombre ORIGINAL de un archivo subido para poder usarlo
   como nombre en disco y en la base de datos (sin comillas, rutas
   ni caracteres de control). Conserva letras, números, . _ - ( ) y espacio. */
function sist_nombre_subida($nombre): string
{
    $nombre = basename(str_replace('\\', '/', (string) $nombre));
    $nombre = preg_replace('/[^\p{L}\p{N}._ ()\-]/u', '_', $nombre);
    $nombre = preg_replace('/\.{2,}/', '.', (string) $nombre);
    $nombre = ltrim((string) $nombre, '.');

    return mb_substr($nombre, 0, 120, 'UTF-8');
}

/*---------------------------------------------------------
    PAGINACIÓN
    $p = sist_paginacion($total, 20);
    ... "SELECT ... LIMIT ? OFFSET ?"  con  $p['limite'], $p['offset']
    $p['pagina'], $p['paginas'], $p['total']
    Siempre pagina los listados: traer tablas completas es lo que
    hace colapsar un sistema cuando crece el número de usuarios.
---------------------------------------------------------*/
function sist_paginacion(int $total, int $porPagina = 20): array
{
    $porPagina = max(1, min(200, $porPagina));
    $paginas   = max(1, (int) ceil($total / $porPagina));
    $pagina    = min($paginas, max(1, (int) ($_GET['pagina'] ?? 1)));

    return [
        'total'   => $total,
        'limite'  => $porPagina,
        'offset'  => ($pagina - 1) * $porPagina,
        'pagina'  => $pagina,
        'paginas' => $paginas,
    ];
}
