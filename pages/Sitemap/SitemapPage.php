<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   Sitemap-es.html + Sitemap.html
// Textos: pages/Sitemap/texts/sitemap.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Sitemap/texts/sitemap.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Sitemap/Sitemap.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Sitemap/CustomSelect.css">

<section id="sitemap" class="sm-section" aria-labelledby="sm-title">
  <div class="sm-container">
    <h2 id="sm-title" class="sm-title"<?= ozz_alt($T['sitemap.01_sm-title']) ?>><?= ozz_tx($T['sitemap.01_sm-title']) ?></h2>
    <p class="sm-desc"<?= ozz_alt_html($T['sitemap.02_sm-desc_html']) ?>><?= ozz_th($T['sitemap.02_sm-desc_html']) ?></p>

    <div class="sm-toolbar" role="region" aria-label="<?= ozz_tx($T['sitemap.03_sm-toolbar_aria-label']) ?>"<?= ozz_alt($T['sitemap.03_sm-toolbar_aria-label'], 'aria-label') ?>>
      <input id="sm-search" class="sm-search" type="search" placeholder="<?= ozz_tx($T['sitemap.04_sm-search_placeholder']) ?>" aria-label="<?= ozz_tx($T['sitemap.05_sm-search_aria-label']) ?>"<?= ozz_alt($T['sitemap.04_sm-search_placeholder'], 'placeholder') ?><?= ozz_alt($T['sitemap.05_sm-search_aria-label'], 'aria-label') ?>>
      <select id="sm-group" class="sm-select" aria-label="<?= ozz_tx($T['sitemap.06_sm-select_aria-label']) ?>"<?= ozz_alt($T['sitemap.06_sm-select_aria-label'], 'aria-label') ?>>
        <option value="none"<?= ozz_alt($T['sitemap.07_option']) ?>><?= ozz_tx($T['sitemap.07_option']) ?></option>
        <option value="prefix"<?= ozz_alt($T['sitemap.08_option']) ?>><?= ozz_tx($T['sitemap.08_option']) ?></option>
      </select>

      <!-- Select custom -->
      <div class="sm-select-wrapper">
        <div class="sm-select-display" tabindex="0"<?= ozz_alt($T['sitemap.09_sm-select-display']) ?>><?= ozz_tx($T['sitemap.09_sm-select-display']) ?></div>
        <ul class="sm-select-options">
          <li data-value="none"<?= ozz_alt($T['sitemap.10_li']) ?>><?= ozz_tx($T['sitemap.10_li']) ?></li>
          <li data-value="prefix"<?= ozz_alt($T['sitemap.11_li']) ?>><?= ozz_tx($T['sitemap.11_li']) ?></li>
        </ul>
      </div>
    </div>

    <div id="sm-list" class="sm-list" role="list" aria-live="polite"></div>

    <noscript>
      <p class="sm-desc"<?= ozz_alt_html($T['sitemap.12_sm-desc_html']) ?>><?= ozz_th($T['sitemap.12_sm-desc_html']) ?></p>
    </noscript>
  </div>
</section>

<script src="<?= ozz_e(ozz_asset('pages/Sitemap/SitemapList.js')) ?>"></script>
<script src="<?php echo $base_url; ?>/pages/Sitemap/CustomSelect.js"></script>
