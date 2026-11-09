// LoadImages.js
(function () {
  // ========================
  // 1) Configuración
  // ========================
  const PROJECT_DIR = "La Pileta";
  const PROJECT_BASE = `../pages/Work/${encodeURIComponent(PROJECT_DIR)}`;
  const MOBILE_BREAKPOINT = "(max-width: 768px)";

  function buildSrcFromKey(key, isMobile) {
    if (!key) return null;
    return isMobile
      ? `${PROJECT_BASE}/Mobile/${key}-mobile.webp`
      : `${PROJECT_BASE}/Pc/${key}.webp`;
  }

  function buildSrcFromNumber(num, isMobile, isVertical) {
    if (num == null) return null;
    const key = String(num);
    if (isVertical) {
      return isMobile
        ? `${PROJECT_BASE}/Mobile-Vertical/${key}-mobile.webp`
        : `${PROJECT_BASE}/Pc-Vertical/${key}.webp`;
    } else {
      return buildSrcFromKey(key, isMobile);
    }
  }

  // ========================
  // 2) Helpers (no-vertical)
  // ========================
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

  function getResponsiveSrc(key) {
    return buildSrcFromKey(key, isMobileNow());
  }

  // ========================
  // 3) Insert / update single (solo no-verticales)
  // ========================
  function insertOrUpdateImage(container) {
    if (!container || containerIsMarkedVertical(container)) return; // no tocar verticales

    const key = container.getAttribute("data-image");
    if (!key) return;

    const primarySrc = getResponsiveSrc(key);
    if (!primarySrc) return;

    const current = container.getAttribute("data-current-src");
    if (current === primarySrc) return;

    container.setAttribute("data-current-src", primarySrc);

    let img = container.querySelector("img[data-responsive]");
    if (!img) {
      img = document.createElement("img");
      img.setAttribute("data-responsive", "true");
      img.alt = container.getAttribute("data-alt") || `Image ${key}`;
      img.decoding = "async";
      img.removeAttribute("loading");
      container.innerHTML = "";
      container.appendChild(img);
    } else {
      img.removeAttribute("loading");
    }

    const tried = new Set();
    function trySetSrc(nextSrc) {
      if (!nextSrc || tried.has(nextSrc)) return false;
      tried.add(nextSrc);
      img.src = nextSrc;
      return true;
    }

    img.onerror = null;
    const primaryTried = trySetSrc(primarySrc);

    img.onerror = () => {
      const altSrc = buildSrcFromKey(key, !isMobileNow());
      if (trySetSrc(altSrc)) {
        img.onerror = () => container.setAttribute("data-load-error", "true");
      } else {
        container.setAttribute("data-load-error", "true");
      }
    };

    if (!primaryTried) container.setAttribute("data-load-error", "true");
  }

  // ========================
  // 4) IntersectionObserver para NO-verticales (preload ±600px)
  // ========================
  const nonVerticalObserver = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        if (!containerIsMarkedVertical(el)) insertOrUpdateImage(el);
        obs.unobserve(el);
      });
    },
    { root: null, rootMargin: "600px 0px 600px 0px", threshold: 0 },
  );

  // ========================
  // 5) Insertar placeholders de imágenes entre videos
  //    (respeta elementos manuales y no altera verticales)
  // ========================
  function insertImagesBetweenVideos(imageCount) {
    const mediaGrid = document.querySelector(".media-grid");
    if (!mediaGrid) return;

    // limpiamos solo lo generado por este script
    mediaGrid
      .querySelectorAll('[data-image][data-generated="true"]')
      .forEach((n) => n.remove());

    const videos = Array.from(mediaGrid.querySelectorAll(".video-process"));
    const videoWrapper = new Map(
      videos.map((v) => [v, v.closest(".media-item") || v]),
    );

    function getVideoPositionFromWrapper(el) {
      const wrap = videoWrapper.get(el);
      if (!wrap) return null;
      const posStr = (wrap.getAttribute("data-position") || "").trim();
      const pos = parseInt(posStr, 10);
      return Number.isFinite(pos) ? pos : null;
    }

    const videosWithoutPosition = videos.filter(
      (v) => getVideoPositionFromWrapper(v) == null,
    );
    const videosWithPosition = videos
      .filter((v) => getVideoPositionFromWrapper(v) != null)
      .sort(
        (a, b) =>
          getVideoPositionFromWrapper(a) - getVideoPositionFromWrapper(b),
      );

    let insertAfterNode = null;
    if (videosWithoutPosition.length > 0) {
      const lastNoPos = videosWithoutPosition[videosWithoutPosition.length - 1];
      insertAfterNode = videoWrapper.get(lastNoPos);
    } else {
      insertAfterNode = mediaGrid.firstElementChild;
    }

    for (let i = 1; i <= imageCount; i++) {
      const imgDiv = document.createElement("div");
      imgDiv.className = "media-item";
      imgDiv.setAttribute("data-image", String(i));
      imgDiv.setAttribute("data-generated", "true");
      imgDiv.style.minHeight = "auto";

      // If manual (non-generated) exist with same data-image, insert after last manual
      const existingManual = Array.from(
        mediaGrid.querySelectorAll(
          `[data-image="${i}"]:not([data-generated="true"])`,
        ),
      );
      if (existingManual.length > 0) {
        existingManual[existingManual.length - 1].after(imgDiv);
        insertAfterNode = imgDiv;
      } else if (insertAfterNode && insertAfterNode.parentNode === mediaGrid) {
        insertAfterNode.after(imgDiv);
        insertAfterNode = imgDiv;
      } else {
        mediaGrid.appendChild(imgDiv);
        insertAfterNode = imgDiv;
      }

      // if a video wrapper had data-position == i, move it after the imgDiv
      const videoForPos = videosWithPosition.find(
        (v) => getVideoPositionFromWrapper(v) === i,
      );
      if (videoForPos) {
        const wrap = videoWrapper.get(videoForPos);
        if (wrap && wrap !== imgDiv && wrap.parentNode === mediaGrid) {
          mediaGrid.insertBefore(wrap, imgDiv.nextSibling);
          insertAfterNode = wrap;
        }
      }
    }
  }

  // ========================
  // 6) Observe items (only non-vertical)
  // ========================
  function observeMediaItems() {
    const items = Array.from(document.querySelectorAll("[data-image]"));
    items.forEach((el) => {
      el.removeAttribute("data-current-src");
      el.removeAttribute("data-load-error");
      if (!containerIsMarkedVertical(el)) nonVerticalObserver.observe(el);
    });
  }

  // ========================
  // 7) Prime near viewport (only non-vertical)
  // ========================
  function primeNearViewportLoad() {
    const VH = window.innerHeight || document.documentElement.clientHeight || 0;
    document.querySelectorAll("[data-image]").forEach((el) => {
      if (containerIsMarkedVertical(el)) return;
      const r = el.getBoundingClientRect();
      if (r.top <= VH + 800 && r.bottom >= -800) {
        nonVerticalObserver.unobserve(el);
        insertOrUpdateImage(el);
      }
    });
  }

  // ========================
  // 8) Responsive: update images on breakpoint change
  // ========================
  mq.addEventListener("change", () => {
    document.querySelectorAll("[data-image]").forEach((container) => {
      if (containerIsMarkedVertical(container)) return;
      const img = container.querySelector("img[data-responsive]");
      if (img) {
        container.removeAttribute("data-current-src");
        insertOrUpdateImage(container);
      }
    });
  });

  // ========================
  // 9) Inicializar
  // ========================
  document.addEventListener("DOMContentLoaded", () => {
    const IMAGE_COUNT = 3; // placeholders for general images (no-vertical)
    insertImagesBetweenVideos(IMAGE_COUNT);
    observeMediaItems();
    primeNearViewportLoad();

    // re-run prime on scroll/resize cheaply
    let rafId = null;
    ["scroll", "resize"].forEach((ev) => {
      window.addEventListener(ev, () => {
        if (rafId) cancelAnimationFrame(rafId);
        rafId = requestAnimationFrame(primeNearViewportLoad);
      });
    });
  });
})();
