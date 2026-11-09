<!-- Favicons principales (usar rutas absolutas para Google) -->
<link
  rel="icon"
  href="https://ozzcreativestudio.com/favicon.ico"
  type="image/x-icon"
/>
<link
  rel="icon"
  type="image/png"
  sizes="48x48"
  href="https://ozzcreativestudio.com/favicon.png"
/>
<link
  rel="icon"
  type="image/png"
  sizes="96x96"
  href="https://ozzcreativestudio.com/favicon-96x96.png"
/>

<!-- Icono para dispositivos Apple (iOS/iPadOS) -->
<link
  rel="apple-touch-icon"
  sizes="180x180"
  href="https://ozzcreativestudio.com/apple-touch-icon.png"
/>

<!-- Manifest -->
<link rel="manifest" href="https://ozzcreativestudio.com/site.webmanifest" />

<script>
  window.BASE_URL = <?= json_encode(BASE_URL, JSON_UNESCAPED_SLASHES) ?>;
  window.IS_LOCAL = <?= json_encode(IS_LOCAL) ?>;
</script>

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

<link
  href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Limelight&family=Monoton&family=Nabla&family=Protest+Guerrilla&family=Rubik+Glitch&family=Turret+Road:wght@700&display=swap"
  rel="stylesheet"
/>

<script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/CustomEase.min.js"></script>