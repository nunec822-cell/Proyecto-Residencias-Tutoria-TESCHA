<?php
/*=========================================================
    SIST - ENVÍO DE CORREO (PHPMailer + SMTP)

    sist_correo_activo()                  -> bool (¿está configurado el SMTP?)
    sist_correo_enviar($para, $asunto, $html, $texto) -> [bool ok, string error]

    Nunca lanza excepciones: devuelve [false, 'motivo'].
    Normalmente NO se llama directo: las notificaciones dejan el
    correo en la cola (correos_pendientes) y sist_correos_procesar()
    (core/notificaciones.php) lo envía sin bloquear ni romper el flujo.
=========================================================*/

function sist_correo_config(): array
{
    static $config = null;

    if ($config === null) {
        $config = require SIST_ROOT . '/config/correo.php';
    }

    return $config;
}

function sist_correo_activo(): bool
{
    $c = sist_correo_config();

    /* Para un servidor de pruebas local (smtp4dev) no hace falta usuario ni clave */
    return !empty($c['activo']) && $c['host'] !== '';
}

function sist_correo_valido($correo): bool
{
    return is_string($correo)
        && strlen($correo) <= 150
        && filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Envía un correo por SMTP.
 * @return array [bool $ok, string $error]
 */
function sist_correo_enviar(string $para, string $asunto, string $html, string $texto): array
{
    if (!sist_correo_activo()) {
        return [false, 'Correo desactivado (config/correo.local.php)'];
    }

    if (!sist_correo_valido($para)) {
        return [false, 'Destinatario con correo no válido'];
    }

    try {
        require_once SIST_ROOT . '/core/lib/PHPMailer/Exception.php';
        require_once SIST_ROOT . '/core/lib/PHPMailer/PHPMailer.php';
        require_once SIST_ROOT . '/core/lib/PHPMailer/SMTP.php';

        $c = sist_correo_config();
        $m = new PHPMailer\PHPMailer\PHPMailer(true);

        $m->isSMTP();
        $m->Host       = $c['host'];
        $m->Port       = (int) $c['puerto'];
        $m->SMTPAuth   = ($c['usuario'] !== '');   /* sin usuario = sin autenticación */
        $m->Username   = $c['usuario'];
        $m->Password   = $c['clave'];
        $m->Timeout    = (int) $c['timeout'];
        $m->CharSet    = 'UTF-8';

        $seguridad = strtolower((string) $c['seguridad']);
        if ($seguridad === 'ssl') {
            $m->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($seguridad === 'tls') {
            $m->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $m->SMTPSecure  = '';
            $m->SMTPAutoTLS = false;
        }

        /* Remitente: 'de', o el usuario SMTP; si no es una dirección válida se usa una genérica
           (el servidor de pruebas rechaza direcciones como sist@localhost) */
        $de = $c['de'] !== '' ? $c['de'] : $c['usuario'];
        if (!sist_correo_valido($de)) {
            $de = 'sist@sist.local';
        }
        $m->setFrom($de, $c['nombre']);
        $m->addAddress($para);

        $m->isHTML(true);
        $m->Subject = $asunto;
        $m->Body    = $html;
        $m->AltBody = $texto;

        $m->send();

        return [true, ''];
    } catch (Throwable $e) {
        /* Mensaje corto y sin credenciales */
        return [false, mb_substr($e->getMessage(), 0, 250)];
    }
}

/** Plantilla HTML sencilla y segura (todo se escapa) para los avisos */
function sist_correo_plantilla(string $titulo, string $mensaje, string $enlace = ''): array
{
    $t = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $m = nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'));

    $html = '<div style="font-family:Arial,Helvetica,sans-serif;max-width:560px;margin:auto;'
          . 'border:1px solid #ddd;border-radius:8px;overflow:hidden">'
          . '<div style="background:#8B1C1C;color:#fff;padding:14px 18px;font-size:18px">'
          . 'SIST · Programa Institucional de Tutorías</div>'
          . '<div style="padding:18px;color:#222;font-size:15px;line-height:1.5">'
          . '<h2 style="margin:0 0 10px;font-size:18px;color:#8B1C1C">' . $t . '</h2>'
          . '<p style="margin:0 0 14px">' . $m . '</p>';

    if ($enlace !== '') {
        $h = htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8');
        $html .= '<p><a href="' . $h . '" style="background:#8B1C1C;color:#fff;padding:10px 16px;'
               . 'border-radius:6px;text-decoration:none;display:inline-block">Abrir en el portal</a></p>';
    }

    $html .= '<p style="color:#777;font-size:12px;margin-top:18px">Este es un aviso automático; '
           . 'no respondas a este correo.</p></div></div>';

    $texto = $titulo . "\n\n" . $mensaje . ($enlace !== '' ? "\n\nAbrir en el portal: " . $enlace : '')
           . "\n\n(Aviso automático del SIST; no respondas a este correo.)";

    return [$html, $texto];
}
