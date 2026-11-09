<?php
// =========================================================
// PROYECTOS (página Work) — grilla de tarjetas póster (ES / EN)
// ---------------------------------------------------------
// - Proyectos: data/projects.json (misma fuente que el inicio)
// - Textos de la cabecera: lang/ui.json → "work"
// - Videos: Scripts/ResponsiveMedia.js
//   (antes se cargaba Scripts/LoadVideos.js, que solo buscaba
//   tarjetas .panel del inicio: por eso aquí no aparecía
//   ningún video).
// =========================================================

require_once ROOT_PATH . '/config/helpers.php';
require_once ROOT_PATH . '/pages/index/ProjectCard.php';

$wk_ui = ozz_json('lang/ui.json')['work'] ?? [];
$wk_projects = ozz_projects();
$wk_total = count($wk_projects);
$wk_pad = static fn(int $n) => str_pad((string) $n, 2, '0', STR_PAD_LEFT);
$wk_h2 = $wk_ui['title'] ?? '';

$wk_arrow = '<svg viewBox="0 0 640 640" aria-hidden="true" focusable="false"><path d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z"/></svg>';
?>
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/index/StackCardsPoster.css')) ?>" />
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/Work/WorkGrid.css')) ?>" />

<section class="sc sc--grid wk" id="work" aria-labelledby="work-title">
  <header class="ozz-head wk-head">
    <div class="ozz-head__bar ozz-label">
      <span<?= ozz_alt($wk_ui['number'] ?? '') ?>><?= ozz_tx($wk_ui['number'] ?? '') ?></span>
      <span<?= ozz_alt($wk_ui['kicker'] ?? '') ?>><?= ozz_tx($wk_ui['kicker'] ?? '') ?></span>
      <span><?= $wk_pad($wk_total) ?> <span<?= ozz_alt($wk_ui['count'] ?? '') ?>><?= ozz_tx($wk_ui['count'] ?? '') ?></span></span>
    </div>

    <h1 class="ozz-head__title reveal-lines" id="work-title" style="--chars: <?= max(1, mb_strlen(ozz_t($wk_h2, ozz_lang()))) ?>"<?= ozz_alt($wk_h2) ?>><?= ozz_tx($wk_h2) ?></h1>

    <div class="ozz-head__aside">
      <p class="ozz-head__intro"<?= ozz_alt($wk_ui['intro'] ?? '') ?>><?= ozz_tx($wk_ui['intro'] ?? '') ?></p>

      <a class="ozz-link ozz-label" <?= ozz_href('Contact') ?>>
        <span<?= ozz_alt($wk_ui['cta'] ?? '') ?>><?= ozz_tx($wk_ui['cta'] ?? '') ?></span>
        <?= $wk_arrow ?>
      </a>
    </div>
  </header>

  <ul class="sc-grid">
    <?php
    $wk_i = 0;
    foreach ($wk_projects as $wk_id => $wk_p) {
        echo ozz_project_card($wk_id, $wk_p, ++$wk_i, $wk_total, '(min-width: 900px) 50vw, 100vw');
    }
    ?>
  </ul>
</section>

<?= ozz_projects_ld($wk_projects) ?>

<?php include ROOT_PATH . '/partials/Reveal/reveal.html'; ?>

<script defer src="<?= ozz_e(ozz_asset('Scripts/ResponsiveMedia.js')) ?>"></script>
<script defer src="<?= ozz_e(ozz_asset('pages/index/js/StackCards.js')) ?>"></script>
<script defer src="<?= ozz_e(ozz_asset('pages/index/js/ProjectTooltip.js')) ?>"></script>
