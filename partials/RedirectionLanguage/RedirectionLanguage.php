<?php
// ===============================
// Redirección automática por idioma (solo una vez)
// ===============================

// Si no existe la cookie 'lang_redirect', hacemos la detección
if (!isset($_COOKIE['lang_redirect'])) {
    // Crear cookie para no redirigir más (dura 30 días)
    setcookie('lang_redirect', '1', time() + (86400 * 30), "/");

    // Detectar idioma principal del navegador
    // (los bots como Googlebot no envían Accept-Language)
    $idioma = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);

    // ¿Ya estamos en una página en español? (index-es.php, etc.)
    $pagina_actual = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $ya_en_espanol = str_contains($pagina_actual, '-es.php');

    // Si el idioma es español, redirigir a la versión en español.
    // En local no existe la regla de reescritura "/es", así que
    // se apunta directamente al archivo.
    if ($idioma === 'es' && !$ya_en_espanol) {
        $destino = (defined('IS_LOCAL') && IS_LOCAL)
            ? BASE_URL . '/index-es.php'
            : BASE_URL . '/es';

        header('Location: ' . $destino);
        exit();
    }
}

// Si ya tiene la cookie o no es español, continúa normalmente con index.php
?>
