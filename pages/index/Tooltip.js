// ValuesSection.integrado.js
(() => {
  // ======== Config fondo (las mismas imágenes que usabas) ========
  const BG_SRCS = [
    "pages/index/Impact.webp",
    "pages/index/Clarity.webp",
    "pages/index/Difference.webp",
    "pages/index/Results.webp",
  ];

  // ======== Estado y utilidades comunes ========
  const TAP_TIME = 220; // umbral tap (ms) para móvil
  const root = document.documentElement;
  const htmlEl = document.documentElement;

  // Precarga
  const preloadedImages = BG_SRCS.map((src) => {
    const img = new Image();
    img.src = src;
    return img;
  });

  // Activar / desactivar fondo
  function activateBG(i) {
    const img = preloadedImages[i];
    if (!img || !img.src) return; // guard por si faltan imágenes
    root.style.setProperty("--page-bg-image", `url("${img.src}")`);
    root.style.setProperty("--page-bg-opacity", "1");
    htmlEl.classList.add("values-bg-active");
  }
  function deactivateBG() {
    root.style.setProperty("--page-bg-opacity", "0");
    htmlEl.classList.remove("values-bg-active");
  }

  // ======== Tooltip flotante (desktop / teclado) ========
  let floating;
  let mouseMoveHandler;

  function ensureFloating() {
    if (!floating) {
      floating = document.createElement("div");
      floating.className = "vv-tooltip";
      floating.setAttribute("role", "tooltip");
      document.body.appendChild(floating);
    }
    return floating;
  }
  function showFloating(text) {
    const tip = ensureFloating();
    tip.textContent = text;
    tip.classList.add("vv-tooltip--visible");
  }
  function hideFloating() {
    if (floating) floating.classList.remove("vv-tooltip--visible");
  }
  function attachDesktop(titleEl) {
    const txt = titleEl.dataset.tooltip || titleEl.textContent.trim();

    titleEl.addEventListener("mouseenter", () => showFloating(txt));
    titleEl.addEventListener("mouseleave", hideFloating);
    titleEl.addEventListener("focus", () => showFloating(txt));
    titleEl.addEventListener("blur", hideFloating);

    if (!mouseMoveHandler) {
      mouseMoveHandler = (ev) => {
        const r = document.documentElement;
        r.style.setProperty("--vv-tooltip-left", ev.clientX + "px");
        r.style.setProperty("--vv-tooltip-top", ev.clientY + "px");
      };
      window.addEventListener("mousemove", mouseMoveHandler, { passive: true });
    }
  }

  // ======== Tooltip inline (móvil): no se cierra por fuera ========
  function attachMobile(card, titleEl, index) {
    const state = { startX: 0, startY: 0, startTime: 0, moved: false };

    const onTouchStart = (ev) => {
      const t = ev.touches && ev.touches[0] ? ev.touches[0] : ev;
      state.startX = t.clientX || 0;
      state.startY = t.clientY || 0;
      state.startTime = Date.now();
      state.moved = false;
    };

    const onTouchMove = (ev) => {
      const t = ev.touches && ev.touches[0] ? ev.touches[0] : ev;
      const dx = Math.abs((t.clientX || 0) - state.startX);
      const dy = Math.abs((t.clientY || 0) - state.startY);
      if (dx > 8 || dy > 8) state.moved = true;
    };

    const onTouchEnd = (ev) => {
      const elapsed = Date.now() - state.startTime;
      if (state.moved || elapsed > TAP_TIME) return;

      // Si ya está abierto en esta card, no hagas nada (no cerrar por fuera)
      const existingInline = card.querySelector(".vv-tooltip-inline");
      if (existingInline) {
        ev.stopPropagation();
        ev.preventDefault();
        return;
      }

      // Cierra otros tooltips abiertos (si hay) antes de abrir el nuevo
      document
        .querySelectorAll(".vv-tooltip-inline")
        .forEach((n) => n.remove());

      // Crea tooltip inline
      const inline = document.createElement("div");
      inline.className = "vv-tooltip-inline";
      inline.setAttribute("role", "dialog");
      inline.setAttribute("aria-modal", "true");

      const txt = document.createElement("div");
      txt.className = "vv-text";
      txt.textContent = titleEl.dataset.tooltip || titleEl.textContent.trim();

      const btn = document.createElement("button");
      btn.className = "vv-close";
      btn.type = "button";
      btn.setAttribute("aria-label", "Close");
      btn.innerText = "×";
      btn.addEventListener("click", (evBtn) => {
        evBtn.stopPropagation();
        if (inline && inline.parentNode) inline.parentNode.removeChild(inline);
        // Desactiva fondo al cerrar
        deactivateBG();
      });

      inline.appendChild(txt);
      inline.appendChild(btn);
      card.appendChild(inline);

      // Activa fondo al abrir
      activateBG(index);

      // Bloquea propagación para que ningún otro handler interfiera
      ev.stopPropagation();
      ev.preventDefault();
    };

    // Bind
    titleEl.addEventListener("touchstart", onTouchStart, { passive: true });
    titleEl.addEventListener("touchmove", onTouchMove, { passive: true });
    titleEl.addEventListener("touchend", onTouchEnd);
  }

  // ======== Inicialización unificada ========
  document.addEventListener("DOMContentLoaded", () => {
    const titles = document.querySelectorAll(
      "#ValuesSection .card .card-title",
    );

    titles.forEach((title, i) => {
      const card = title.closest(".card");

      // Fondo en desktop/teclado (como tenías)
      title.addEventListener("mouseenter", () => activateBG(i));
      title.addEventListener("mouseleave", deactivateBG);
      title.addEventListener("focus", () => activateBG(i));
      title.addEventListener("blur", deactivateBG);

      // Tooltip flotante (desktop)
      attachDesktop(title);

      // Tooltip inline (móvil) que NO se cierra al tocar fuera
      attachMobile(card, title, i);
    });

    // 🔕 Importante: NO añadimos ningún "outside click/touch" global.
    // El cierre en móvil solo ocurre con .vv-close.
  });
})();
