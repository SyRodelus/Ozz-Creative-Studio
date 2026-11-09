<?php
// =========================================================
// PROYECTOS (inicio) — Stack Cards estilo póster (ES / EN)
// ---------------------------------------------------------
// - Textos de la sección: pages/index/texts/stackcards.json
// - Proyectos: data/projects.json ("featured" elige cuáles
//   y en qué orden se muestran aquí).
// - Videos: Scripts/ResponsiveMedia.js elige la versión según
//   el tamaño real de la tarjeta.
// =========================================================

require_once ROOT_PATH . '/config/helpers.php';
require_once ROOT_PATH . '/pages/index/ProjectCard.php';

$sc_data = ozz_json('pages/index/texts/stackcards.json');
$sc_ui = $sc_data['_section'] ?? [];
$sc_all = ozz_projects();

$sc_projects = [];
foreach ($sc_data['featured'] ?? array_keys($sc_all) as $sc_id) {
    if (isset($sc_all[$sc_id])) {
        $sc_projects[$sc_id] = $sc_all[$sc_id];
    }
}

$sc_total = count($sc_projects);
$sc_pad = static fn(int $n) => str_pad((string) $n, 2, '0', STR_PAD_LEFT);
$sc_h2 = $sc_ui['title'] ?? '';

$sc_arrow = '<svg viewBox="0 0 640 640" aria-hidden="true" focusable="false"><path d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z"/></svg>';
?>
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/index/Poster.css')) ?>" />
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/index/StackCardsPoster.css')) ?>" />

<section class="stackCards sc" id="stackcards" data-stack aria-labelledby="stackcards-title">
  <header class="ozz-head">
    <div class="ozz-head__bar ozz-label">
      <span><?= ozz_tx($sc_ui['number'] ?? '') ?> <span<?= ozz_alt($sc_ui['label'] ?? '') ?>><?= ozz_tx($sc_ui['label'] ?? '') ?></span></span>
      <span<?= ozz_alt($sc_ui['kicker'] ?? '') ?>><?= ozz_tx($sc_ui['kicker'] ?? '') ?></span>
      <span><?= $sc_pad($sc_total) ?> <span<?= ozz_alt($sc_ui['cases'] ?? '') ?>><?= ozz_tx($sc_ui['cases'] ?? '') ?></span></span>
    </div>

    <h2 class="ozz-head__title reveal-lines" id="stackcards-title" style="--chars: <?= max(1, mb_strlen(ozz_t($sc_h2, ozz_lang()))) ?>"<?= ozz_alt($sc_h2) ?>><?= ozz_tx($sc_h2) ?></h2>

    <div class="ozz-head__aside">
      <p class="ozz-head__intro"<?= ozz_alt($sc_ui['intro'] ?? '') ?>><?= ozz_tx($sc_ui['intro'] ?? '') ?></p>

      <a class="ozz-link ozz-label" <?= ozz_href($sc_ui['ctaHref'] ?? 'Work') ?>>
        <span<?= ozz_alt($sc_ui['cta'] ?? '') ?>><?= ozz_tx($sc_ui['cta'] ?? '') ?></span>
        <?= $sc_arrow ?>
      </a>
    </div>
  </header>

  <ul id="panels" class="sc-grid">
    <?php
    $sc_i = 0;
    foreach ($sc_projects as $sc_id => $sc_p) {
        echo ozz_project_card($sc_id, $sc_p, ++$sc_i, $sc_total, '(min-width: 900px) 50vw, 100vw');
    }
    ?>
  </ul>
</section>

<?= ozz_projects_ld($sc_projects) ?>

<script defer src="<?= ozz_e(ozz_asset('Scripts/ResponsiveMedia.js')) ?>"></script>
<script defer src="<?= ozz_e(ozz_asset('pages/index/js/StackCards.js')) ?>"></script>
<script defer src="<?= ozz_e(ozz_asset('pages/index/js/ProjectTooltip.js')) ?>"></script>
