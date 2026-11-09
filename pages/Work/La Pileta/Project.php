<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   La Pileta-es.html + La Pileta.html
// Textos: pages/Work/La Pileta/texts.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Work/La Pileta/texts.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Work/Projects_Style.css">

<section class="video-section">
  <div class="video-item">
    <div class="video-background" data-video="pileta"></div>
  </div>
</section>

<section class="description-section">
  <div class="description-content">
    <h1>La Pileta</h1>
    <p<?= ozz_alt($T['pileta.01_p']) ?>><?= ozz_tx($T['pileta.01_p']) ?></p>
  </div>
</section>

<div class="project-section">
  <button class="project-header">
    <span class="icon">+</span>
    <span class="project-title"<?= ozz_alt($T['pileta.02_project-title']) ?>><?= ozz_tx($T['pileta.02_project-title']) ?></span>
  </button>

  <!-- Sección de imágenes/videos -->
  <div class="media-grid">
    <div class="media-item process" data-position="3">
      <div class="video-process" data-video-process="pileta_reels"></div>
    </div>
    <div class="media-item vertical" data-image="4" data-orientation="vertical"></div>
    <div class="media-item vertical" data-image="5" data-orientation="vertical"></div>
    <div class="media-item vertical" data-image="6" data-orientation="vertical"></div>
  </div>

  <!-- Panel flotante que cubre la grid -->
  <div class="project-info">
    <div class="info-content">
      <div class="info-box">
        <h2<?= ozz_alt($T['pileta.03_h2']) ?>><?= ozz_tx($T['pileta.03_h2']) ?></h2>
        <p<?= ozz_alt($T['pileta.04_p']) ?>><?= ozz_tx($T['pileta.04_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['pileta.05_h2']) ?>><?= ozz_tx($T['pileta.05_h2']) ?></h2>
        <p<?= ozz_alt($T['pileta.06_p']) ?>><?= ozz_tx($T['pileta.06_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['pileta.07_h2']) ?>><?= ozz_tx($T['pileta.07_h2']) ?></h2>
        <p<?= ozz_alt($T['pileta.08_p']) ?>><?= ozz_tx($T['pileta.08_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['pileta.09_h2']) ?>><?= ozz_tx($T['pileta.09_h2']) ?></h2>
        <p<?= ozz_alt_html($T['pileta.10_p_html']) ?>><?= ozz_th($T['pileta.10_p_html']) ?></p>
        <p<?= ozz_alt_html($T['pileta.11_p_html']) ?>><?= ozz_th($T['pileta.11_p_html']) ?></p>
        <p<?= ozz_alt_html($T['pileta.12_p_html']) ?>><?= ozz_th($T['pileta.12_p_html']) ?></p>
        <p<?= ozz_alt_html($T['pileta.13_p_html']) ?>><?= ozz_th($T['pileta.13_p_html']) ?></p>
        <p<?= ozz_alt_html($T['pileta.14_p_html']) ?>><?= ozz_th($T['pileta.14_p_html']) ?></p>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $base_url; ?>/pages/Work/MoreInformation.js"></script>
<script src="<?= ozz_tx($T['pileta.15_script_src']) ?>"></script>
<script src="<?php echo $base_url; ?>/pages/Work/La%20Pileta/LoadImages.js"></script>
<script src="<?php echo $base_url; ?>/pages/Work/LoadVideosProjectsProcess.js"></script>
<script src="<?php echo $base_url; ?>/pages/Work/La%20Pileta/LoadImagesVertical.js"></script>
