<?php
// Plantilla bilingüe generada con tools/i18n-merge.php a partir de
//   HeroText-es.html + HeroText.html
// Textos: pages/Studio/texts/hero.json ({"es": …, "en": …}). Edite los textos allí.
require_once ROOT_PATH . '/config/helpers.php';
$T = ozz_json('pages/Studio/texts/hero.json');
?>
<link rel="stylesheet" href="<?php echo $base_url; ?>/pages/Studio/HeroText.css">

<section class="hero" aria-label="<?= ozz_tx($T['hero.01_hero_aria-label']) ?>" id="idbackground"<?= ozz_alt($T['hero.01_hero_aria-label'], 'aria-label') ?>>
  <h1 class="sliced-words" data-text="<?= ozz_tx($T['hero.02_sliced-words_data-text']) ?>"<?= ozz_alt($T['hero.02_sliced-words_data-text'], 'data-text') ?><?= ozz_alt($T['hero.03_sliced-words']) ?>><?= ozz_tx($T['hero.03_sliced-words']) ?></h1>
</section>

<!-- Copias oficiales sin modificar (las locales tenían "Index" reemplazado por
     "index" en todo el código, p. ej. regex.lastindex, y three.js fallaba) -->
<script src="https://cdn.jsdelivr.net/npm/three@0.134.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanta@0.5.24/dist/vanta.net.min.js"></script>

<script>
  // ========================================
  // VantaBackground.js — Init + reinicio responsive y al "theme:changed"
  // ========================================

  function cssVarToHexInt(varName) {
    const raw = getComputedStyle(document.documentElement)
      .getPropertyValue(varName)
      .trim();
    return new THREE.Color(raw).getHex();
  }

  let vanta = null;
  const MOBILE_BP = 768; // px
  let currentMode = null; // 'mobile' | 'desktop'

  function getConfigForWidth(w) {
    const mobile = w <= MOBILE_BP;
    return {
      mode: mobile ? "mobile" : "desktop",
      options: {
        el: document.getElementById("idbackground"),
        mouseControls: true,
        touchControls: true,
        gyroControls: false,
        minHeight: 200.0,
        minWidth: 200.0,
        scale: 1.0,
        scaleMobile: 1.0,
        color: cssVarToHexInt("--color-text"),
        backgroundColor: cssVarToHexInt("--color-bg"),
        // Valores "desktop"
        points: mobile ? 12.0 : 10.0,
        maxDistance: mobile ? 20.0 : 22.0,
        spacing: mobile ? 25.0 : 20.0,
      },
    };
  }

  function initVanta() {
    // Destruye instancia previa si existe
    if (vanta && typeof vanta.destroy === "function") {
      vanta.destroy();
      vanta = null;
    }

    const { mode, options } = getConfigForWidth(window.innerWidth);
    currentMode = mode;
    vanta = VANTA.NET(options);
  }

  // Debounce genérico para resize
  function debounce(fn, wait = 150) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(null, args), wait);
    };
  }

  // Reinicia Vanta sólo si cambia el modo (mobile/desktop)
  const handleResize = debounce(() => {
    const { mode } = getConfigForWidth(window.innerWidth);
    if (mode !== currentMode) {
      requestAnimationFrame(initVanta);
    }
  }, 150);

  document.addEventListener("DOMContentLoaded", () => {
    initVanta();

    // Reiniciar al cambiar tema (para recalcular colores)
    document.addEventListener("theme:changed", () => {
      requestAnimationFrame(initVanta);
    });

    // Escuchar cambios de tamaño de ventana
    window.addEventListener("resize", handleResize);

    // Alternativamente, también podemos vigilar con matchMedia (opcional pero preciso)
    const mq = window.matchMedia(`(max-width: ${MOBILE_BP}px)`);
    mq.addEventListener
      ? mq.addEventListener("change", () => requestAnimationFrame(initVanta))
      : mq.addListener(() => requestAnimationFrame(initVanta)); // fallback
  });
</script>
