<?php
// =========================================================
// ENVÍO DEL FORMULARIO DE CONTACTO (ES / EN)
// ---------------------------------------------------------
// - Un solo manejador para ambos idiomas: el idioma llega en
//   el campo "lang" (sendemail-es.php fuerza español).
// - Campos: firstName, lastName, email, accept (obligatorios)
//           company, services[], budget, message (opcionales)
// - Credenciales SMTP: config/mail.php
// - Respuesta JSON: { ok, message, html, new_csrf_token }
//   ("html" se mantiene por compatibilidad con el formulario
//   anterior del footer).
// =========================================================

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------
// Idioma y mensajes
// ---------------------------------------------------------
$lang = defined('OZZ_MAIL_LANG')
    ? OZZ_MAIL_LANG
    : (($_POST['lang'] ?? 'es') === 'en' ? 'en' : 'es');

$MSG = [
    'es' => [
        'csrf'     => 'Su sesión ha expirado. Por favor recargue la página e inténtelo de nuevo.',
        'invalid'  => 'No fue posible validar el formulario.',
        'flood'    => 'Ya recibimos un mensaje suyo hace un momento. Por favor espere un minuto antes de volver a intentarlo.',
        'email'    => 'Ingrese un correo electrónico válido.',
        'accept'   => 'Debe autorizar el tratamiento de sus datos para continuar.',
        'first'    => 'Ingrese un nombre válido (1 a 25 letras).',
        'last'     => 'Ingrese un apellido válido (1 a 25 letras).',
        'numbers'  => 'El nombre y el apellido no pueden contener números.',
        'format'   => 'El formato de los datos no es válido.',
        'message'  => 'El mensaje no puede superar los 1.500 caracteres.',
        'error'    => 'No fue posible enviar su solicitud. Por favor inténtelo de nuevo o escríbanos a team@ozzcreativestudio.com.',
        'success'  => 'Gracias por escribirnos. Hemos recibido su solicitud y nos pondremos en contacto con usted a la brevedad.',
    ],
    'en' => [
        'csrf'     => 'Your session has expired. Please reload the page and try again.',
        'invalid'  => 'The form could not be validated.',
        'flood'    => 'We received a message from you a moment ago. Please wait a minute before trying again.',
        'email'    => 'Please enter a valid email address.',
        'accept'   => 'You must authorize the processing of your data to continue.',
        'first'    => 'Please enter a valid first name (1 to 25 letters).',
        'last'     => 'Please enter a valid last name (1 to 25 letters).',
        'numbers'  => 'First and last name cannot contain numbers.',
        'format'   => 'The data format is not valid.',
        'message'  => 'The message cannot exceed 1,500 characters.',
        'error'    => 'We could not send your request. Please try again or write to team@ozzcreativestudio.com.',
        'success'  => 'Thank you for reaching out. We have received your request and will contact you shortly.',
    ],
];

$m = $MSG[$lang];

// ---------------------------------------------------------
// Utilidades
// ---------------------------------------------------------
final class FormError extends \RuntimeException {}

function clean_text($input, int $max): string
{
    $input = trim((string) $input);
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $input);
    return mb_substr($input, 0, $max);
}

function has_header_injection(string $str): bool
{
    return (bool) preg_match("/[\r\n]|%0A|%0D|Content-Type:|bcc:|cc:/i", $str);
}

function check_ip_flood(string $ip, string $logPath): bool
{
    $now = time();
    $kept = [];
    $found = false;

    if (!is_file($logPath)) {
        @touch($logPath);
    }

    $lines = is_file($logPath) ? file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    foreach ($lines as $line) {
        $parts = explode('|', trim($line));
        if (count($parts) < 2) {
            continue;
        }
        [$loggedIp, $timestamp] = $parts;
        if ($now - (int) $timestamp < 60) {
            if ($loggedIp === $ip) {
                $found = true;
            }
            $kept[] = "$loggedIp|$timestamp";
        }
    }

    if (!$found) {
        $kept[] = "$ip|$now";
    }

    @file_put_contents($logPath, implode(PHP_EOL, $kept) . PHP_EOL, LOCK_EX);

    return $found;
}

function respond(array $data): void
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
    exit;
}

// ---------------------------------------------------------
// Proceso
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(['ok' => false, 'message' => $m['invalid']]);
}

