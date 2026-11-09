// ========================
// LoadVideos.js
// ========================

import { sectionCache, BASE_URL } from "./Cache.js";

const MOBILE_MEDIA = "(max-width: 768px)";

/**
 * Devuelve una URL absoluta basada en BASE_URL.
 */
function absoluteUrl(path) {
  if (!path) return "";

  // Ya es absoluta
  if (
    /^https?:\/\//i.test(path) ||
    path.startsWith("//") ||
    path.startsWith("data:") ||
    path.startsWith("blob:")
  ) {
    return path;
  }

  return `${BASE_URL}/${path.replace(/^\/+/, "")}`;
}

/**
 * Obtiene el JSON de una sección.
 */
async function getSectionData(sectionId) {
  const file = absoluteUrl(`pages/index/texts/${sectionId}.json`);

  if (sectionCache.has(file)) {
    return sectionCache.get(file);
  }

  try {
    const response = await fetch(file, {
      cache: "force-cache",
    });

    if (!response.ok) {
      throw new Error(`No se pudo cargar ${file}`);
    }

    const json = await response.json();

    sectionCache.set(file, json);

    return json;
  } catch (error) {
    console.error(error);
    return null;
  }
}

/**
 * Inyecta JSON-LD para SEO.
 */
function injectVideoLD(id, meta, src) {
  if (!meta || !src) return;

  const scriptId = `video-ld-${id}`;

  if (document.getElementById(scriptId)) return;

  const data = {
    "@context": "https://schema.org",
    "@type": "VideoObject",
    name: meta.title || "",
    description: meta.description || "",
    duration: meta.duration,
    contentUrl: src,
    embedUrl: window.location.href,
    keywords: Array.isArray(meta.keywords)
      ? meta.keywords.join(", ")
      : undefined,
  };

  const script = document.createElement("script");
  script.type = "application/ld+json";
  script.id = scriptId;
  script.textContent = JSON.stringify(data);

  document.head.appendChild(script);
}

/**
 * Devuelve el video adecuado para desktop/mobile.
 */
function getVideoSrc(meta) {
  const src =
    window.matchMedia(MOBILE_MEDIA).matches && meta.mobileSrc
      ? meta.mobileSrc
      : meta.src;

  return absoluteUrl(src);
}

/**
 * Construye el HTML del video.
 */
function buildVideo(meta) {
  const src = getVideoSrc(meta);

  const aria =
    meta.title || meta.description
      ? `${meta.title ?? ""}${meta.description ? " — " + meta.description : ""}`
      : "";

  return `
    <video
      autoplay
      muted
      loop
      playsinline
      preload="none"
      aria-label="${aria.replace(/"/g, "&quot;")}"
    >
      <source src="${src}" type="video/mp4">
    </video>
  `;
}

/**
 * Carga un video.
 */
async function loadPanelVideo(panel) {
  if (panel.dataset.videoLoaded === "true") return;

  const section = panel.closest("section");
  if (!section) return;

  const data = await getSectionData(section.id);
  const meta = data?.[panel.id]?.video;

  if (!meta) return;

  const container = panel.querySelector(".panel-video");
  if (!container) return;

  container.innerHTML = buildVideo(meta);

  injectVideoLD(panel.id, meta, getVideoSrc(meta));

  panel.dataset.videoLoaded = "true";
}

/**
 * Actualiza videos al cambiar tamaño.
 */
async function updateLoadedVideos() {
  const loadedPanels = document.querySelectorAll(
    ".panel[data-video-loaded='true']",
  );

  for (const panel of loadedPanels) {
    const section = panel.closest("section");
    if (!section) continue;

    const data = await getSectionData(section.id);
    const meta = data?.[panel.id]?.video;

    if (!meta) continue;

    const container = panel.querySelector(".panel-video");
    if (!container) continue;

    container.innerHTML = buildVideo(meta);
  }
}

/**
 * Lazy loading.
 */
const videoObserver = new IntersectionObserver(
  (entries) => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue;

      loadPanelVideo(entry.target);
      videoObserver.unobserve(entry.target);
    }
  },
  {
    rootMargin: "600px 0px",
    threshold: 0,
  },
);

/**
 * Inicialización.
 */
document.querySelectorAll(".panel").forEach((panel) => {
  videoObserver.observe(panel);
});

/**
 * Cambio desktop/mobile.
 */
window.addEventListener("resize", updateLoadedVideos);
