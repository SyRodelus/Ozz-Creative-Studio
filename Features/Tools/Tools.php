<?php
// HERRAMIENTAS FLOTANTES: idioma + tema
// - En páginas unificadas (con Scripts/LangSwitch.js) el botón de idioma
//   cambia los textos sin recargar; en el resto funciona como enlace normal.
require_once ROOT_PATH . '/config/helpers.php';

$tools_ui = ozz_json('lang/ui.json')['tools'] ?? [];
$tools_alt_url = ozz_current_page_url(ozz_other_lang());
?>
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('Features/Tools/Tools.css')) ?>" />

<div id="nav-tools-floating">
  <!-- IDIOMA -->
  <a
    href="<?= ozz_e($tools_alt_url) ?>"
    data-alt-href="<?= ozz_e(ozz_current_page_url(ozz_lang())) ?>"
    class="nav-idiom"
    data-lang-switch
    hreflang="<?= ozz_e(ozz_other_lang()) ?>"
    data-alt-hreflang="<?= ozz_e(ozz_lang()) ?>"
    title="<?= ozz_tx($tools_ui['langTitle'] ?? '') ?>"<?= ozz_alt($tools_ui['langTitle'] ?? '', 'title') ?>
  >
    <div class="nav-item language" id="ChangeIdiom">
      <span class="language-text"<?= ozz_alt($tools_ui['langLabel'] ?? '') ?>><?= ozz_tx($tools_ui['langLabel'] ?? '') ?></span>
    </div>
  </a>

  <!-- TEMA -->
  <div class="nav-item theme" id="theme-toggle">
    <?php include ROOT_PATH . '/Icons/ThemeIcons.svg'; ?>
  </div>
</div>

<script src="<?= ozz_e(ozz_asset('Features/Tools/SelectorTheme.js')) ?>"></script>
<script src="<?= ozz_e(ozz_asset('Features/Tools/Theme.js')) ?>"></script>
