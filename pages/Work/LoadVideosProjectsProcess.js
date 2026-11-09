// ===============================
// LoadVideosProjectsProcess.js
// (NO crea wrappers ni reordena: solo rellena placeholders .video-process)
// ===============================

const CONFIG_URL_PROCESS = "../JSON/Videos.projects.process.json";
const MOBILE_MEDIA_PROCESS = "(max-width: 768px)";
const ROOT_MARGIN_PROCESS = "800px 0px 800px 0px";

const mqProcess = window.matchMedia(MOBILE_MEDIA_PROCESS);

let _videoConfigPromiseProcess = null;
function getVideoConfigProcess() {
  if (!_videoConfigPromiseProcess) {
    _videoConfigPromiseProcess = fetch(CONFIG_URL_PROCESS, {
      cache: "force-cache",
    })
      .then((r) => r.json())
      .catch((err) => {
        console.error(
          "[videos-process] Cannot load videos.projects.process.json:",
          err
        );
        return {};
      });
  }
  return _videoConfigPromiseProcess;
}

function isMobileNowProcess() {
  return mqProcess.matches;
}

// ---------- SEO (JSON-LD VideoObject) ----------
function injectVideoLDProcess(name, meta, srcUrl) {
  if (!meta || !srcUrl) return;
  const ldId = `ld-video-process-${name}`;
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

// ---------- Construcción del <video> ----------
function buildVideoHTMLProcess(meta, src) {
  const aria =
    meta.title || meta.description
      ? ` aria-label="${(meta.title || "").replace(/"/g, "&quot;")}${
          meta.description
            ? " — " + meta.description.replace(/"/g, "&quot;")
            : ""
        }"`
      : "";
  return `
    <video${aria} class="video-el-process" autoplay muted loop playsinline preload="none">
      <source src="${src}" type="video/mp4">
      Your browser does not support HTML5 video.
    </video>
  `;
}

// ---------- Montar/Actualizar dentro del placeholder ----------
function mountOrUpdateVideoProcess(wrapper, meta) {
  const chosenSrc =
    isMobileNowProcess() && meta.mobileSrc ? meta.mobileSrc : meta.src;
  if (!chosenSrc) return;

  const current = wrapper.querySelector("source");
  if (current && current.getAttribute("src") === chosenSrc) return;

  wrapper.innerHTML = buildVideoHTMLProcess(meta, chosenSrc);
  injectVideoLDProcess(
    wrapper.getAttribute("data-video-process") || "",
    meta,
    chosenSrc
  );
}

// ---------- IO para lazy load de placeholders ----------
const videoObserverProcess = new IntersectionObserver(
  (entries, obs) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const wrapper = entry.target;
      const key = wrapper.getAttribute("data-video-process");
      if (!key) return;

      getVideoConfigProcess().then((config) => {
        const meta = config[key];
        if (!meta) return;
        mountOrUpdateVideoProcess(wrapper, meta);
        obs.unobserve(wrapper);
      });
    });
  },
  { root: null, rootMargin: ROOT_MARGIN_PROCESS, threshold: 0 }
);

// ---------- Inicialización: enganchar solo placeholders existentes ----------
function initProcessPlaceholders() {
  const mediaGrid = document.querySelector(".media-grid");
  if (!mediaGrid) return;

  // Solo los placeholders pre-existentes en el HTML
  const placeholders = mediaGrid.querySelectorAll(
    ".video-process[data-video-process]"
  );
  placeholders.forEach((el) => videoObserverProcess.observe(el));
}

// ---------- Re-montar visibles en resize/breakpoint ----------
function remountVisibleVideosOnResizeProcess() {
  const mediaGrid = document.querySelector(".media-grid");
  if (!mediaGrid) return;

  const wrappers = mediaGrid.querySelectorAll(".video-process");
  getVideoConfigProcess().then((config) => {
    wrappers.forEach((w) => {
      const key = w.getAttribute("data-video-process");
      const meta = config[key];
      if (!meta) return;
      if (w.querySelector("video")) {
        mountOrUpdateVideoProcess(w, meta);
      }
    });
  });
}

mqProcess.addEventListener("change", remountVisibleVideosOnResizeProcess);
window.addEventListener("resize", () => {
  remountVisibleVideosOnResizeProcess();
});

// ---------- DOM Ready ----------
document.addEventListener("DOMContentLoaded", () => {
  initProcessPlaceholders();
});
