<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   (versión ES + EN) Lylo Mousse.html
// Textos: pages/Work/Lylo Mousse/texts.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Work/Lylo Mousse/texts.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Work/Projects_Style.css">

<section class="video-section">
  <div class="video-item">
    <div class="video-background" data-video="lylo"></div>
  </div>
</section>

<section class="description-section">
  <div class="description-content">
    <h1>Lylo Mousse</h1>
    <p<?= ozz_alt($T['lylo.01_p']) ?>><?= ozz_tx($T['lylo.01_p']) ?></p>
  </div>
</section>

<div class="project-section">
  <button class="project-header">
    <span class="icon">+</span>
    <span class="project-title"<?= ozz_alt($T['lylo.02_project-title']) ?>><?= ozz_tx($T['lylo.02_project-title']) ?></span>
  </button>

  <!-- Sección de imágenes/videos -->
  <div class="media-grid"><div class="media-item process" data-position="5"> <div class="video-process" data-video-process="lylo_process"></div> </div></div>

  <!-- Panel flotante que cubre la grid -->
  <div class="project-info">
    <div class="info-content">
      <div class="info-box">
        <h2<?= ozz_alt($T['lylo.04_h2']) ?>><?= ozz_tx($T['lylo.04_h2']) ?></h2>
        <p<?= ozz_alt($T['lylo.05_p']) ?>><?= ozz_tx($T['lylo.05_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['lylo.06_h2']) ?>><?= ozz_tx($T['lylo.06_h2']) ?></h2>
        <p<?= ozz_alt($T['lylo.07_p']) ?>><?= ozz_tx($T['lylo.07_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['lylo.08_h2']) ?>><?= ozz_tx($T['lylo.08_h2']) ?></h2>
        <p<?= ozz_alt($T['lylo.09_p']) ?>><?= ozz_tx($T['lylo.09_p']) ?></p>
      </div>
      <div class="info-box">
        <h2<?= ozz_alt($T['lylo.10_h2']) ?>><?= ozz_tx($T['lylo.10_h2']) ?></h2>
        <p<?= ozz_alt_html($T['lylo.11_p_html']) ?>><?= ozz_th($T['lylo.11_p_html']) ?></p>
        <p<?= ozz_alt_html($T['lylo.12_p_html']) ?>><?= ozz_th($T['lylo.12_p_html']) ?></p>

        <p<?= ozz_alt_html($T['lylo.13_p_html']) ?>><?= ozz_th($T['lylo.13_p_html']) ?></p>
        <p<?= ozz_alt_html($T['lylo.14_p_html']) ?>><?= ozz_th($T['lylo.14_p_html']) ?></p>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $base_url; ?>/pages/Work/MoreInformation.js"></script>
<script src="<?= ozz_tx($T['lylo.15_script_src']) ?>"></script>
<script src="<?php echo $base_url; ?>/pages/Work/Lylo%20Mousse/LoadImages.js"></script>
<script src="<?php echo $base_url; ?>/pages/Work/LoadVideosProjectsProcess.js"></script>
