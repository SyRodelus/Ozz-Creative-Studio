// ========================
// LoadVideos.js (Lazy load puro, sin preload)
// ========================

const CONFIG_URL = "JSON/Videos-es.json";
const MOBILE_MEDIA = "(max-width: 768px)";

let _videoConfigPromise = null;
function getVideoConfig() {
  if (!_videoConfigPromise) {
    _videoConfigPromise = fetch(CONFIG_URL, { cache: "force-cache" })
      .then((r) => r.json())
      .catch((err) => {
        console.error("[videos] No se pudo cargar videos.json:", err);
        return {};
      });
  }
  return _videoConfigPromise;
}

// JSON-LD para SEO (sin fechas ni thumbs)
function injectVideoLD(name, meta, srcUrl) {
  if (!meta || !srcUrl) return;
  const ldId = `ld-video-${name}`;
  if (document.getElementById(ldId)) return;

  const data = {
    "@context": "https://schema.org",
    "@type": "VideoObject",
    name: meta.title || name,
    description: meta.description || "",
    duration: meta.duration || undefined,
    contentUrl: srcUrl,
    embedUrl: typeof window !== "undefined" ? window.location.href : undefined,
    keywords: Array.isArray(meta.keywords)
      ? meta.keywords.join(", ")
      : undefined,
  };

  const script = document.createElement("script");
  script.type = "application/ld+json";
  script.id = ldId;
  script.textContent = JSON.stringify(data, null, 2);
  document.head.appendChild(script);
}

function buildVideoHTML(meta, src) {
  const aria =
    meta.title || meta.description
      ? ` aria-label="${(meta.title || "").replace(/"/g, "&quot;")}${
          meta.description
            ? " — " + meta.description.replace(/"/g, "&quot;")
            : ""
        }"`
      : "";

  // preload="none" para no cargar nada hasta que se inserte en viewport (lazy real)
  return `
    <video${aria} autoplay muted loop playsinline preload="none">
      <source src="${src}" type="video/mp4">
      Tu navegador no soporta video en HTML5.
    </video>
  `;
}

function insertVideo(container, config) {
  const isMobile = window.matchMedia(MOBILE_MEDIA).matches;
  const name = container.getAttribute("data-video");
  if (!name || !config) return;

  const meta = config[name];
  if (!meta) {
    console.warn(`[videos] No hay metadata para "${name}" en videos.json`);
    return;
  }

  const chosenSrc = isMobile && meta.mobileSrc ? meta.mobileSrc : meta.src;
  if (!chosenSrc) return;

  // Evita reinsertar el mismo source
  const current = container.querySelector("source");
  if (current && current.getAttribute("src") === chosenSrc) return;

  if (meta.title) container.setAttribute("data-video-title", meta.title);
  if (meta.keywords && meta.keywords.length)
    container.setAttribute("data-video-keywords", meta.keywords.join(","));

  container.innerHTML = buildVideoHTML(meta, chosenSrc);

  // Solo SEO; no preloads
  injectVideoLD(name, meta, chosenSrc);
}

// Lazy load: cargar cuando el usuario se va acercando
const observer = new IntersectionObserver(
  async (entries, obs) => {
    const config = await getVideoConfig();
    for (const entry of entries) {
      if (entry.isIntersecting) {
        insertVideo(entry.target, config);
        obs.unobserve(entry.target);
      }
    }
  },
  // rootMargin amplio para “cargar a medida que se acerca”
  { rootMargin: "1000px 0px 1000px 0px", threshold: 0 }
);

// Observar contenedores
document
  .querySelectorAll("[data-video]")
  .forEach((container) => observer.observe(container));

// Re-evaluar mobile/desktop al redimensionar
window.addEventListener("resize", async () => {
  const config = await getVideoConfig();
  document.querySelectorAll("[data-video]").forEach((container) => {
    if (container.querySelector("video")) {
      insertVideo(container, config);
    }
  });
});
