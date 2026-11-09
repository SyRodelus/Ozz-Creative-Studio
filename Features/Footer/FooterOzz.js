/* =========================================================
   FooterOzz.js — animación de las letras OZZ del footer
   ---------------------------------------------------------
   Mantiene la idea original (dos "OZZ" que se separan hacia
   los bordes al hacer scroll) y la mejora:
   - Las dos palabras parten superpuestas en el centro (se leen
     como un solo OZZ) y se abren hacia los bordes.
   - Cada letra sube desde abajo de forma escalonada.
   - La llama de la marca crece en el hueco que dejan.
   - Inclinación según la velocidad del scroll (inercia).
   - Movimiento suavizado (lerp) a 60 fps sin GSAP ni
     ScrollTrigger (el footer anterior cargaba GSAP otra vez y
     ejecutaba ScrollTrigger.getAll().kill(), que borraba las
     animaciones de toda la página).
   - Modo cortina en escritorio: el footer queda fijo debajo
     del contenido y se descubre al hacer scroll.
   - Respeta prefers-reduced-motion.
========================================================= */

(function () {
  "use strict";

  const footer = document.getElementById("site-footer");
  if (!footer) return;

  const wordmark = footer.querySelector(".ft-wordmark");
  const left = footer.querySelector(".ft-word--l");
  const right = footer.querySelector(".ft-word--r");
  const flame = footer.querySelector(".ft-flame");
  const main = document.getElementById("main");
  if (!wordmark || !left || !right) return;

  const lettersL = Array.from(left.children);
  const lettersR = Array.from(right.children).reverse(); // de fuera hacia dentro
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const curtainMQ = window.matchMedia("(min-width: 900px) and (min-height: 680px)");

  const clamp = (v, a, b) => Math.min(Math.max(v, a), b);
  const easeInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);
  const easeOut = (t) => 1 - Math.pow(1 - t, 3);

  let shift = 0; // distancia de cada palabra hasta el centro
  let target = 0; // progreso objetivo (scroll)
  let current = 0; // progreso suavizado
  let skew = 0;
  let raf = 0;
  let curtain = false;

  /* ---------- Medidas ---------- */

  function fitWords() {
    // Tamaño de letra para que cada OZZ ocupe ~41% del ancho
    const width = wordmark.clientWidth;
    left.style.fontSize = "100px";
    const w100 = left.scrollWidth || 1;
    left.style.fontSize = "";

    const size = Math.max(36, (width * 0.4 * 100) / w100);
    wordmark.style.setProperty("--ft-word-size", size.toFixed(1) + "px");

    // Distancia desde la posición final (bordes) hasta el centro
    const wordW = left.offsetWidth;
    const pad = parseFloat(getComputedStyle(wordmark).paddingLeft) || 0;
    shift = width / 2 - pad - wordW / 2;
  }

  function setMode() {
    const shouldCurtain =
      !!main &&
      curtainMQ.matches &&
      !reduceMotion.matches &&
      footer.scrollHeight <= window.innerHeight;

    if (shouldCurtain !== curtain) {
      curtain = shouldCurtain;
      document.body.classList.toggle("ft-curtain", curtain);
    }
  }

  /* ---------- Progreso de scroll ---------- */

  function readTarget() {
    const vh = window.innerHeight;

    if (curtain && main) {
      // Cortina: 0 cuando el contenido aún tapa el footer, 1 cuando se ve entero
      const revealed = vh - main.getBoundingClientRect().bottom;
      target = clamp(revealed / footer.offsetHeight, 0, 1);
    } else {
      // Normal: desde que la franja OZZ entra hasta el final de la página
      const top = wordmark.getBoundingClientRect().top;
      const end = footer.getBoundingClientRect().bottom;
      const range = Math.max(1, end - top);
      target = clamp((vh - top) / range, 0, 1);
    }
  }

  /* ---------- Render ---------- */

  function render(p, velocity) {
    const e = easeInOut(p);
    const dx = (1 - e) * shift;

    skew += (clamp(velocity * 260, -9, 9) - skew) * 0.15;

    left.style.transform = `translate3d(${dx.toFixed(2)}px,0,0) skewX(${(-skew).toFixed(2)}deg)`;
    right.style.transform = `translate3d(${(-dx).toFixed(2)}px,0,0) skewX(${(-skew).toFixed(2)}deg)`;

    const rise = (letters) =>
      letters.forEach((letter, i) => {
        const lp = easeOut(clamp((p - i * 0.1) / 0.62, 0, 1));
        letter.style.transform = `translate3d(0,${((1 - lp) * 105).toFixed(2)}%,0)`;
      });

    rise(lettersL);
    rise(lettersR);

    if (flame) {
      const fp = clamp((e - 0.25) / 0.75, 0, 1);
      flame.style.opacity = fp.toFixed(3);
      flame.style.transform = `translateX(-50%) scale(${(0.35 + 0.65 * easeOut(fp)).toFixed(3)}) rotate(${((1 - fp) * -18).toFixed(2)}deg)`;
    }
  }

  function tick() {
    raf = 0;
    const prev = current;
    current += (target - current) * 0.12;

    if (Math.abs(target - current) < 0.0005) current = target;

    render(current, current - prev);

    if (current !== target || Math.abs(skew) > 0.02) {
      raf = requestAnimationFrame(tick);
    }
  }

  function onScroll() {
    readTarget();
    if (!raf) raf = requestAnimationFrame(tick);
  }

  /* ---------- Init ---------- */

  function layout() {
    setMode();
    fitWords();
    readTarget();

    if (reduceMotion.matches) {
      current = target = 1;
      render(1, 0);
    } else {
      onScroll();
    }
  }

  function start() {
    footer.classList.add("ft-ready");
    layout();

    // Solo escuchar el scroll cuando el footer está cerca
    const io = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting) {
          window.addEventListener("scroll", onScroll, { passive: true });
          onScroll();
        } else {
          window.removeEventListener("scroll", onScroll);
        }
      },
      { rootMargin: "100% 0px 100% 0px" },
    );
    io.observe(footer);

    let lastW = window.innerWidth;
    let lastH = window.innerHeight;
    let t;
    window.addEventListener("resize", () => {
      // Ignora cambios de alto pequeños (barra del navegador en móvil)
      if (window.innerWidth === lastW && Math.abs(window.innerHeight - lastH) < 120) return;
      lastW = window.innerWidth;
      lastH = window.innerHeight;
      clearTimeout(t);
      t = setTimeout(layout, 150);
    });

    // Al cambiar de idioma cambia la altura de los textos
    document.addEventListener("ozz:langchange", () => setTimeout(layout, 50));

    // Botón "volver arriba"
    footer.querySelectorAll("[data-ft-top]").forEach((btn) =>
      btn.addEventListener("click", () =>
        window.scrollTo({ top: 0, behavior: reduceMotion.matches ? "auto" : "smooth" }),
      ),
    );
  }

  // Esperar la fuente de las letras para medir bien
  const ready = document.fonts && document.fonts.load
    ? Promise.race([
        document.fonts.load('100px "Hanson-Bold"'),
        new Promise((r) => setTimeout(r, 1500)),
      ])
    : Promise.resolve();

  ready.then(start, start);
})();
