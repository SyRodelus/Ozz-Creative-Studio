// ========================
// 1) Configuración
// ========================
const PROJECT_DIR = "Xero Minded";
const PROJECT_BASE = `../pages/Work/${encodeURIComponent(PROJECT_DIR)}`;
const MOBILE_BREAKPOINT = "(max-width: 768px)";

// Construye la ruta según el índice y el modo (mobile/desktop)
function buildSrcFromKey(key, isMobile) {
  if (!key) return null;
  return isMobile
    ? `${PROJECT_BASE}/Mobile/${key}-mobile.webp`
    : `${PROJECT_BASE}/Pc/${key}.webp`;
}

// ========================
// 2) Helpers
// ========================
const mq = window.matchMedia(MOBILE_BREAKPOINT);

function isMobileNow() {
  return mq.matches;
}

function getResponsiveSrc(key) {
  return buildSrcFromKey(key, isMobileNow());
}

function insertOrUpdateImage(container) {
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

    // Carga inmediata (sin lazy nativo), control con IO/preload
    img.removeAttribute("loading");
    img.decoding = "async";

    container.innerHTML = "";
    container.appendChild(img);
  } else {
    img.removeAttribute("loading");
  }

  // Fallback: intenta desktop/mobile alterno si falla el primero
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
      img.onerror = () => {
        container.setAttribute("data-load-error", "true");
      };
    } else {
      container.setAttribute("data-load-error", "true");
    }
  };

  if (!primaryTried) {
    container.setAttribute("data-load-error", "true");
  }
}

// ========================
// 3) IntersectionObserver como PRELOAD (±400px)
// ========================
const imageObserver = new IntersectionObserver(
  (entries, obs) => {
    entries.forEach((entry) => {
      // Se considera "intersectando" al entrar en el buffer de ±400px
      if (entry.isIntersecting) {
        insertOrUpdateImage(entry.target); // pre-carga antes del viewport
        obs.unobserve(entry.target);
      }
    });
  },
  {
    root: null,
    rootMargin: "400px 0px 400px 0px", // máximo 400px solicitado
    threshold: 0,
  },
);

// ========================
// 4) Insertar imágenes entre videos según data-position
//    REGLA (actualizado):
//    - Solo se consideran videos con .video-process
//    - La posición se lee del wrapper .media-item[data-position]
//    - imagen i -> se mueve el wrapper del video con data-position=i justo DESPUÉS
// ========================
function insertImagesBetweenVideos(imageCount) {
  const mediaGrid = document.querySelector(".media-grid");
  if (!mediaGrid) return;

  // 1) Tomamos SOLO videos .video-process (estén o no envueltos, pero esperamos wrapper .media-item)
  const videos = Array.from(mediaGrid.querySelectorAll(".video-process"));

  // Mapa: video (.video-process) -> su wrapper directo en la grid (si existe)
  const videoWrapper = new Map(
    videos.map((v) => [v, v.closest(".media-item") || v]),
  );

  // Helper para leer posición: SIEMPRE desde el wrapper .media-item[data-position]
  function getVideoPositionFromWrapper(el) {
    const wrap = videoWrapper.get(el);
    if (!wrap) return null;
    const posStr = (wrap.getAttribute("data-position") || "").trim();
    const pos = parseInt(posStr, 10);
    return Number.isFinite(pos) ? pos : null;
  }

  // 2) Limpiar solo lo que insertamos nosotros previamente (no borrar videos)
  mediaGrid.querySelectorAll("[data-image]").forEach((n) => n.remove());

  // 3) Separar videos con y sin posición (usando el helper)
  const videosWithoutPosition = videos.filter(
    (v) => getVideoPositionFromWrapper(v) == null,
  );
  const videosWithPosition = videos
    .filter((v) => getVideoPositionFromWrapper(v) != null)
    .sort(
      (a, b) => getVideoPositionFromWrapper(a) - getVideoPositionFromWrapper(b),
    );

  // 4) Punto de inserción inicial: tras el último video SIN posición (si existen)
  let insertAfterNode = null;
  if (videosWithoutPosition.length > 0) {
    const lastNoPos = videosWithoutPosition[videosWithoutPosition.length - 1];
    insertAfterNode = videoWrapper.get(lastNoPos);
  } else if (mediaGrid.firstElementChild) {
    insertAfterNode = mediaGrid.firstElementChild;
  }

  // 5) Insertar imágenes 1..N en orden
  for (let i = 1; i <= imageCount; i++) {
    // crear contenedor de imagen
    const imgDiv = document.createElement("div");
    imgDiv.className = "media-item";
    imgDiv.setAttribute("data-image", String(i));

    // placeholder mínimo para que IO detecte intersección (evita altura 0)
    imgDiv.style.minHeight = "1px";
    // (opcional para evitar layout shift)
    // imgDiv.style.aspectRatio = "4 / 5";

    // colocar la imagen en la grilla
    if (insertAfterNode && insertAfterNode.parentNode === mediaGrid) {
      insertAfterNode.after(imgDiv);
    } else {
      mediaGrid.appendChild(imgDiv);
    }
    insertAfterNode = imgDiv; // avanzar el cursor

    // 6) Si existe un video con data-position = i (en su WRAPPER), moverlo justo DESPUÉS
    const videoForPos = videosWithPosition.find(
      (v) => getVideoPositionFromWrapper(v) === i,
    );
    if (videoForPos) {
      const wrap = videoWrapper.get(videoForPos);
      // Mover el wrapper del video a justo después de imgDiv
      if (wrap && wrap !== imgDiv && wrap.parentNode === mediaGrid) {
        mediaGrid.insertBefore(wrap, imgDiv.nextSibling);
        insertAfterNode = wrap; // el cursor ahora queda después del video
      }
    }
  }
}

