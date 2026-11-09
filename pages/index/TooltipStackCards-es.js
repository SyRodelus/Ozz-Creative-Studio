(function () {
  const LINK_SELECTOR = ".no-underline"; // enlaces donde aparece el tooltip
  let ozzTooltipEl = null;
  let ozzMoveHandler = null;

  function ozzSetVar(name, value) {
    document.documentElement.style.setProperty(name, value);
  }

  function ozzCreateTooltip() {
    if (ozzTooltipEl) return ozzTooltipEl;
    const el = document.createElement("div");
    el.className = "ozz-tt-tooltip";
    el.setAttribute("role", "tooltip");
    el.textContent = "Más"; // texto fijo
    document.body.appendChild(el);
    ozzTooltipEl = el;
    return el;
  }

  function ozzShowTooltip(e) {
    const el = ozzCreateTooltip();
    el.classList.add("ozz-tt-visible");

    if (e) {
      ozzPositionTooltip(e);
    } else {
      // fallback: centro de la ventana
      ozzPositionTooltip({
        clientX: window.innerWidth / 2,
        clientY: window.innerHeight / 2,
      });
    }
  }

  function ozzHideTooltip() {
    if (!ozzTooltipEl) return;
    ozzTooltipEl.classList.remove("ozz-tt-visible");
  }

  function ozzPositionTooltip(e) {
    if (!ozzTooltipEl) return;
    const padding = 12,
      margin = 8;
    const tRect = ozzTooltipEl.getBoundingClientRect();
    const vw = window.innerWidth,
      vh = window.innerHeight;
    let left = e.clientX + padding;
    let top = e.clientY + padding;

    if (left + tRect.width + margin > vw) {
      left = e.clientX - tRect.width - padding;
      if (left < margin) left = Math.max(margin, vw - tRect.width - margin);
    }
    if (top + tRect.height + margin > vh) {
      top = e.clientY - tRect.height - padding;
      if (top < margin) top = Math.max(margin, vh - tRect.height - margin);
    }

    ozzSetVar("--ozz-tt-left", Math.round(left) + "px");
    ozzSetVar("--ozz-tt-top", Math.round(top) + "px");
  }

  function ozzInitDesktop() {
    const links = document.querySelectorAll(LINK_SELECTOR);
    if (!links.length) return;

    // handler con requestAnimationFrame
    ozzMoveHandler = (ev) =>
      requestAnimationFrame(() => ozzPositionTooltip(ev));

    links.forEach((link) => {
      link.addEventListener("mouseenter", (e) => {
        ozzShowTooltip(e);
        document.addEventListener("mousemove", ozzMoveHandler);
      });
      link.addEventListener("mouseleave", () => {
        ozzHideTooltip();
        document.removeEventListener("mousemove", ozzMoveHandler);
      });
      link.addEventListener("focus", () => {
        ozzShowTooltip();
        const rect = link.getBoundingClientRect();
        ozzPositionTooltip({
          clientX: rect.left + 10,
          clientY: rect.top + 10,
        });
      });
      link.addEventListener("blur", ozzHideTooltip);
    });

    window.addEventListener("scroll", ozzHideTooltip, { passive: true });
  }

  function ozzInitAll() {
    const mq = window.matchMedia("(hover: hover) and (pointer: fine)");

    // incluye fallback para híbridos (ej: laptops táctiles)
    if (mq.matches || !("ontouchstart" in window)) {
      ozzInitDesktop();
    }
  }

  document.addEventListener("DOMContentLoaded", ozzInitAll);
})();
