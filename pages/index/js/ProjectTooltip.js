/* =========================================================
   ProjectTooltip.js — etiqueta "Ver proyecto" que sigue al
   cursor sobre las tarjetas de proyecto (.sc-card).
   Un solo archivo para ES/EN: el texto se toma del idioma
   activo, así también funciona tras cambiar de idioma sin
   recargar. Sustituye a TooltipStackCards(-es).js.
========================================================= */

(function () {
  "use strict";

  const mq = window.matchMedia("(hover: hover) and (pointer: fine)");
  if (!mq.matches) return;

  const LABEL = { es: "Ver proyecto", en: "View project" };
  const root = document.documentElement;
  let tip = null;
  let raf = 0;
  let pos = { x: 0, y: 0 };

  function label() {
    const lang = (root.lang || "es").slice(0, 2);
    return LABEL[lang] || LABEL.en;
  }

  function ensureTip() {
    if (tip) return tip;
    tip = document.createElement("div");
    tip.className = "ozz-tt-tooltip";
    tip.setAttribute("aria-hidden", "true");
    document.body.appendChild(tip);
    return tip;
  }

  function place() {
    raf = 0;
    if (!tip) return;

    const margin = 8;
    const offset = 14;
    const r = tip.getBoundingClientRect();
    let left = pos.x + offset;
    let top = pos.y + offset;

    if (left + r.width + margin > window.innerWidth) left = pos.x - r.width - offset;
    if (top + r.height + margin > window.innerHeight) top = pos.y - r.height - offset;

    root.style.setProperty("--ozz-tt-left", Math.round(left) + "px");
    root.style.setProperty("--ozz-tt-top", Math.round(top) + "px");
  }

  function onMove(e) {
    pos = { x: e.clientX, y: e.clientY };
    if (!raf) raf = requestAnimationFrame(place);
  }

  function show(e) {
    const el = ensureTip();
    el.textContent = label();
    el.classList.add("ozz-tt-visible");
    onMove(e);
  }

  function hide() {
    if (tip) tip.classList.remove("ozz-tt-visible");
  }

  document.querySelectorAll(".sc-card").forEach((card) => {
    card.addEventListener("pointerenter", show);
    card.addEventListener("pointermove", onMove);
    card.addEventListener("pointerleave", hide);
  });

  window.addEventListener("scroll", hide, { passive: true });
})();
