<?php
// Página de inicio. ES (index-es.php) y EN (index.php) comparten
// templates/home.php: el idioma se detecta por el nombre del archivo.
require_once __DIR__ . '/config/bootstrap.php';
require_once ROOT_PATH . '/partials/RedirectionLanguage/RedirectionLanguage.php';
require ROOT_PATH . '/templates/home.php';
