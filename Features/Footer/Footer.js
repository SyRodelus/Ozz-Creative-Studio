gsap.registerPlugin(ScrollTrigger);

function setupOzz(scaleFactor = 1) {
  // Limpia triggers previos
  ScrollTrigger.getAll().forEach((t) => t.kill());

  const left = document.querySelector(".Ozz-Text-Animation h1:first-child");
  const right = document.querySelector(".Ozz-Text-Animation h1:last-child");
  const wrapper =
    document.querySelector(".footer-wrapper") ||
    document.querySelector("footer");

  if (!left || !right || !wrapper) return;

  // Ocultamos el bloque hasta que el footer sea visible (no altera posicionamiento)
  gsap.set(".Ozz-Text-Animation", { autoAlpha: 0 });

  // Mostrar el bloque únicamente cuando el footer entra
  ScrollTrigger.create({
    trigger: wrapper,
    start: "top bottom", // cuando el footer empieza a entrar en pantalla
    onEnter: () =>
      gsap.to(".Ozz-Text-Animation", { autoAlpha: 1, duration: 0.5 }),
    onLeaveBack: () =>
      gsap.to(".Ozz-Text-Animation", { autoAlpha: 0, duration: 0.5 }),
  });

  // 🔹 Calcular font-size proporcional al ancho del viewport (igual que el tuyo)
  let vw = window.innerWidth;
  let fontSize;

  if (vw <= 1024) {
    fontSize = Math.min(Math.max(60, vw * 0.25), 220) + "px";
  } else {
    fontSize = Math.min(Math.max(100, vw * 0.15), 260) + "px";
  }

  left.style.fontSize = fontSize;
  right.style.fontSize = fontSize;

  // ⭐ Tu misma lógica de desplazamientos (sin cambios en destino ni dirección)
  function measureDisplacements() {
    const leftRect = left.getBoundingClientRect();
    const rightRect = right.getBoundingClientRect();
    const leftCenter = leftRect.left + leftRect.width / 2;
    const rightCenter = rightRect.left + rightRect.width / 2;
    const vw = window.innerWidth;

    const leftTargetCenter = 0; // borde izq de viewport (tal cual)
    const rightTargetCenter = vw; // borde der de viewport (tal cual)

    const leftDisp = (leftTargetCenter - leftCenter) * scaleFactor;
    const rightDisp = (rightTargetCenter - rightCenter) * scaleFactor;

    return { leftDisp, rightDisp };
  }

  function buildAnimations() {
    const { leftDisp, rightDisp } = measureDisplacements();

    gsap.to(left, {
      x: leftDisp,
      ease: "none",
      overwrite: true,
      scrollTrigger: {
        trigger: wrapper,
        start: "top bottom",
        end: "bottom bottom",
        scrub: true,
      },
    });

    gsap.to(right, {
      x: rightDisp,
      ease: "none",
      overwrite: true,
      scrollTrigger: {
        trigger: wrapper,
        start: "top bottom",
        end: "bottom bottom",
        scrub: true,
      },
    });
  }

  // Construimos tras cargar fuentes o con pequeño delay (igual que tu enfoque)
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(() => {
      buildAnimations();
      ScrollTrigger.refresh();
    });
  } else {
    setTimeout(() => {
      buildAnimations();
      ScrollTrigger.refresh();
    }, 60);
  }
}

function autoResizeSetup() {
  let lastWidth = window.innerWidth;

  function checkWidth() {
    if (window.innerWidth !== lastWidth) {
      lastWidth = window.innerWidth;
      if (lastWidth <= 1024) {
        setupOzz(0.33);
      } else {
        setupOzz(1);
      }
    }
    requestAnimationFrame(checkWidth);
  }

  if (lastWidth <= 1024) {
    setupOzz(0.33);
  } else {
    setupOzz(1);
  }

  checkWidth();
}

/* 🔒 Arranque solo cuando la altura/layout estén “quietos”
   - Espera a que no haya cambios de layout durante `SILENCE_MS`
   - No cambia tu animación, solo el momento en que se inicializa autoResizeSetup()
*/
function startWhenLayoutStable(startFn) {
  const SILENCE_MS = 250;
  let lastChange = Date.now();

  function kickOff() {
    // Llamamos tu autoResizeSetup intacto
    startFn();
  }

  if (!("ResizeObserver" in window)) {
    // Fallback: tras load
    window.addEventListener("load", kickOff, { once: true });
    return;
  }

  const ro = new ResizeObserver(() => {
    lastChange = Date.now();
  });

  // Observamos body completo (cubre sitemap u otros módulos que cambian altura)
  ro.observe(document.body);

  const tick = () => {
    if (Date.now() - lastChange >= SILENCE_MS) {
      ro.disconnect();
      kickOff();
    } else {
      requestAnimationFrame(tick);
    }
  };
  requestAnimationFrame(tick);
}

// Si algún módulo re-renderiza (ej. Sitemap) puede despachar este evento
document.addEventListener("layout:refresh", () => {
  if (window.ScrollTrigger && ScrollTrigger.refresh) {
    ScrollTrigger.refresh();
  }
});

// ⛳ En vez de llamar directo a autoResizeSetup, lo hacemos cuando la altura ya está definida
startWhenLayoutStable(autoResizeSetup);
