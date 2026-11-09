<?php
// ======================================
// IDIOMA (MISMO CRITERIO QUE EL MENÚ)
// ======================================
$current_file = basename($_SERVER['PHP_SELF']);
$is_spanish = str_contains($current_file, '-es.php');
$lang = $is_spanish ? 'es' : 'en';

// ======================================
// TEXTOS HERO WORK
// ======================================
$texts = [
  'en' => [
    'title' => 'Work built with intention.',
    'description' =>
      'Selected projects where strategy, design and execution come together to build clear, scalable and consistent brands. Every case reflects our approach to structure, aesthetics and long term value.',
  ],
  'es' => [
    'title' => 'Trabajo construido con intención.',
    'description' =>
      'Proyectos seleccionados donde estrategia, diseño y ejecución se integran para crear marcas claras, escalables y consistentes. Cada caso refleja nuestro enfoque en estructura, estética y valor a largo plazo.',
  ],
];

// Fallback absoluto
$copy = $texts[$lang] ?? $texts['en'];
?>

<link
  rel="stylesheet"
  href="<?= $base_url; ?>/pages/Work/ProjectsIntro.css"
/>

<section
  id="hero"
  class="projects-intro"
  role="banner"
  aria-label="OZZ Creative Studio work introduction"
>
  <div class="projects-intro__content">
    <h1 class="projects-intro__title">
      <?= htmlspecialchars($copy['title']) ?>
    </h1>

    <p class="projects-intro__description">
      <?= htmlspecialchars($copy['description']) ?>
    </p>
  </div>
</section>
