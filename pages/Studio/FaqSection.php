<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   FaqSection-es.html + FaqSection.html
// Textos: pages/Studio/texts/faq.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Studio/texts/faq.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Studio/FaqSection.css">
<section class="section-wrapper">
  <h2 class="section-heading"<?= ozz_alt($T['faq.01_section-heading']) ?>><?= ozz_tx($T['faq.01_section-heading']) ?></h2>

  <div class="faq-list">
    <details class="faq-item">
      <summary class="faq-toggle">
        <span class="faq-title"<?= ozz_alt($T['faq.02_faq-title']) ?>><?= ozz_tx($T['faq.02_faq-title']) ?></span>
        <span class="faq-cross">+</span>
      </summary>
      <div class="faq-body">
        <p<?= ozz_alt($T['faq.03_p']) ?>><?= ozz_tx($T['faq.03_p']) ?></p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-toggle">
        <span class="faq-title"<?= ozz_alt($T['faq.04_faq-title']) ?>><?= ozz_tx($T['faq.04_faq-title']) ?></span>
        <span class="faq-cross">+</span>
      </summary>
      <div class="faq-body">
        <p<?= ozz_alt($T['faq.05_p']) ?>><?= ozz_tx($T['faq.05_p']) ?></p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-toggle">
        <span class="faq-title"<?= ozz_alt($T['faq.06_faq-title']) ?>><?= ozz_tx($T['faq.06_faq-title']) ?></span>
        <span class="faq-cross">+</span>
      </summary>
      <div class="faq-body">
        <p<?= ozz_alt($T['faq.07_p']) ?>><?= ozz_tx($T['faq.07_p']) ?></p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-toggle">
        <span class="faq-title"<?= ozz_alt($T['faq.08_faq-title']) ?>><?= ozz_tx($T['faq.08_faq-title']) ?></span>
        <span class="faq-cross">+</span>
      </summary>
      <div class="faq-body">
        <p<?= ozz_alt($T['faq.09_p']) ?>><?= ozz_tx($T['faq.09_p']) ?></p>
      </div>
    </details>

    <details class="faq-item">
      <summary class="faq-toggle">
        <span class="faq-title"<?= ozz_alt($T['faq.10_faq-title']) ?>><?= ozz_tx($T['faq.10_faq-title']) ?></span>
        <span class="faq-cross">+</span>
      </summary>
      <div class="faq-body">
        <p<?= ozz_alt($T['faq.11_p']) ?>><?= ozz_tx($T['faq.11_p']) ?></p>
      </div>
    </details>
  </div>
</section>
