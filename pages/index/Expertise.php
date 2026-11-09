<?php
// =========================================================
// ESPECIALIDADES — pósters tipográficos (ES / EN)
// ---------------------------------------------------------
// Contenido: pages/index/texts/expertise.json (también
// alimenta el submenú "Especialidades").
//  - "image": opcional. Si se define {"sources": [...], "alt"}
//    la tarjeta usa esa foto de fondo. Con null queda la
//    versión tipográfica.
// =========================================================

require_once ROOT_PATH . '/config/helpers.php';

$ex_data = ozz_json('pages/index/texts/expertise.json');
$ex_ui = $ex_data['_section'] ?? [];
$ex_items = $ex_data['items'] ?? [];
$ex_total = count($ex_items);
$ex_pad = static fn(int $n) => str_pad((string) $n, 2, '0', STR_PAD_LEFT);
$ex_h2 = $ex_ui['title'] ?? '';

$ex_arrow = '<svg viewBox="0 0 640 640" aria-hidden="true" focusable="false"><path d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z"/></svg>';
?>
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/index/Poster.css')) ?>" />
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/index/Expertise.css')) ?>" />

<section class="exp" id="expertise" aria-labelledby="expertise-title">
  <header class="ozz-head">
    <div class="ozz-head__bar ozz-label">
      <span><?= ozz_tx($ex_ui['number'] ?? '') ?> <span<?= ozz_alt($ex_ui['label'] ?? '') ?>><?= ozz_tx($ex_ui['label'] ?? '') ?></span></span>
      <span<?= ozz_alt($ex_ui['kicker'] ?? '') ?>><?= ozz_tx($ex_ui['kicker'] ?? '') ?></span>
      <span><?= $ex_pad($ex_total) ?> <span<?= ozz_alt($ex_ui['count'] ?? '') ?>><?= ozz_tx($ex_ui['count'] ?? '') ?></span></span>
    </div>

    <h2 class="ozz-head__title reveal-lines" id="expertise-title" style="--chars: <?= max(1, mb_strlen(ozz_t($ex_h2, ozz_lang()))) ?>"<?= ozz_alt($ex_h2) ?>><?= ozz_tx($ex_h2) ?></h2>

    <div class="ozz-head__aside">
      <p class="ozz-head__intro"<?= ozz_alt($ex_ui['intro'] ?? '') ?>><?= ozz_tx($ex_ui['intro'] ?? '') ?></p>
    </div>
  </header>

  <div class="exp-grid">
    <?php foreach ($ex_items as $ex_i => $ex_item):
        $ex_n = $ex_pad($ex_i + 1);
        $ex_title = $ex_item['title'] ?? '';
        $ex_list = ozz_list_pairs($ex_item['list'] ?? []);
        $ex_img = !empty($ex_item['image'])
            ? ozz_img($ex_item['image'], ozz_lang(), ['sizes' => '(min-width: 760px) 33vw, 100vw'])
            : '';
    ?>
    <article class="exp-card<?= $ex_img ? ' has-image' : '' ?>" style="--i: <?= $ex_i ?>">
      <div class="reveal-mask">
        <div class="reveal-block exp-inner">
          <a class="exp-link" <?= ozz_href($ex_item['href'] ?? ($ex_item['slug'] ?? 'Expertise')) ?>>
            <?php if ($ex_img): ?>
            <div class="exp-bg"><?= $ex_img ?></div>
            <?php endif; ?>

            <span class="exp-index" aria-hidden="true"><?= $ex_n ?></span>

            <div class="exp-bar ozz-label" aria-hidden="true">
              <span<?= ozz_alt($ex_ui['studio'] ?? '') ?>><?= ozz_tx($ex_ui['studio'] ?? 'Ozz') ?></span>
              <span<?= ozz_alt($ex_ui['itemLabel'] ?? '') ?>><?= ozz_tx($ex_ui['itemLabel'] ?? '') ?></span>
              <span><?= $ex_n ?> / <?= $ex_pad($ex_total) ?></span>
            </div>

            <?php if ($ex_list): ?>
            <ul class="exp-list">
              <?php foreach ($ex_list as $ex_li): ?>
              <li class="ozz-label"<?= ozz_alt($ex_li) ?>><?= ozz_tx($ex_li) ?></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <div class="exp-foot">
              <p class="exp-desc"<?= ozz_alt($ex_item['description'] ?? '') ?>><?= ozz_tx($ex_item['description'] ?? '') ?></p>

              <div class="exp-bottom">
                <h3 class="exp-name"<?= ozz_alt($ex_title) ?>><?= ozz_tx($ex_title) ?></h3>
                <span class="exp-more ozz-label">
                  <span<?= ozz_alt($ex_ui['more'] ?? '') ?>><?= ozz_tx($ex_ui['more'] ?? '') ?></span>
                  <?= $ex_arrow ?>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
</section>