try {
    // CSRF
    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals((string) $_SESSION['csrf_token'], (string) $_POST['csrf_token'])
    ) {
        throw new FormError($m['csrf']);
    }

    // Honeypot
    if (!empty($_POST['website'] ?? '')) {
        throw new FormError($m['invalid']);
    }

    // Campos obligatorios
    $firstName = clean_text($_POST['firstName'] ?? '', 25);
    $lastName  = clean_text($_POST['lastName'] ?? '', 25);
    $email     = clean_text($_POST['email'] ?? '', 120);
    $accept    = in_array($_POST['accept'] ?? '', ['on', '1', 'true'], true);

    // Campos opcionales
    $company = clean_text($_POST['company'] ?? '', 80);
    $message = trim((string) ($_POST['message'] ?? ''));

    $allowedServices = ['WebDevelopment', 'Branding', 'Campaigns', 'Other'];
    $services = array_values(array_intersect(
        array_map('strval', (array) ($_POST['services'] ?? [])),
        $allowedServices
    ));

    $budgetOptions = [
        'Por definir / To be defined',
        'Menos de USD 2.000 / Under USD 2,000',
        'USD 2.000 – 5.000',
        'USD 5.000 – 10.000',
        'Más de USD 10.000 / Over USD 10,000',
    ];
    $budgetIndex = (int) ($_POST['budget'] ?? 0);
    $budget = $budgetOptions[$budgetIndex] ?? $budgetOptions[0];

    // Validaciones
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new FormError($m['email']);
    }
    if (!$accept) {
        throw new FormError($m['accept']);
    }
    if (mb_strlen($firstName) < 1) {
        throw new FormError($m['first']);
    }
    if (mb_strlen($lastName) < 1) {
        throw new FormError($m['last']);
    }
    if (preg_match('/\d/', $firstName . $lastName)) {
        throw new FormError($m['numbers']);
    }
    if (mb_strlen($message) > 1500) {
        throw new FormError($m['message']);
    }
    if (
        has_header_injection($firstName) ||
        has_header_injection($lastName) ||
        has_header_injection($email) ||
        has_header_injection($company)
    ) {
        throw new FormError($m['format']);
    }

    // Anti-flood por IP (después de validar, para no bloquear correcciones)
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (check_ip_flood($ip, __DIR__ . '/../logs/ip_log.txt')) {
        throw new FormError($m['flood']);
    }

    // Envío
    $cfg = require __DIR__ . '/../config/mail.php';

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $cfg['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $cfg['username'];
    $mail->Password   = $cfg['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $cfg['port'];
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($cfg['from'], $cfg['fromName']);
    foreach ($cfg['to'] as $to) {
        $mail->addAddress($to);
    }
    $mail->addReplyTo($email, "$firstName $lastName");

    $mail->isHTML(false);
    $mail->Subject = "Nuevo contacto: {$firstName} {$lastName}" . ($company !== '' ? " ({$company})" : '');
    $mail->Body =
        "Nuevo mensaje del sitio web\n\n" .
        "Nombre: {$firstName} {$lastName}\n" .
        "Email: {$email}\n" .
        ($company !== '' ? "Empresa: {$company}\n" : '') .
        ($services ? "Servicios: " . implode(', ', $services) . "\n" : '') .
        "Presupuesto: {$budget}\n" .
        "Idioma: " . strtoupper($lang) . "\n\n" .
        ($message !== '' ? "Mensaje:\n{$message}\n\n" : '') .
        "IP: {$ip}\n" .
        "Fecha: " . date('d/m/Y H:i:s');

    $mail->send();

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    respond([
        'ok'             => true,
        'message'        => $m['success'],
        'html'           => '<div class="alert-success" id="Alerta"><span>' . htmlspecialchars($m['success'], ENT_QUOTES, 'UTF-8') . '</span></div>',
        'new_csrf_token' => $_SESSION['csrf_token'],
    ]);
} catch (FormError $e) {
    respond([
        'ok'      => false,
        'message' => $e->getMessage(),
        'html'    => '<div class="alert-error" id="Alerta"><span>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</span></div>',
    ]);
} catch (\Throwable $e) {
    error_log('Error en sendemail: ' . $e->getMessage() . ' - IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    respond([
        'ok'      => false,
        'message' => $m['error'],
        'html'    => '<div class="alert-error" id="Alerta"><span>' . htmlspecialchars($m['error'], ENT_QUOTES, 'UTF-8') . '</span></div>',
    ]);
}