// ========================
// 5) Observar media items para PRELOAD (sin lazy nativo)
// ========================
function observeMediaItems() {
  const items = document.querySelectorAll("[data-image]");
  if (!("IntersectionObserver" in window)) {
    // Fallback: si no hay IO, cargamos todo de una
    items.forEach((el) => insertOrUpdateImage(el));
    return;
  }
  items.forEach((el) => {
    el.removeAttribute("data-current-src");
    el.removeAttribute("data-load-error");
    imageObserver.observe(el); // se cargará al entrar en el rootMargin
  });
}

// ========================
// 6) Precarga proactiva de items cercanos (±400px)
// ========================
function primeNearViewportLoad() {
  const VH = window.innerHeight || document.documentElement.clientHeight || 0;
  document.querySelectorAll("[data-image]").forEach((el) => {
    const r = el.getBoundingClientRect();
    if (r.top <= VH + 800 && r.bottom >= -800) {
      // Está cerca: cargar ya
      imageObserver.unobserve(el);
      insertOrUpdateImage(el);
    }
  });
}

// Re-evaluar al hacer scroll/resize por si aparecen nuevos cerca
let pvRaf = null;
["scroll", "resize"].forEach((ev) =>
  window.addEventListener(ev, () => {
    if (pvRaf) cancelAnimationFrame(pvRaf);
    pvRaf = requestAnimationFrame(primeNearViewportLoad);
  }),
);

// ========================
// 7) Actualizar imágenes responsive al cambiar breakpoint o resize
// ========================
mq.addEventListener("change", () => {
  document.querySelectorAll("[data-image]").forEach((container) => {
    const img = container.querySelector("img[data-responsive]");
    if (img) {
      container.removeAttribute("data-current-src");
      insertOrUpdateImage(container);
    }
  });
});

let resizeRaf = null;
window.addEventListener("resize", () => {
  if (resizeRaf) cancelAnimationFrame(resizeRaf);
  resizeRaf = requestAnimationFrame(() => {
    document.querySelectorAll("[data-image]").forEach((container) => {
      const img = container.querySelector("img[data-responsive]");
      if (img) {
        container.removeAttribute("data-current-src");
        insertOrUpdateImage(container);
      }
    });
  });
});

// ========================
// 8) Inicializar todo al DOMContentLoaded
// ========================
document.addEventListener("DOMContentLoaded", () => {
  const IMAGE_COUNT = 10; // Ajusta al número real que tengas
  insertImagesBetweenVideos(IMAGE_COUNT);
  observeMediaItems();
  primeNearViewportLoad(); // precarga de los cercanos a ±400px
});
