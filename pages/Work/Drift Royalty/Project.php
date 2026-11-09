<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   Drift Royalty-es.html + Drift Royalty.html
// Textos: pages/Work/Drift Royalty/texts.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Work/Drift Royalty/texts.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Work/Projects_Style.css">

<section class="video-section">
  <div class="video-item">
    <div class="video-background" data-video="drift"></div>
  </div>
</section>

<section class="description-section">
  <div class="description-content">
    <h1>Drift Royalty</h1>
    <p<?= ozz_alt($T['drift.01_p']) ?>><?= ozz_tx($T['drift.01_p']) ?></p>
  </div>
</section>

<div class="project-section">
  <button class="project-header">
    <span class="icon">+</span>
    <span class="project-title"<?= ozz_alt($T['drift.02_project-title']) ?>><?= ozz_tx($T['drift.02_project-title']) ?></span>
  </button>

  <!-- Sección de imágenes/videos -->
  <div class="media-grid">
    <div class="media-item process" data-position="5">
      <div class="video-process" data-video-process="drift_process"></div>
    </div>
  </div>

  <!-- Panel flotante que cubre la grid -->
  <div class="project-info">
    <div class="info-content">
      <div class="info-box">
        <h2<?= ozz_alt($T['drift.03_h2']) ?>><?= ozz_tx($T['drift.03_h2']) ?></h2>
        <p<?= ozz_alt($T['drift.04_p']) ?>><?= ozz_tx($T['drift.04_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['drift.05_h2']) ?>><?= ozz_tx($T['drift.05_h2']) ?></h2>
        <p<?= ozz_alt($T['drift.06_p']) ?>><?= ozz_tx($T['drift.06_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['drift.07_h2']) ?>><?= ozz_tx($T['drift.07_h2']) ?></h2>
        <p<?= ozz_alt($T['drift.08_p']) ?>><?= ozz_tx($T['drift.08_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['drift.09_h2']) ?>><?= ozz_tx($T['drift.09_h2']) ?></h2>
        <p<?= ozz_alt_html($T['drift.10_p_html']) ?>><?= ozz_th($T['drift.10_p_html']) ?></p>
        <p<?= ozz_alt_html($T['drift.11_p_html']) ?>><?= ozz_th($T['drift.11_p_html']) ?></p>
        <p<?= ozz_alt_html($T['drift.12_p_html']) ?>><?= ozz_th($T['drift.12_p_html']) ?></p>
        <p<?= ozz_alt_html($T['drift.13_p_html']) ?>><?= ozz_th($T['drift.13_p_html']) ?></p>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $base_url; ?>/pages/Work/MoreInformation.js"></script>
<script src="<?= ozz_tx($T['drift.14_script_src']) ?>"></script>
<script src="<?php echo $base_url; ?>/pages/Work/Drift%20Royalty/LoadImages.js"></script>
<script src="<?php echo $base_url; ?>/pages/Work/LoadVideosProjectsProcess.js"></script>
