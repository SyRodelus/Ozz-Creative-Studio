<?php
// =========================================================
// SERVICIOS — layout bento (ES / EN)
// ---------------------------------------------------------
// Organización: columna izquierda con etiqueta + retrato
// (con "píxeles" naranjas animados) y columna derecha con
// titular, introducción y tarjetas (2 arriba + 1 ancha).
// Contenido: pages/index/texts/services.json (también
// alimenta el submenú "Servicios" del menú principal).
//  - _section.headline admite <em>…</em> (itálica serif).
//  - _section.photo: retrato de la columna izquierda.
//  - items[].image: imagen que aparece al pasar el cursor.
// =========================================================

require_once ROOT_PATH . '/config/helpers.php';

$sv_data = ozz_json('pages/index/texts/services.json');
$sv_ui = $sv_data['_section'] ?? [];
$sv_items = $sv_data['items'] ?? [];
$sv_total = count($sv_items);
$sv_pad = static fn(int $n) => str_pad((string) $n, 2, '0', STR_PAD_LEFT);

$sv_photo_alt = $sv_ui['photo']['alt'] ?? '';
$sv_photo = ozz_img($sv_ui['photo'] ?? [], ozz_lang(), [
    'class' => 'svc-photo__img',
    'sizes' => '(min-width: 900px) 25vw, 70vw',
]);
if ($sv_photo !== '') {
    $sv_photo = substr($sv_photo, 0, -1) . ozz_alt($sv_photo_alt, 'alt') . '>';
}

$sv_arrow = '<svg viewBox="0 0 640 640" aria-hidden="true" focusable="false"><path d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z"/></svg>';

$sv_flame = ozz_flame_svg();
?>
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/index/Poster.css')) ?>" />
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/index/Services.css')) ?>" />

<section class="svc" id="services" aria-labelledby="services-title">
  <div class="svc-bar ozz-head__bar ozz-label">
    <span><?= ozz_tx($sv_ui['number'] ?? '') ?> <span<?= ozz_alt($sv_ui['label'] ?? '') ?>><?= ozz_tx($sv_ui['label'] ?? '') ?></span></span>
    <span<?= ozz_alt($sv_ui['kicker'] ?? '') ?>><?= ozz_tx($sv_ui['kicker'] ?? '') ?></span>
    <span><?= $sv_pad($sv_total) ?> <span<?= ozz_alt($sv_ui['count'] ?? '') ?>><?= ozz_tx($sv_ui['count'] ?? '') ?></span></span>
  </div>

  <div class="svc-layout">
    <!-- Cabecera con el mismo estilo que Proyectos / Especialidades:
         titular grande a la izquierda, texto + enlace a la derecha -->
    <?php $sv_h2 = $sv_ui['title'] ?? ''; ?>
    <h2 class="ozz-head__title svc-headline reveal-lines" id="services-title" style="--chars: <?= max(1, mb_strlen(ozz_t($sv_h2, ozz_lang()))) ?>"<?= ozz_alt($sv_h2) ?>><?= ozz_tx($sv_h2) ?></h2>

    <div class="svc-intro">
      <p class="svc-lead"<?= ozz_alt($sv_ui['intro'] ?? '') ?>><?= ozz_tx($sv_ui['intro'] ?? '') ?></p>

      <a class="ozz-link ozz-label" <?= ozz_href($sv_ui['ctaHref'] ?? 'Contact') ?>>
        <span<?= ozz_alt($sv_ui['cta'] ?? '') ?>><?= ozz_tx($sv_ui['cta'] ?? '') ?></span>
        <?= $sv_arrow ?>
      </a>
    </div>

    <!-- Tarjetas -->
    <ul class="svc-bento">
      <?php foreach ($sv_items as $sv_i => $sv_item):
          $sv_title = $sv_item['title'] ?? '';
          $sv_tags = array_slice(ozz_list_pairs($sv_item['tags'] ?? []), 0, 3);
          $sv_img = ozz_img($sv_item['image'] ?? [], ozz_lang(), [
              'sizes' => '(min-width: 900px) 40vw, 100vw',
              'alt'   => '',
          ]);
      ?>
      <li class="svc-card<?= $sv_i === $sv_total - 1 && $sv_total % 2 === 1 ? ' svc-card--wide' : '' ?>" style="--i: <?= $sv_i ?>">
        <a class="svc-card__link" <?= ozz_href($sv_item['href'] ?? ($sv_item['slug'] ?? 'Services')) ?>>
          <?php if ($sv_img): ?>
          <div class="svc-card__media" aria-hidden="true"><?= $sv_img ?></div>
          <?php endif; ?>

          <div class="svc-card__top">
            <h3 class="svc-card__title"<?= ozz_alt($sv_title) ?>><?= ozz_tx($sv_title) ?></h3>
            <span class="svc-card__num ozz-label"><?= $sv_pad($sv_i + 1) ?></span>
          </div>

          <p class="svc-card__desc"<?= ozz_alt($sv_item['description'] ?? '') ?>><?= ozz_tx($sv_item['description'] ?? '') ?></p>

          <div class="svc-card__bottom">
            <?php if ($sv_tags): ?>
            <ul class="svc-card__tags">
              <?php foreach ($sv_tags as $sv_tag): ?>
              <li class="ozz-label"<?= ozz_alt($sv_tag) ?>><?= ozz_tx($sv_tag) ?></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <span class="svc-card__btn" aria-hidden="true"><?= $sv_arrow ?></span>
          </div>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<script defer src="<?= ozz_e(ozz_asset('pages/index/js/Services.js')) ?>"></script>
