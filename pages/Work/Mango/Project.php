<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   Mango-es.html + Mango.html
// Textos: pages/Work/Mango/texts.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Work/Mango/texts.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Work/Projects_Style.css">

<section class="video-section">
  <div class="video-item">
    <div class="video-background" data-video="mango"></div>
  </div>
</section>

<section class="description-section">
  <div class="description-content">
    <h1>Mango</h1>
    <p<?= ozz_alt($T['mango.01_p']) ?>><?= ozz_tx($T['mango.01_p']) ?></p>
  </div>
</section>

<div class="project-section">
  <button class="project-header">
    <span class="icon">+</span>
    <span class="project-title"<?= ozz_alt($T['mango.02_project-title']) ?>><?= ozz_tx($T['mango.02_project-title']) ?></span>
  </button>

  <!-- Sección de imágenes/videos -->
  <div class="media-grid"></div>

  <!-- Panel flotante que cubre la grid -->
  <div class="project-info">
    <div class="info-content">
      <div class="info-box">
        <h2<?= ozz_alt($T['mango.03_h2']) ?>><?= ozz_tx($T['mango.03_h2']) ?></h2>
        <p<?= ozz_alt($T['mango.04_p']) ?>><?= ozz_tx($T['mango.04_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['mango.05_h2']) ?>><?= ozz_tx($T['mango.05_h2']) ?></h2>
        <p<?= ozz_alt($T['mango.06_p']) ?>><?= ozz_tx($T['mango.06_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['mango.07_h2']) ?>><?= ozz_tx($T['mango.07_h2']) ?></h2>
        <p<?= ozz_alt($T['mango.08_p']) ?>><?= ozz_tx($T['mango.08_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['mango.09_h2']) ?>><?= ozz_tx($T['mango.09_h2']) ?></h2>
        <p<?= ozz_alt_html($T['mango.10_p_html']) ?>><?= ozz_th($T['mango.10_p_html']) ?></p>
        <p<?= ozz_alt_html($T['mango.11_p_html']) ?>><?= ozz_th($T['mango.11_p_html']) ?></p>
        <p<?= ozz_alt_html($T['mango.12_p_html']) ?>><?= ozz_th($T['mango.12_p_html']) ?></p>
        <p<?= ozz_alt_html($T['mango.13_p_html']) ?>><?= ozz_th($T['mango.13_p_html']) ?></p>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $base_url; ?>/pages/Work/MoreInformation.js"></script>
<script src="<?= ozz_tx($T['mango.14_script_src']) ?>"></script>
<script src="<?php echo $base_url; ?>/pages/Work/Mango/LoadImages.js"></script>
