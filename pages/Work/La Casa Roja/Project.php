<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   La Casa Roja-es.html + La Casa Roja.html
// Textos: pages/Work/La Casa Roja/texts.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Work/La Casa Roja/texts.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Work/Projects_Style.css">

<section class="video-section">
  <div class="video-item">
    <div class="video-background" data-video="casa"></div>
  </div>
</section>

<section class="description-section">
  <div class="description-content">
    <h1>La Casa Roja</h1>
    <p<?= ozz_alt($T['casa.01_p']) ?>><?= ozz_tx($T['casa.01_p']) ?></p>
  </div>
</section>

<div class="project-section">
  <button class="project-header">
    <span class="icon">+</span>
    <span class="project-title"<?= ozz_alt($T['casa.02_project-title']) ?>><?= ozz_tx($T['casa.02_project-title']) ?></span>
  </button>

  <!-- Sección de imágenes/videos -->
  <div class="media-grid">
    <div class="media-item process" data-position="4">
      <div class="video-process" data-video-process="casa_reels"></div>
    </div>
    <div class="media-item vertical" data-image="6" data-orientation="vertical"></div>
    <div class="media-item vertical" data-image="8" data-orientation="vertical"></div>
  </div>

  <!-- Panel flotante que cubre la grid -->
  <div class="project-info">
    <div class="info-content">
      <div class="info-box">
        <h2<?= ozz_alt($T['casa.03_h2']) ?>><?= ozz_tx($T['casa.03_h2']) ?></h2>
        <p<?= ozz_alt($T['casa.04_p']) ?>><?= ozz_tx($T['casa.04_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['casa.05_h2']) ?>><?= ozz_tx($T['casa.05_h2']) ?></h2>
        <p<?= ozz_alt($T['casa.06_p']) ?>><?= ozz_tx($T['casa.06_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['casa.07_h2']) ?>><?= ozz_tx($T['casa.07_h2']) ?></h2>
        <p<?= ozz_alt($T['casa.08_p']) ?>><?= ozz_tx($T['casa.08_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['casa.09_h2']) ?>><?= ozz_tx($T['casa.09_h2']) ?></h2>
        <p<?= ozz_alt_html($T['casa.10_p_html']) ?>><?= ozz_th($T['casa.10_p_html']) ?></p>
        <p<?= ozz_alt_html($T['casa.11_p_html']) ?>><?= ozz_th($T['casa.11_p_html']) ?></p>
        <p<?= ozz_alt_html($T['casa.12_p_html']) ?>><?= ozz_th($T['casa.12_p_html']) ?></p>
        <p<?= ozz_alt_html($T['casa.13_p_html']) ?>><?= ozz_th($T['casa.13_p_html']) ?></p>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $base_url; ?>/pages/Work/MoreInformation.js"></script>
<script src="<?= ozz_tx($T['casa.14_script_src']) ?>"></script>
<script src="<?php echo $base_url; ?>/pages/Work/La%20Casa%20Roja/LoadImages.js"></script>
<script src="<?php echo $base_url; ?>/pages/Work/LoadVideosProjectsProcess.js"></script>
<script src="<?php echo $base_url; ?>/pages/Work/La%20Casa%20Roja/LoadImagesVertical.js"></script>
