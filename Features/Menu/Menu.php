<?php
// ======================================================
// MENÚ PRINCIPAL (ES / EN)
// ------------------------------------------------------
// - Etiquetas: lang/ui.json → "menu"
// - Submenús de Servicios y Especialidades: se leen de
//   pages/index/texts/services.json y expertise.json
//   (misma fuente que las secciones del inicio).
// - Cada texto lleva data-alt para el cambio de idioma
//   sin recarga (Scripts/LangSwitch.js).
// ======================================================

require_once ROOT_PATH . '/config/helpers.php';

// ======================================================
// ENTORNO / BASE URL (variables usadas por otros parciales)
// ======================================================
$base_url = BASE_URL;

$current_file = basename($_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? 'index.php'));
$query_string = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';

$is_spanish = ozz_lang() === 'es';
$lang_suffix = $is_spanish ? '-es.php' : '.php';
$lang = ozz_lang();

// URL equivalente en el otro idioma (la usa Tools.php)
$switch_url = ozz_current_page_url(ozz_other_lang());

// Página actual (para marcar el ítem activo)
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$current = preg_replace(['/\.php$/', '/-es$/'], '', basename($uri)) ?: 'index';

// ======================================================
// DATOS
// ======================================================
$menu_ui = ozz_json('lang/ui.json');
$menu_labels = $menu_ui['menu'] ?? [];

$menu_children = static function (string $file): array {
    $items = ozz_json($file)['items'] ?? [];

    return array_map(static fn($item) => [
        'slug'        => $item['slug'] ?? '',
        'title'       => $item['title'] ?? '',
        'description' => $item['menuDescription'] ?? ($item['description'] ?? ''),
    ], $items);
};

$menu = [
    'Services'  => ['slug' => 'Services',  'children' => $menu_children('pages/index/texts/services.json')],
    'Expertise' => ['slug' => 'Expertise', 'children' => $menu_children('pages/index/texts/expertise.json')],
    'Work'      => ['slug' => 'Work'],
    'Studio'    => ['slug' => 'Studio'],
    'Contact'   => ['slug' => 'Contact'],
];

foreach ($menu as $key => &$item) {
    $item['label'] = $menu_labels[$key] ?? ['es' => $key, 'en' => $key];
}
unset($item);

$home_aria = $menu_ui['common']['homeAria'] ?? '';

$logo_svg = '<svg class="LogoOzz" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 94 26" aria-hidden="true" focusable="false"><path d="M8.3 1C6.6 1.6 4 3.5 2.5 5.2.1 8-.2 9.1.2 13.8c.4 4 1.1 6 3 7.8 5.7 5.8 22.4 5.9 28.4.3 4.9-4.6 3.7-15.7-2.1-19.5C26.4.4 13.3-.5 8.3 1m14.9 7.7c1 .9 1.8 2.7 1.8 4.1 0 4-2.7 6.2-7.6 6.2-3.4 0-4.7-.5-6.3-2.7-2.3-2.8-2-5.9.7-8.1 2.3-1.8 9.2-1.5 11.4.5"/><path d="M35 4v3.9l7.3.3 7.2.3-7.2 4.5c-7.2 4.4-7.3 4.5-7.3 8.7V26h59v-9.2l-6.9.4c-3.8.2-6.7 0-6.5-.3.2-.4 3.3-2.4 6.9-4.5 6.3-3.8 6.5-4 6.5-8.2V0H35zm44 4.4c0 .2-3.1 2.2-6.9 4.5-6.4 3.8-7.5 4.1-14.2 4.1h-7.3l6.9-4.4c6.6-4.2 7.4-4.5 14.3-4.5 3.9-.1 7.2.1 7.2.3"/></svg>';

$chevron_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 174 174" aria-hidden="true" focusable="false"><path d="M14.225 54.274C10.251 58.274 7 61.989 7 62.528c0 1.529 79.632 80.767 80.746 80.346C89.385 142.254 169 63.385 169 62.381c0-.512-3.379-4.288-7.51-8.391l-7.509-7.459L120.99 79.51 88 112.489 55.244 79.744C37.229 61.735 22.255 47 21.969 47c-.285 0-3.77 3.273-7.744 7.274"/></svg>';

$arrow_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" aria-hidden="true" focusable="false"><path d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z"/></svg>';
?>

<link rel="stylesheet" href="<?= ozz_e(ozz_asset('Features/Menu/Menu.css')) ?>" />

