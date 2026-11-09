<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   StaffSection-es.html + StaffSection.html
// Textos: pages/Studio/texts/staff.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Studio/texts/staff.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Studio/StaffSection.css">
<!-- StaffSection.html -->
<section class="staff-wrap" aria-labelledby="staff-title">
  <h2 id="staff-title" class="staff-title"<?= ozz_alt_html($T['staff.01_staff-title_html']) ?>><?= ozz_th($T['staff.01_staff-title_html']) ?></h2>

  <div class="staff-grid">
    <div class="staff-card">
      <!-- IMAGEN: SIEMPRE ARRIBA (fuera del summary) -->
      <figure class="staff-media">
        <img class="staff-img" src="<?php echo $base_url; ?>/pages/Studio/Mateo.webp" alt="Mateo Gonzalez">
      </figure>

      <!-- BLOQUE FIJO PARA ESCRITORIO -->
      <div class="desk-block">
        <h3 class="staff-name">Mateo Gonzalez</h3>
        <p class="staff-role"<?= ozz_alt_html($T['staff.02_staff-role_html']) ?>><?= ozz_th($T['staff.02_staff-role_html']) ?></p>
        <p class="staff-text"<?= ozz_alt($T['staff.03_staff-text']) ?>><?= ozz_tx($T['staff.03_staff-text']) ?></p>
      </div>

      <!-- ACORDEÓN SIN JS (DETAILS/SUMMARY) SOLO EN MÓVIL -->
      <details class="staff-acc">
        <summary class="staff-summary">
          <div class="staff-meta">
            <h3 class="staff-name">Mateo Gonzalez</h3>
            <p class="staff-role"<?= ozz_alt_html($T['staff.04_staff-role_html']) ?>><?= ozz_th($T['staff.04_staff-role_html']) ?></p>
          </div>
          <span class="staff-cross">+</span>
        </summary>
        <div class="mob-body">
          <p<?= ozz_alt($T['staff.05_p']) ?>><?= ozz_tx($T['staff.05_p']) ?></p>
        </div>
      </details>
    </div>

    <div class="staff-card">
      <figure class="staff-media">
        <img class="staff-img" src="<?php echo $base_url; ?>/pages/Studio/Steven.webp" alt="Steven Rodelo">
      </figure>

      <div class="desk-block">
        <h3 class="staff-name">Steven Rodelo</h3>
        <p class="staff-role"<?= ozz_alt_html($T['staff.06_staff-role_html']) ?>><?= ozz_th($T['staff.06_staff-role_html']) ?></p>
        <p class="staff-text"<?= ozz_alt($T['staff.07_staff-text']) ?>><?= ozz_tx($T['staff.07_staff-text']) ?></p>
      </div>

      <details class="staff-acc">
        <summary class="staff-summary">
          <div class="staff-meta">
            <h3 class="staff-name">Steven Rodelo</h3>
            <p class="staff-role"<?= ozz_alt_html($T['staff.08_staff-role_html']) ?>><?= ozz_th($T['staff.08_staff-role_html']) ?></p>
          </div>
          <span class="staff-cross">+</span>
        </summary>
        <div class="mob-body">
          <p<?= ozz_alt($T['staff.09_p']) ?>><?= ozz_tx($T['staff.09_p']) ?></p>
        </div>
      </details>
    </div>
  </div>
</section>
