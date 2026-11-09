<?php
// =========================================================
// FOOTER (ES / EN) — un solo archivo para ambos idiomas
// ---------------------------------------------------------
// - Footer-es.php solo incluye este archivo (compatibilidad
//   con las páginas que aún lo referencian).
// - Textos: lang/ui.json → "footer"; listas de servicios y
//   especialidades: pages/index/texts/*.json (misma fuente que
//   el inicio y el menú).
// - Animación OZZ: Features/Footer/FooterOzz.js (sin GSAP).
// =========================================================

require_once ROOT_PATH . '/config/helpers.php';

// Token CSRF (lo usa el formulario de contacto)
if (session_status() === PHP_SESSION_ACTIVE && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Compatibilidad: algunas páginas antiguas usan e()
if (!function_exists('e')) {
    function e($string)
    {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$ft = ozz_json('lang/ui.json')['footer'] ?? [];
$ft_studio = ozz_json('lang/ui.json')['common']['studio'] ?? 'Ozz';
$ft_services = ozz_json('pages/index/texts/services.json')['items'] ?? [];
$ft_expertise = ozz_json('pages/index/texts/expertise.json')['items'] ?? [];

$ft_email = 'team@ozzcreativestudio.com';
$ft_phone = '+57 315 536 3130';
$ft_whatsapp = 'https://wa.me/573155363130';

$ft_social = [
    ['label' => 'Instagram', 'href' => 'https://www.instagram.com/ozzcreativestudio?igsh=ZjlvMHVjbDM4ODRs'],
    ['label' => 'Behance',   'href' => 'https://www.behance.net/mateogonzalez5'],
    ['label' => 'LinkedIn',  'href' => '#'],
];

$ft_nav = [
    ['label' => $ft['home'] ?? 'Inicio',            'slug' => 'index'],
    ['label' => $ft['work'] ?? 'Proyectos',         'slug' => 'Work'],
    ['label' => $ft['studioLink'] ?? 'Estudio',     'slug' => 'Studio'],
    ['label' => $ft['contact'] ?? 'Contacto',       'slug' => 'Contact'],
];

$ft_arrow = '<svg viewBox="0 0 640 640" aria-hidden="true" focusable="false"><path d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z"/></svg>';

$ft_flame = 'M 366.419 6.863 C 363.714 7.782, 357.450 11.170, 352.500 14.391 C 308.220 43.207, 278.912 91.742, 256.421 173.500 C 254 182.300, 251.276 191.177, 250.368 193.228 C 245.865 203.393, 238.549 196.129, 234.024 177 C 231.089 164.596, 229.278 159, 226.739 154.500 C 224.142 149.897, 218.842 146.864, 214.806 147.672 C 205.669 149.502, 195.692 167.215, 191.575 188.916 C 190.779 193.116, 189.166 205.766, 187.991 217.026 C 183.257 262.418, 175.259 298.429, 169.414 300.672 C 166.593 301.754, 164.714 299.714, 159.500 289.906 C 152.888 277.467, 151.430 275.429, 147.336 272.899 C 144.572 271.190, 142.784 270.853, 138.408 271.216 C 133.859 271.593, 132.341 272.246, 129.409 275.085 C 124.592 279.750, 120.404 288.833, 118.407 298.950 C 116.408 309.068, 116.924 336.164, 119.379 350 C 132.655 424.850, 144.086 490.125, 145.004 496.338 C 146.558 506.844, 145.573 513.580, 141.956 517.198 C 139.357 519.797, 138.578 520, 131.212 520 C 121.788 520, 118.676 521.775, 116.966 528.125 C 114.352 537.835, 123.363 573.825, 134.964 600 C 151.352 636.977, 181.884 677.864, 216.050 708.586 C 274.372 761.029, 315.805 777.140, 398.778 779.635 C 479.766 782.071, 546.508 763.495, 590.341 726.317 C 618.802 702.177, 639.188 665.988, 647.415 625 C 651.485 604.721, 652.393 594.782, 652.307 571.500 C 652.172 535.484, 648.866 515.955, 632.500 454.500 C 618.729 402.794, 615.341 381.942, 616.288 354.738 C 616.679 343.490, 617.391 337.405, 619.005 331.500 C 621.739 321.497, 621.392 314.110, 618.020 310.521 C 609.955 301.937, 595.415 307.065, 582.929 322.898 C 571.965 336.802, 570.896 338, 569.467 338 C 566.067 338, 563.820 330.808, 556.992 298.068 C 552.491 276.491, 545.947 237.456, 542.980 214.500 C 540.668 196.606, 539.843 193.282, 536.937 190.163 C 533.523 186.499, 529.608 187.311, 523.690 192.912 C 517.442 198.825, 511.948 207.772, 504.516 224.142 C 496.941 240.828, 492.458 248.596, 487.100 254.324 C 479.388 262.569, 474.483 263.768, 471.563 258.122 C 467.444 250.157, 463.030 183.679, 463.010 129.315 C 463 100.208, 462.672 97.427, 458.759 93.250 C 456.075 90.385, 450.177 90.283, 444.194 92.998 C 438.463 95.599, 429.621 104.662, 423.215 114.500 C 415.680 126.074, 400.252 157.808, 392.588 177.500 C 383.916 199.782, 382.070 203.419, 379.226 203.823 C 374.328 204.518, 371.621 192.540, 369.999 163 C 367.662 120.429, 369.660 77.915, 376.054 34.195 C 378.544 17.170, 378.599 9.824, 376.250 7.807 C 373.322 5.292, 371.547 5.122, 366.419 6.863';
?>
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/index/Poster.css')) ?>" />
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('Features/Footer/Footer.css')) ?>" />

<footer class="ft" id="site-footer">
  <div class="ft-inner">

    <div class="ft-bar ozz-label">
      <span<?= ozz_alt($ft['number'] ?? '') ?>><?= ozz_tx($ft['number'] ?? '') ?></span>
      <span<?= ozz_alt($ft['kicker'] ?? '') ?>><?= ozz_tx($ft['kicker'] ?? '') ?></span>
      <span>© <?= date('Y') ?></span>
    </div>

    <!-- CTA -->
    <div class="ft-cta">
      <h2 class="ft-title"<?= ozz_alt($ft['title'] ?? '') ?>><?= ozz_tx($ft['title'] ?? '') ?></h2>

      <div class="ft-cta__aside">
        <p class="ft-intro"<?= ozz_alt($ft['intro'] ?? '') ?>><?= ozz_tx($ft['intro'] ?? '') ?></p>

        <a class="ft-btn" <?= ozz_href('Contact') ?>>
          <span<?= ozz_alt($ft['cta'] ?? '') ?>><?= ozz_tx($ft['cta'] ?? '') ?></span>
          <?= $ft_arrow ?>
        </a>
      </div>
    </div>

    <!-- Canales directos -->
    <div class="ft-channels">
      <a class="ft-channel" href="mailto:<?= ozz_e($ft_email) ?>">
        <span class="ozz-label ft-channel__label"<?= ozz_alt($ft['emailLabel'] ?? '') ?>><?= ozz_tx($ft['emailLabel'] ?? '') ?></span>
        <span class="ft-channel__value"><?= ozz_e($ft_email) ?></span>
        <span class="ft-channel__arrow"><?= $ft_arrow ?></span>
      </a>

      <a class="ft-channel" href="<?= ozz_e($ft_whatsapp) ?>" target="_blank" rel="noopener">
        <span class="ozz-label ft-channel__label"<?= ozz_alt($ft['whatsappLabel'] ?? '') ?>><?= ozz_tx($ft['whatsappLabel'] ?? '') ?></span>
        <span class="ft-channel__value"><?= ozz_e($ft_phone) ?></span>
        <span class="ft-channel__arrow"><?= $ft_arrow ?></span>
      </a>
    </div>

    <!-- Columnas -->
    <div class="ft-cols">
      <nav class="ft-col" aria-labelledby="ft-nav-title">
        <h3 class="ozz-label ft-col__title" id="ft-nav-title"<?= ozz_alt($ft['navTitle'] ?? '') ?>><?= ozz_tx($ft['navTitle'] ?? '') ?></h3>
        <ul>
          <?php foreach ($ft_nav as $ft_item): ?>
          <li><a <?= ozz_href($ft_item['slug']) ?><?= ozz_alt($ft_item['label']) ?>><?= ozz_tx($ft_item['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <nav class="ft-col" aria-labelledby="ft-svc-title">
        <h3 class="ozz-label ft-col__title" id="ft-svc-title"<?= ozz_alt($ft['servicesTitle'] ?? '') ?>><?= ozz_tx($ft['servicesTitle'] ?? '') ?></h3>
        <ul>
          <?php foreach ($ft_services as $ft_item): ?>
          <li><a <?= ozz_href($ft_item['slug'] ?? 'Services') ?><?= ozz_alt($ft_item['title'] ?? '') ?>><?= ozz_tx($ft_item['title'] ?? '') ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <nav class="ft-col" aria-labelledby="ft-exp-title">
        <h3 class="ozz-label ft-col__title" id="ft-exp-title"<?= ozz_alt($ft['expertiseTitle'] ?? '') ?>><?= ozz_tx($ft['expertiseTitle'] ?? '') ?></h3>
        <ul>
          <?php foreach ($ft_expertise as $ft_item): ?>
          <li><a <?= ozz_href($ft_item['slug'] ?? 'Expertise') ?><?= ozz_alt($ft_item['title'] ?? '') ?>><?= ozz_tx($ft_item['title'] ?? '') ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <div class="ft-col">
        <h3 class="ozz-label ft-col__title"<?= ozz_alt($ft['socialTitle'] ?? '') ?>><?= ozz_tx($ft['socialTitle'] ?? '') ?></h3>
        <ul>
          <?php foreach ($ft_social as $ft_item): ?>
          <li><a href="<?= ozz_e($ft_item['href']) ?>"<?= $ft_item['href'] !== '#' ? ' target="_blank" rel="noopener"' : '' ?>><?= ozz_e($ft_item['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- Legal -->
    <div class="ft-legal">
      <span class="ft-legal__copy">© <?= date('Y') ?> <span<?= ozz_alt($ft_studio) ?>><?= ozz_tx($ft_studio) ?></span>. <span<?= ozz_alt($ft['rights'] ?? '') ?>><?= ozz_tx($ft['rights'] ?? '') ?></span></span>

      <div class="ft-legal__links">
        <a <?= ozz_href('Privacy_Policy') ?><?= ozz_alt($ft['privacy'] ?? '') ?>><?= ozz_tx($ft['privacy'] ?? '') ?></a>
        <a <?= ozz_href('Sitemap') ?><?= ozz_alt($ft['sitemap'] ?? '') ?>><?= ozz_tx($ft['sitemap'] ?? '') ?></a>
        <button type="button" class="ft-top" data-ft-top>
          <span<?= ozz_alt($ft['backTop'] ?? '') ?>><?= ozz_tx($ft['backTop'] ?? '') ?></span>
          <svg viewBox="0 0 640 640" aria-hidden="true" focusable="false"><path d="M342.6 73.4C330.1 60.9 309.8 60.9 297.3 73.4L137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7C149.8 291.2 170.1 291.2 182.6 278.7L288 173.3L288 544C288 561.7 302.3 576 320 576C337.7 576 352 561.7 352 544L352 173.3L457.4 278.7C469.9 291.2 490.2 291.2 502.7 278.7C515.2 266.2 515.2 245.9 502.7 233.4L342.7 73.4z"/></svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Animación OZZ -->
  <div class="ft-wordmark" aria-hidden="true">
    <div class="ft-word ft-word--l"><span>O</span><span>Z</span><span>Z</span></div>
    <svg class="ft-flame" viewBox="0 0 787 787"><path d="<?= $ft_flame ?>"/></svg>
    <div class="ft-word ft-word--r"><span>O</span><span>Z</span><span>Z</span></div>
  </div>
</footer>

<script defer src="<?= ozz_e(ozz_asset('Features/Footer/FooterOzz.js')) ?>"></script>
