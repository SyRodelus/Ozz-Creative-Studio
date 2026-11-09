<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   BoxsTexts-es.html + BoxsTexts.html
// Textos: pages/Studio/texts/boxes.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Studio/texts/boxes.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Studio/BoxsTexts.css">

<section id="services" class="services">
  <div class="services__header">
    <h2 class="services__title"<?= ozz_alt($T['boxes.01_services__title']) ?>><?= ozz_tx($T['boxes.01_services__title']) ?></h2>
  </div>

  <div class="services__grid">
    <!-- Columna: tarjetas -->
    <div class="services__cards">
      <button class="service-card" data-modal="brand-seeds" aria-haspopup="dialog">
        <span<?= ozz_alt($T['boxes.02_span']) ?>><?= ozz_tx($T['boxes.02_span']) ?></span>
      </button>
      <button class="service-card" data-modal="visual-identity" aria-haspopup="dialog">
        <span<?= ozz_alt($T['boxes.03_span']) ?>><?= ozz_tx($T['boxes.03_span']) ?></span>
      </button>
      <button class="service-card" data-modal="development-strategy" aria-haspopup="dialog">
        <span<?= ozz_alt($T['boxes.04_span']) ?>><?= ozz_tx($T['boxes.04_span']) ?></span>
      </button>
      <button class="service-card" data-modal="content-direction" aria-haspopup="dialog">
        <span<?= ozz_alt($T['boxes.05_span']) ?>><?= ozz_tx($T['boxes.05_span']) ?></span>
      </button>
      <button class="service-card" data-modal="brand-activation" aria-haspopup="dialog">
        <span<?= ozz_alt($T['boxes.06_span']) ?>><?= ozz_tx($T['boxes.06_span']) ?></span>
      </button>
      <button class="service-card" data-modal="creative-strategy" aria-haspopup="dialog">
        <span<?= ozz_alt($T['boxes.07_span']) ?>><?= ozz_tx($T['boxes.07_span']) ?></span>
      </button>
    </div>

    <!-- Columna: imagen -->
    <div class="services__imagery">
      <figure class="services__photo" aria-hidden="true">
        <img src="<?php echo $base_url; ?>/pages/Studio/Mask%20group.webp" alt="<?= ozz_tx($T['boxes.08_img_alt']) ?>"<?= ozz_alt($T['boxes.08_img_alt'], 'alt') ?>>
      </figure>
    </div>
  </div>

  <!-- Modal -->
  <div class="service-modal" id="service-modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="service-modal__dialog" role="document">
      <button class="service-modal__close" type="button" aria-label="<?= ozz_tx($T['boxes.09_service-modal__close_aria-label']) ?>"<?= ozz_alt($T['boxes.09_service-modal__close_aria-label'], 'aria-label') ?>>
        ✕
      </button>
      <div class="service-modal__scroll">
        <h3 class="service-modal__title" id="service-modal-title"></h3>
        <p class="service-modal__desc" id="service-modal-desc"></p>
        <ul class="service-modal__bullets" id="service-modal-bullets"></ul>
      </div>
    </div>
  </div>

  <!-- Plantillas -->
  <template id="tpl-brand-seeds">
    <h4 data-title<?= ozz_alt($T['boxes.10_h4']) ?>><?= ozz_tx($T['boxes.10_h4']) ?></h4>
    <p data-desc<?= ozz_alt($T['boxes.11_p']) ?>><?= ozz_tx($T['boxes.11_p']) ?></p>
    <ul data-bullets>
      <li<?= ozz_alt($T['boxes.12_li']) ?>><?= ozz_tx($T['boxes.12_li']) ?></li>
      <li<?= ozz_alt($T['boxes.13_li']) ?>><?= ozz_tx($T['boxes.13_li']) ?></li>
      <li<?= ozz_alt($T['boxes.14_li']) ?>><?= ozz_tx($T['boxes.14_li']) ?></li>
    </ul>
  </template>

  <template id="tpl-visual-identity">
    <h4 data-title<?= ozz_alt($T['boxes.15_h4']) ?>><?= ozz_tx($T['boxes.15_h4']) ?></h4>
    <p data-desc<?= ozz_alt($T['boxes.16_p']) ?>><?= ozz_tx($T['boxes.16_p']) ?></p>
    <ul data-bullets>
      <li<?= ozz_alt($T['boxes.17_li']) ?>><?= ozz_tx($T['boxes.17_li']) ?></li>
      <li<?= ozz_alt($T['boxes.18_li']) ?>><?= ozz_tx($T['boxes.18_li']) ?></li>
      <li<?= ozz_alt($T['boxes.19_li']) ?>><?= ozz_tx($T['boxes.19_li']) ?></li>
    </ul>
  </template>

  <template id="tpl-development-strategy">
    <h4 data-title<?= ozz_alt($T['boxes.20_h4']) ?>><?= ozz_tx($T['boxes.20_h4']) ?></h4>
    <p data-desc<?= ozz_alt($T['boxes.21_p']) ?>><?= ozz_tx($T['boxes.21_p']) ?></p>
    <ul data-bullets>
      <li<?= ozz_alt($T['boxes.22_li']) ?>><?= ozz_tx($T['boxes.22_li']) ?></li>
      <li<?= ozz_alt($T['boxes.23_li']) ?>><?= ozz_tx($T['boxes.23_li']) ?></li>
      <li<?= ozz_alt($T['boxes.24_li']) ?>><?= ozz_tx($T['boxes.24_li']) ?></li>
    </ul>
  </template>

  <template id="tpl-content-direction">
    <h4 data-title<?= ozz_alt($T['boxes.25_h4']) ?>><?= ozz_tx($T['boxes.25_h4']) ?></h4>
    <p data-desc<?= ozz_alt($T['boxes.26_p']) ?>><?= ozz_tx($T['boxes.26_p']) ?></p>
    <ul data-bullets>
      <li<?= ozz_alt($T['boxes.27_li']) ?>><?= ozz_tx($T['boxes.27_li']) ?></li>
      <li<?= ozz_alt($T['boxes.28_li']) ?>><?= ozz_tx($T['boxes.28_li']) ?></li>
      <li<?= ozz_alt($T['boxes.29_li']) ?>><?= ozz_tx($T['boxes.29_li']) ?></li>
    </ul>
  </template>

  <template id="tpl-brand-activation">
    <h4 data-title<?= ozz_alt($T['boxes.30_h4']) ?>><?= ozz_tx($T['boxes.30_h4']) ?></h4>
    <p data-desc<?= ozz_alt($T['boxes.31_p']) ?>><?= ozz_tx($T['boxes.31_p']) ?></p>
    <ul data-bullets>
      <li<?= ozz_alt($T['boxes.32_li']) ?>><?= ozz_tx($T['boxes.32_li']) ?></li>
      <li<?= ozz_alt($T['boxes.33_li']) ?>><?= ozz_tx($T['boxes.33_li']) ?></li>
      <li<?= ozz_alt($T['boxes.34_li']) ?>><?= ozz_tx($T['boxes.34_li']) ?></li>
    </ul>
  </template>

  <template id="tpl-creative-strategy">
    <h4 data-title<?= ozz_alt($T['boxes.35_h4']) ?>><?= ozz_tx($T['boxes.35_h4']) ?></h4>
    <p data-desc<?= ozz_alt($T['boxes.36_p']) ?>><?= ozz_tx($T['boxes.36_p']) ?></p>
    <ul data-bullets>
      <li<?= ozz_alt($T['boxes.37_li']) ?>><?= ozz_tx($T['boxes.37_li']) ?></li>
      <li<?= ozz_alt($T['boxes.38_li']) ?>><?= ozz_tx($T['boxes.38_li']) ?></li>
      <li<?= ozz_alt($T['boxes.39_li']) ?>><?= ozz_tx($T['boxes.39_li']) ?></li>
    </ul>
  </template>
</section>

<script src="<?php echo $base_url; ?>/pages/Studio/OpenBoxs.js"></script>
