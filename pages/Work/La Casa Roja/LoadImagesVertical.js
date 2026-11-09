(function () {
  // ========================
  // 1) Configuración
  // ========================
  const PROJECT_DIR = "La Casa Roja";
  const PROJECT_BASE = `../pages/Work/${encodeURIComponent(PROJECT_DIR)}`;
  const MOBILE_BREAKPOINT = "(max-width: 768px)";

  function buildSrcFromNumber(num, isMobile, isVertical) {
    if (num == null) return null;
    const key = String(num);
    if (isVertical) {
      return isMobile
        ? `${PROJECT_BASE}/Mobile-Vertical/${key}-mobile.webp`
        : `${PROJECT_BASE}/Pc-Vertical/${key}.webp`;
    } else {
      return isMobile
        ? `${PROJECT_BASE}/Mobile/${key}-mobile.webp`
        : `${PROJECT_BASE}/Pc/${key}.webp`;
    }
  }

  const mq = window.matchMedia(MOBILE_BREAKPOINT);
  function isMobileNow() {
    return mq.matches;
  }

  function containerIsMarkedVertical(el) {
    if (!el) return false;
    if (el.classList && el.classList.contains("vertical")) return true;
    try {
      return (
        el.getAttribute && el.getAttribute("data-orientation") === "vertical"
      );
    } catch (e) {
      return false;
    }
  }

  // ========================
  // 2) Crear wrapper de 3 imágenes (SOLO ESTRUCTURA, CERO ESTILOS DE LAYOUT)
  // ========================
  function createThreeImageWrapper(startNumber, altPrefix) {
    const wrapper = document.createElement("div");
    wrapper.className = "vertical-images-wrapper";
    // *** ESTILOS DE LAYOUT ELIMINADOS ***

    for (let i = 0; i < 3; i++) {
      const num = startNumber + i;
      const img = document.createElement("img");
      img.setAttribute("data-responsive", "true");
      img.dataset.pendingNum = String(num);
      img.alt = altPrefix ? `${altPrefix} ${num}` : `Image ${num}`;
      img.decoding = "async";
      // Añadimos solo estilos esenciales que no son de layout, si es necesario,
      // pero mantenemos object-fit y display block, que son seguros:
      img.style.objectFit = "cover";
      img.style.display = "block";
      // *** ELIMINADAS: width, height, flex, flex-direction ***

      wrapper.appendChild(img);
    }

    return wrapper;
  }

  // ========================
  // 3) Asignar bloques verticales EN ORDEN DEL DOM (contador global)
  //    - Cada media-item vertical recibe su bloque secuencial: 1..3, 4..6, 7..9, ...
  //    - Conserva attributes data-block-start / end y data-group-occurrence
  // ========================
  function assignBlocksForVerticals() {
    // Seleccionamos únicamente los contenedores que son verticales y que están en el DOM,
    // respetando el orden natural del DOM.
    const verticalContainers = Array.from(
      document.querySelectorAll(
        ".media-item.vertical, .media-item[data-orientation='vertical']",
      ),
    );

    if (!verticalContainers.length) {
      updateHasVerticalClass();
      return;
    }

    // contador global: 1, 2, 3...
    let globalindex = 0;

    verticalContainers.forEach((container) => {
      globalindex += 1;
      const occ = globalindex;
      const startNumber = (occ - 1) * 3 + 1;

      // asignamos atributos (útiles para debug / responsive)
      container.setAttribute("data-group-occurrence", String(occ));
      container.setAttribute("data-block-start", String(startNumber));
      container.setAttribute("data-block-end", String(startNumber + 2));

      // Si no existe wrapper, lo creamos con los pending nums correctos
      const exists = container.querySelector(".vertical-images-wrapper");
      if (!exists) {
        container.innerHTML = "";
        const wrapper = createThreeImageWrapper(
          startNumber,
          container.getAttribute("data-alt") || null,
        );
        container.appendChild(wrapper);
        container.setAttribute("data-loaded", "false");
      } else {
        // Si ya existía, actualizamos los data-pending-num por si cambió startNumber
        const imgs = container.querySelectorAll("img[data-responsive]");
        if (imgs && imgs.length > 0) {
          imgs.forEach((img, idx) => {
            img.dataset.pendingNum = String(startNumber + idx);
            img.removeAttribute("src");
          });
          container.setAttribute("data-loaded", "false");
        }
      }
    });

    // actualizar fallback .has-vertical si aplica
    updateHasVerticalClass();
  }

  // ========================
  // 4) Setear src en imgs pendientes dentro de un container vertical
  // ========================
  function setSrcForPendingImagesInContainer(container) {
    if (!container) return;
    const imgs = Array.from(
      container.querySelectorAll("img[data-pending-num]"),
    );
    if (!imgs.length) {
      container.setAttribute("data-loaded", "true");
      return;
    }

    imgs.forEach((img) => {
      const num = parseInt(img.dataset.pendingNum, 10);
      if (!num) return;
      const primary = buildSrcFromNumber(num, isMobileNow(), true);
      const tried = new Set();

      function trySet(src) {
        if (!src || tried.has(src)) return false;
        tried.add(src);
        img.src = src;
        return true;
      }

      img.onerror = null;
      const ok = trySet(primary);

      img.onerror = () => {
        const alt = buildSrcFromNumber(num, !isMobileNow(), true);
        if (trySet(alt)) {
          img.onerror = () => container.setAttribute("data-load-error", "true");
        } else {
          container.setAttribute("data-load-error", "true");
        }
      };

      delete img.dataset.pendingNum;
    });

    container.setAttribute("data-loaded", "true");
  }

  // ========================
  // 5) IntersectionObserver vertical (preload ±400px)
  // ========================
  const verticalObserver = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        if (containerIsMarkedVertical(el)) {
          // prepare wrapper if missing (in case new vertical was added dynamically)
          if (!el.querySelector(".vertical-images-wrapper"))
            assignBlocksForVerticals();
          setSrcForPendingImagesInContainer(el);
        }
        obs.unobserve(el);
      });
    },
    { root: null, rootMargin: "400px 0px 400px 0px", threshold: 0 },
  );

  // ========================
  // 6) Observe vertical media-items only
  // ========================
  function observeVerticalItems() {
    const items = Array.from(document.querySelectorAll("[data-image]")).filter(
      containerIsMarkedVertical,
    );
    if (!("IntersectionObserver" in window)) {
      // fallback: load everything immediately
      items.forEach((el) => {
        if (!el.querySelector(".vertical-images-wrapper"))
          assignBlocksForVerticals();
        setSrcForPendingImagesInContainer(el);
      });
      return;
    }
    items.forEach((el) => {
      el.removeAttribute("data-loaded");
      verticalObserver.observe(el);
    });
  }

  // ========================
  // 7) Prime near viewport for verticals
  // ========================
  function primeVerticalsNearViewport() {
    const VH = window.innerHeight || document.documentElement.clientHeight || 0;
    document.querySelectorAll("[data-image]").forEach((el) => {
      if (!containerIsMarkedVertical(el)) return;
      const r = el.getBoundingClientRect();
      if (r.top <= VH + 800 && r.bottom >= -800) {
        verticalObserver.unobserve(el);
        if (!el.querySelector(".vertical-images-wrapper"))
          assignBlocksForVerticals();
        setSrcForPendingImagesInContainer(el);
      }
    });
  }

  // ========================
  // 8) Fallback .has-vertical (para navegadores sin :has())
  // ========================
  function supportsHasSelector() {
    try {
      return CSS && CSS.supports && CSS.supports("selector(:has(*))");
    } catch (e) {
      return false;
    }
  }

  function updateHasVerticalClass() {
    const grid = document.querySelector(".media-grid");
    if (!grid) return;
    const hasVertical = !!grid.querySelector(
      '.media-item.vertical, .media-item[data-orientation="vertical"]',
    );
    if (hasVertical) grid.classList.add("has-vertical");
    else grid.classList.remove("has-vertical");
  }

  function initHasVerticalFallback() {
    if (supportsHasSelector()) return;
    updateHasVerticalClass();
    const grid = document.querySelector(".media-grid");
    if (!grid) return;
    const mo = new MutationObserver(() => updateHasVerticalClass());
    mo.observe(grid, {
      childList: true,
      subtree: true,
      attributes: true,
      attributeFilter: ["class", "data-orientation", "data-image"],
    });
  }

  // ========================
  // 9) Breakpoint change handling (verticals)
  // ========================
  mq.addEventListener("change", () => {
    // reassign pending nums for verticals
    document.querySelectorAll("[data-image]").forEach((container) => {
      if (!containerIsMarkedVertical(container)) return;
      const start = parseInt(container.getAttribute("data-block-start"), 10);
      if (Number.isFinite(start)) {
        const imgs = container.querySelectorAll("img[data-responsive]");
        if (imgs && imgs.length) {
          imgs.forEach((img, idx) => {
            img.dataset.pendingNum = String(start + idx);
            img.removeAttribute("src");
          });
          container.setAttribute("data-loaded", "false");
        }
      } else {
        assignBlocksForVerticals();
      }
    });
  });

  // ========================
  // 10) Inicializar
  // ========================
  document.addEventListener("DOMContentLoaded", () => {
    initHasVerticalFallback();
    assignBlocksForVerticals();
    observeVerticalItems();
    primeVerticalsNearViewport();

    // re-eval on scroll/resize
    let rafId = null;
    ["scroll", "resize"].forEach((ev) => {
      window.addEventListener(ev, () => {
        if (rafId) cancelAnimationFrame(rafId);
        rafId = requestAnimationFrame(primeVerticalsNearViewport);
      });
    });
  });
})();
