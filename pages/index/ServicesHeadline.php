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
    'title' => 'How We Work',
    'description' =>
      'Ozz delivers services through a clear and adaptable process, designed to respond to different needs, stages, and levels of involvement. We assess, define, and execute with precision, ensuring each engagement is aligned with specific goals and measurable outcomes.',
    'button' => 'Our Services',
  ],
  'es' => [
    'title' => 'Nuestro enfoque de trabajo',
    'description' =>
      'En Ozz ofrecemos servicios a través de un proceso claro y adaptable, pensado para responder a distintas necesidades, etapas y niveles de acompañamiento. Analizamos, definimos y ejecutamos con precisión, asegurando que cada intervención esté alineada con objetivos concretos y resultados medibles.',
    'button' => 'Nuestros servicios',
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
