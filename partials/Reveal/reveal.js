/* =============================
   BUILD LINES (en un elemento)
============================== */

const buildLinesIn = (root) => {
  root.querySelectorAll(".reveal-lines").forEach((el) => {
    if (el.dataset.revealBuilt) return;

    const html = el.innerHTML.trim();
    if (!html) return;

    el.dataset.revealBuilt = "true";

    const parts = html
      .split("<br>")
      .map((line) => line.trim())
      .filter(Boolean);

    el.innerHTML = parts
      .map((line) => `<div class="reveal-line"><span>${line}</span></div>`)
      .join("");
  });
};

/* =============================
   REVEAL INMEDIATO (hero u otro en viewport)
============================== */

const revealElementsIn = (root) => {
  root.querySelectorAll(".reveal-line, .reveal-mask").forEach((el) => {
    if (el.dataset.revealed === "true") return;
    el.dataset.revealed = "true";

    requestAnimationFrame(() => {
      el.classList.add("is-visible", "is-x-open");

      setTimeout(
        () => el.classList.add("is-done"),
        el.classList.contains("reveal-line") ? 900 : 800,
      );
    });
  });
};

/* =============================
   SCROLL OBSERVER
============================== */

const scrollObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;

      const el = entry.target;
      if (el.dataset.revealed === "true") return;
      el.dataset.revealed = "true";

      el.classList.add("is-visible", "is-x-open");

      setTimeout(
        () => el.classList.add("is-done"),
        el.classList.contains("reveal-line") ? 900 : 800,
      );

      scrollObserver.unobserve(el);
    });
  },
  {
    threshold: 0,
    rootMargin: "0px 0px -8% 0px",
  },
);

const bindScrollReveal = (root) => {
  root
    .querySelectorAll(
      ".reveal-line:not([data-scroll-bound]), .reveal-mask:not([data-scroll-bound])",
    )
    .forEach((el) => {
      el.dataset.scrollBound = "true";

      const rect = el.getBoundingClientRect();
      const inViewport = rect.top < window.innerHeight && rect.bottom > 0;

      if (inViewport) {
        if (el.dataset.revealed === "true") return;
        el.dataset.revealed = "true";
        el.classList.add("is-visible", "is-x-open");
        setTimeout(
          () => el.classList.add("is-done"),
          el.classList.contains("reveal-line") ? 900 : 800,
        );
      } else {
        scrollObserver.observe(el);
      }
    });
};

/* =============================
   LISTENER: texto inyectado por i18n
============================== */

document.addEventListener("i18n:sectionLoaded", (e) => {
  const section = e.target;

  // 1. Construir líneas con el texto ya listo
  buildLinesIn(section);

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      if (section.classList.contains("hero")) {
        // Hero: revelar todo directo sin esperar scroll
        if (!window.__HERO_REVEALED__) {
          window.__HERO_REVEALED__ = true;
          revealElementsIn(section);
        }
      } else {
        // Otras secciones: bindear al scroll observer
        bindScrollReveal(section);
      }
    });
  });
});

/* =============================
   RESCAN MANUAL (lazy sections)
============================== */

const rescanReveal = () => {
  buildLinesIn(document);
  bindScrollReveal(document);
};

window.__rescanReveal__ = rescanReveal;

/* =============================
   INIT: secciones sin i18n
============================== */

const initStaticReveal = () => {
  buildLinesIn(document);

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      bindScrollReveal(document);
    });
  });
};

if (document.body.classList.contains("has-preloader")) {
  window.addEventListener(
    "preloader:done",
    () => requestAnimationFrame(initStaticReveal),
    { once: true },
  );
} else {
  requestAnimationFrame(initStaticReveal);
}
