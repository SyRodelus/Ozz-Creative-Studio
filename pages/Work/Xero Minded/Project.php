<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   (versión ES + EN) Xero Minded.html
// Textos: pages/Work/Xero Minded/texts.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Work/Xero Minded/texts.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Work/Projects_Style.css">

<section class="video-section">
  <div class="video-item">
    <div class="video-background" data-video="xero"></div>
  </div>
</section>

<section class="description-section">
  <div class="description-content">
    <h1>Xero Minded</h1>
    <p<?= ozz_alt($T['xero.01_p']) ?>><?= ozz_tx($T['xero.01_p']) ?></p>
  </div>
</section>

<div class="project-section">
  <button class="project-header">
    <span class="icon">+</span>
    <span class="project-title"<?= ozz_alt($T['xero.02_project-title']) ?>><?= ozz_tx($T['xero.02_project-title']) ?></span>
  </button>

  <!-- Sección de imágenes/videos -->
  <div class="media-grid"><div class="media-item process" data-position="5"> <div class="video-process" data-video-process="xero_process"></div> </div></div>

  <!-- Panel flotante que cubre la grid -->
  <div class="project-info">
    <div class="info-content">
      <div class="info-box">
        <h2<?= ozz_alt($T['xero.04_h2']) ?>><?= ozz_tx($T['xero.04_h2']) ?></h2>
        <p<?= ozz_alt($T['xero.05_p']) ?>><?= ozz_tx($T['xero.05_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['xero.06_h2']) ?>><?= ozz_tx($T['xero.06_h2']) ?></h2>
        <p<?= ozz_alt($T['xero.07_p']) ?>><?= ozz_tx($T['xero.07_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['xero.08_h2']) ?>><?= ozz_tx($T['xero.08_h2']) ?></h2>
        <p<?= ozz_alt($T['xero.09_p']) ?>><?= ozz_tx($T['xero.09_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['xero.10_h2']) ?>><?= ozz_tx($T['xero.10_h2']) ?></h2>
        <p<?= ozz_alt_html($T['xero.11_p_html']) ?>><?= ozz_th($T['xero.11_p_html']) ?></p>
        <p<?= ozz_alt_html($T['xero.12_p_html']) ?>><?= ozz_th($T['xero.12_p_html']) ?></p>
        <p<?= ozz_alt_html($T['xero.13_p_html']) ?>><?= ozz_th($T['xero.13_p_html']) ?></p>
        <p<?= ozz_alt_html($T['xero.14_p_html']) ?>><?= ozz_th($T['xero.14_p_html']) ?></p>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $base_url; ?>/pages/Work/MoreInformation.js"></script>
<script src="<?= ozz_tx($T['xero.15_script_src']) ?>"></script>
<script src="<?php echo $base_url; ?>/pages/Work/Xero%20Minded/LoadImages.js"></script>
<script src="<?php echo $base_url; ?>/pages/Work/LoadVideosProjectsProcess.js"></script>
