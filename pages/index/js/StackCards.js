/* =========================================================
   StackCards.js — interacción de las tarjetas de proyectos
   Aplica a cada <section class="sc">:
   1. Entrada: cada tarjeta se revela (clip-path) al entrar.
   2. Activa: en táctil, la tarjeta del centro pasa a color.
   3. Profundidad (solo si la sección tiene [data-stack]):
      cuando la fila siguiente cubre una tarjeta sticky, esta
      se reduce y oscurece (variable --cover).
   Sin dependencias (no usa ScrollTrigger).
========================================================= */

(function () {
  "use strict";

  if (!("IntersectionObserver" in window)) return;

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  document.querySelectorAll("section.sc").forEach(initSection);

  function initSection(section) {
    const panels = Array.from(section.querySelectorAll(".sc-panel"));
    const grid = section.querySelector(".sc-grid");
    if (!panels.length || !grid) return;

    section.classList.add("sc-ready");

    /* ---------- 1) Entrada ---------- */

    const revealObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add("is-revealed");
          revealObserver.unobserve(entry.target);
        });
      },
      { threshold: 0.12 },
    );

    panels.forEach((panel) => revealObserver.observe(panel));

    /* ---------- 2) Tarjeta activa (táctil) ---------- */

    const activeObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) =>
          entry.target.classList.toggle("is-active", entry.isIntersecting),
        );
      },
      { rootMargin: "-40% 0px -40% 0px", threshold: 0 },
    );

    panels.forEach((panel) => activeObserver.observe(panel));

    /* ---------- 3) Profundidad del apilado ---------- */

    if (!section.hasAttribute("data-stack")) return;

    const cards = panels.map((p) => p.querySelector(".sc-card"));
    const lastCover = new Array(panels.length).fill(-1);
    let columns = 1;
    let ticking = false;
    let inView = false;

    function readColumns() {
      const tpl = getComputedStyle(grid).gridTemplateColumns;
      columns = Math.max(1, tpl.split(" ").filter(Boolean).length);
    }

    function update() {
      ticking = false;
      if (!inView || reduceMotion.matches) return;

      for (let i = 0; i < panels.length; i++) {
        const next = panels[i + columns];
        let cover = 0;

        if (next) {
          const current = panels[i].getBoundingClientRect();
          const nextTop = next.getBoundingClientRect().top;
          const height = current.height || 1;
          // 0 = la siguiente fila aún no toca; 1 = la cubre por completo
          cover = Math.min(Math.max((current.bottom - nextTop) / height, 0), 1);
        }

        const rounded = Math.round(cover * 1000) / 1000;
        if (rounded !== lastCover[i]) {
          lastCover[i] = rounded;
          cards[i].style.setProperty("--cover", rounded);
        }
      }
    }

    function requestUpdate() {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(update);
    }

    const sectionObserver = new IntersectionObserver(
      (entries) => {
        inView = entries[0].isIntersecting;
        if (inView) {
          window.addEventListener("scroll", requestUpdate, { passive: true });
          requestUpdate();
        } else {
          window.removeEventListener("scroll", requestUpdate);
        }
      },
      { threshold: 0 },
    );

    readColumns();
    sectionObserver.observe(section);

    let lastWidth = window.innerWidth;
    window.addEventListener(
      "resize",
      () => {
        if (window.innerWidth === lastWidth) return; // ignora barra del navegador
        lastWidth = window.innerWidth;
        readColumns();
        requestUpdate();
      },
      { passive: true },
    );

    const onMotionChange = () => {
      if (reduceMotion.matches) {
        cards.forEach((card) => card.style.removeProperty("--cover"));
        lastCover.fill(-1);
      } else {
        requestUpdate();
      }
    };

    if (reduceMotion.addEventListener) {
      reduceMotion.addEventListener("change", onMotionChange);
    }
  }
})();
