<?php
// =======================================================
// BOOTSTRAP GLOBAL
// =======================================================

/*
|--------------------------------------------------------------------------
| 0) Sesión
|--------------------------------------------------------------------------
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| 1) Configuración del proyecto
|--------------------------------------------------------------------------
*/
const PROJECT_FOLDER    = 'Ozz1';
const PRODUCTION_DOMAIN = 'ozzcreativestudio.com';

/*
|--------------------------------------------------------------------------
| 2) Detección de entorno
|--------------------------------------------------------------------------
*/
$http_host = $_SERVER['HTTP_HOST'] ?? '';

$is_local = (
    $http_host !== '' &&
    (
        $http_host === 'localhost' ||
        str_starts_with($http_host, 'localhost:') ||
        $http_host === '127.0.0.1' ||
        str_starts_with($http_host, '127.0.0.1:') ||
        str_starts_with($http_host, '192.168.') ||
        str_starts_with($http_host, '10.')
    )
);

/*
|--------------------------------------------------------------------------
| 3) ROOT PATH
|--------------------------------------------------------------------------
*/
$document_root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');

$root_path = $is_local
    ? $document_root . '/' . PROJECT_FOLDER
    : $document_root;

$root_path = str_replace('\\', '/', $root_path);

/*
|--------------------------------------------------------------------------
| 4) BASE URL
|--------------------------------------------------------------------------
*/
$https = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

$scheme = $https ? 'https' : 'http';

$host = $http_host !== ''
    ? $http_host
    : PRODUCTION_DOMAIN;

$base_url = $scheme . '://' . $host;

if ($is_local) {
    $base_url .= '/' . PROJECT_FOLDER;
}

$base_url = rtrim($base_url, '/');

/*
|--------------------------------------------------------------------------
| 5) Constantes globales
|--------------------------------------------------------------------------
*/
defined('ROOT_PATH') || define('ROOT_PATH', $root_path);
defined('BASE_URL')  || define('BASE_URL', $base_url);
defined('IS_LOCAL')  || define('IS_LOCAL', $is_local);

/*
|--------------------------------------------------------------------------
| 6) Validación
|--------------------------------------------------------------------------
*/
if (!is_dir(ROOT_PATH)) {
    die(
        '<strong>Bootstrap error:</strong> ROOT_PATH inválido → ' .
        htmlspecialchars(ROOT_PATH, ENT_QUOTES, 'UTF-8')
    );
}

/*
|--------------------------------------------------------------------------
| 7) Función opcional para imprimir variables JS
|--------------------------------------------------------------------------
| Llamar donde se necesite, por ejemplo dentro del <head>.
|--------------------------------------------------------------------------
*/
if (!function_exists('printBootstrapJS')) {
    function printBootstrapJS(): void
    {
        ?>
        <script>
        window.BASE_URL = <?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES) ?>;
        window.IS_LOCAL = <?= json_encode(IS_LOCAL) ?>;
        </script>
        <?php
    }
}