<nav class="menu">

    <!-- MENU -->
    <div class="nav-space">
        <ul class="nav">

            <!-- HOME -->
            <li class="nav-item home">
                <a
                    class="nav-link"
                    <?= ozz_href('index') ?>
                    aria-label="<?= ozz_tx($home_aria) ?>"<?= ozz_alt($home_aria, 'aria-label') ?>
                >
                    <div class="logo"><?= $logo_svg ?></div>
                </a>
            </li>

            <?php foreach ($menu as $key => $item):
                $active = ($key === $current);
                $hasChildren = !empty($item['children']);
            ?>
                <li class="nav-item <?= $active ? 'active' : '' ?> <?= $hasChildren ? 'has-mega' : '' ?>">

                    <a
                        class="nav-link arrow-activation"
                        <?= ozz_href($item['slug']) ?>
                        <?= $active ? 'aria-current="page"' : '' ?>
                    >
                        <span class="nav-link-inner">
                            <span<?= ozz_alt($item['label']) ?>><?= ozz_tx($item['label']) ?></span>

                            <?php if ($hasChildren): ?>
                                <span class="nav-chevron"><?= $chevron_svg ?></span>
                            <?php elseif ($item['slug'] === 'Contact'): ?>
                                <span class="mega-arrow">
                                    <?php include ROOT_PATH . '/partials/arrowactivation/arrowactivation.html'; ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </a>

                    <?php if ($hasChildren): ?>
                        <div class="mega-menu">
                            <div class="mega-grid">
                                <?php foreach ($item['children'] as $child): ?>
                                    <a <?= ozz_href($child['slug']) ?> class="mega-card arrow-activation">
                                        <div class="mega-content">
                                            <h3<?= ozz_alt($child['title']) ?>><?= ozz_tx($child['title']) ?></h3>
                                            <p<?= ozz_alt($child['description']) ?>><?= ozz_tx($child['description']) ?></p>
                                        </div>

                                        <div class="mega-arrow">
                                            <?php include ROOT_PATH . '/partials/arrowactivation/arrowactivation.html'; ?>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </li>
            <?php endforeach; ?>

        </ul>
    </div>

    <!-- ===========================
        MOBILE HEADER
    =========================== -->
    <div class="mobile-topbar">
        <a class="mobile-logo" <?= ozz_href('index') ?> aria-label="<?= ozz_tx($home_aria) ?>"<?= ozz_alt($home_aria, 'aria-label') ?>>
            <div class="logo"><?= $logo_svg ?></div>
        </a>

        <div class="toggle-box" id="OpenMenu">
            <div class="menu-toggle">
                <?php include ROOT_PATH . '/Features/Menu/HamburguerMenu.html'; ?>
            </div>
        </div>
    </div>

    <!-- ===========================
        MOBILE PANEL
    =========================== -->
    <div class="mobile-menu" id="MobileMenu">
        <div class="mobile-menu-inner">

            <?php foreach ($menu as $key => $item):
                $hasChildren = !empty($item['children']);
            ?>
                <div class="mobile-item <?= $hasChildren ? 'has-submenu' : '' ?>">

                    <?php if ($hasChildren): ?>
                        <button class="mobile-title" type="button">
                            <span<?= ozz_alt($item['label']) ?>><?= ozz_tx($item['label']) ?></span>
                            <span class="nav-chevron"><?= $chevron_svg ?></span>
                        </button>

                        <div class="mobile-submenu">
                            <?php foreach ($item['children'] as $child): ?>
                                <a <?= ozz_href($child['slug']) ?> class="mobile-card">
                                    <div class="mobile-card-text">
                                        <h3<?= ozz_alt($child['title']) ?>><?= ozz_tx($child['title']) ?></h3>
                                        <p<?= ozz_alt($child['description']) ?>><?= ozz_tx($child['description']) ?></p>
                                    </div>
                                    <div class="mega-arrow"><?= $arrow_svg ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>

                    <?php else: ?>
                        <a <?= ozz_href($item['slug']) ?> class="mobile-link">
                            <span<?= ozz_alt($item['label']) ?>><?= ozz_tx($item['label']) ?></span>
                        </a>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

        </div>
    </div>
</nav>

<!-- SCRIPTS -->
<script src="<?= ozz_e(ozz_asset('Features/Menu/visible.js')) ?>"></script>
<script src="<?= ozz_e(ozz_asset('Scripts/Fixed-Dvh.js')) ?>"></script>
<script src="<?= ozz_e(ozz_asset('Features/Menu/ShowMenuMobile.js')) ?>"></script>
