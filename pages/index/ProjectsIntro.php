<?php
// ======================================
// IDIOMA (MISMO CRITERIO QUE EL MENÚ)
// ======================================
$current_file = basename($_SERVER['PHP_SELF']);
$is_spanish = str_contains($current_file, '-es.php');
$lang = $is_spanish ? 'es' : 'en';

// ======================================
// TEXTOS
// ======================================
$texts = [
  'en' => [
    'title' => 'Design that creates impact.',
    'description' =>
      'Each project is built on a defined strategic structure that integrates brand positioning, visual systems, and digital production. We develop precise and consistent design solutions focused on clarity, scalability, and long-term brand coherence.',
    'button' => 'Our work',
  ],
  'es' => [
    'title' => 'Diseño que genera impacto.',
    'description' =>
      'Cada proyecto se construye sobre una estructura estratégica definida que integra posicionamiento de marca, sistemas visuales y producción digital. Desarrollamos soluciones de diseño precisas y coherentes, enfocadas en la claridad, la escalabilidad y la consistencia de la marca a largo plazo.',
    'button' => 'Nuestro trabajo',
  ],
];


// Fallback absoluto
$copy = $texts[$lang] ?? $texts['en'];

// ======================================
// LINK DE PROYECTOS SEGÚN IDIOMA
// ======================================
$projects_url = $base_url . '/Projects' . ($is_spanish ? '-es' : '') . '.php';
?>

<link
  rel="stylesheet"
  href="<?= $base_url; ?>/pages/index/ProjectsIntro.css"
/>

<section class="projects-intro">
  <div class="projects-intro__content">
    <h1 class="projects-intro__title">
      <?= htmlspecialchars($copy['title']) ?>
    </h1>

    <p class="projects-intro__description">
      <?= htmlspecialchars($copy['description']) ?>
    </p>

    <a href="<?= htmlspecialchars($projects_url) ?>">
      <div class="projects-intro__cta">
        <span class="projects-intro__cta-text">
          <?= htmlspecialchars($copy['button']) ?>
        </span>
      </div>
    </a>
  </div>
</section>
