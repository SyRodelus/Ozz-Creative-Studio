<?php
// =========================================================
// LAYOUT — inicio de página (ES / EN)
// ---------------------------------------------------------
// Variables esperadas antes de incluir:
//   $page            'home' | 'work' | 'contact'  (clave en lang/ui.json → meta)
//   $with_preloader  bool (opcional)
// El idioma se detecta por el archivo (*-es.php = español),
// así index.php e index-es.php comparten esta misma plantilla.
// =========================================================

require_once ROOT_PATH . '/config/helpers.php';

$layout_ui = ozz_json('lang/ui.json');
// $page_meta (opcional) permite pasar metadatos propios, p. ej. los de cada proyecto
$layout_meta = $page_meta ?? ($layout_ui['meta'][$page] ?? []);
$layout_lang = ozz_lang();
$layout_canon = $layout_meta['canonical'] ?? [];
?>
<!DOCTYPE html>
<html lang="<?= ozz_e($layout_lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title<?= ozz_alt($layout_meta['title'] ?? '') ?>><?= ozz_tx($layout_meta['title'] ?? 'Ozz') ?></title>
  <meta name="description" content="<?= ozz_tx($layout_meta['description'] ?? '') ?>"<?= ozz_alt($layout_meta['description'] ?? '', 'content') ?>>

  <?php if ($layout_canon): ?>
  <link rel="canonical" href="<?= ozz_e($layout_canon[$layout_lang] ?? '') ?>">
  <link rel="alternate" hreflang="es" href="<?= ozz_e($layout_canon['es'] ?? '') ?>">
  <link rel="alternate" hreflang="en" href="<?= ozz_e($layout_canon['en'] ?? '') ?>">
  <link rel="alternate" hreflang="x-default" href="<?= ozz_e($layout_canon['en'] ?? '') ?>">
  <?php endif; ?>

  <?php include ROOT_PATH . '/head.php'; ?>

  <!-- Open Graph / Twitter -->
  <meta property="og:title" content="<?= ozz_tx($layout_meta['ogTitle'] ?? '') ?>">
  <meta property="og:description" content="<?= ozz_tx($layout_meta['ogDescription'] ?? '') ?>">
  <meta property="og:image" content="https://ozzcreativestudio.com/social-share-image.png">
  <meta property="og:url" content="<?= ozz_e($layout_canon[$layout_lang] ?? '') ?>">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="<?= $layout_lang === 'es' ? 'es_CO' : 'en_US' ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= ozz_tx($layout_meta['ogTitle'] ?? '') ?>">
  <meta name="twitter:description" content="<?= ozz_tx($layout_meta['ogDescription'] ?? '') ?>">
  <meta name="twitter:image" content="https://ozzcreativestudio.com/social-share-image.png">

  <link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/index/Poster.css')) ?>">
</head>

<body>
<?php
if (!empty($with_preloader)) {
    include ROOT_PATH . '/pages/index/Preloader.html';
}

include ROOT_PATH . '/Features/Menu/Menu.php';
include ROOT_PATH . '/Features/Tools/Tools.php';
include ROOT_PATH . '/partials/ButtonWhatsapp/BtnWhatsapp.html';
?>
<main id="main">
