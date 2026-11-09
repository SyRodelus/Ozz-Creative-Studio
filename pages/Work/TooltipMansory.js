(function () {
  const TILE_SELECTOR = '.tile[data-tooltip="true"]';
  let tooltipEl = null;
  let moveHandler = null;

  function setVar(name, value) {
    document.documentElement.style.setProperty(name, value);
  }

  function createTooltip() {
    if (tooltipEl) return tooltipEl;
    const el = document.createElement("div");
    el.className = "ozz-tt-tooltip";
    el.setAttribute("role", "tooltip");
    document.body.appendChild(el);
    tooltipEl = el;
    return el;
  }

  function showTooltip(e, tile) {
    const el = createTooltip();
    const title = tile.getAttribute("data-tooltip-title") || "";
    const meta = tile.getAttribute("data-tooltip-meta") || "";
    el.innerHTML = `<strong>${title}</strong><br>${meta}`;
    el.classList.add("ozz-tt-visible");
    positionTooltip(e);
  }

  function hideTooltip() {
    if (!tooltipEl) return;
    tooltipEl.classList.remove("ozz-tt-visible");
  }

  function positionTooltip(e) {
    if (!tooltipEl) return;
    const padding = 12,
      margin = 8;
    const tRect = tooltipEl.getBoundingClientRect();
    const vw = window.innerWidth,
      vh = window.innerHeight;
    let left = e.clientX + padding;
    let top = e.clientY + padding;

    if (left + tRect.width + margin > vw) {
      left = e.clientX - tRect.width - padding;
    }
    if (top + tRect.height + margin > vh) {
      top = e.clientY - tRect.height - padding;
    }

    setVar("--ozz-tt-left", `${Math.round(left)}px`);
    setVar("--ozz-tt-top", `${Math.round(top)}px`);
  }

  function initDesktop() {
    const tiles = document.querySelectorAll(TILE_SELECTOR);
    if (!tiles.length) return;

    moveHandler = (ev) => requestAnimationFrame(() => positionTooltip(ev));

    tiles.forEach((tile) => {
      tile.addEventListener("mouseenter", (e) => {
        showTooltip(e, tile);
        document.addEventListener("mousemove", moveHandler);
      });
      tile.addEventListener("mouseleave", () => {
        hideTooltip();
        document.removeEventListener("mousemove", moveHandler);
      });
    });

    window.addEventListener("scroll", hideTooltip, { passive: true });
  }

  function initAll() {
    const mq = window.matchMedia("(hover: hover) and (pointer: fine)");
    if (mq.matches || !("ontouchstart" in window)) {
      initDesktop();
    }
  }

  document.addEventListener("DOMContentLoaded", initAll);
})();
