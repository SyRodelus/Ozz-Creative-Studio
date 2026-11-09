/* =========================================================
   ResponsiveMedia.js — carga inteligente de video
   ---------------------------------------------------------
   Uso (HTML):
     <div class="mi-media" data-media-video='{"sources":[
         {"src":".../Video Mobile.mp4","w":853,"h":480},
         {"src":".../Video.mp4","w":1920,"h":1080}
     ]}'>
       <img ... srcset ... >   ← poster responsive (nativo)
     </div>

   Qué resuelve frente al loader anterior:
   1. Elige la fuente según el tamaño REAL del contenedor
      (con object-fit: cover) y la densidad de píxeles, no
      solo con un breakpoint de pantalla. Ej.: una tarjeta
      de 480px en un portátil usa la versión liviana.
   2. Nunca recarga el video por eventos "resize" sin cambio
      real (en móvil la barra del navegador dispara resize al
      hacer scroll y antes reiniciaba/re-descargaba todo).
   3. Solo mejora calidad (nunca baja): si una fuente mayor
      ya se cargó, se mantiene → cero descargas repetidas.
   4. Solo descarga cuando el contenedor se acerca al
      viewport, solo reproduce lo visible y pausa el resto.
   5. Respeta "Ahorro de datos", conexiones 2G y
      prefers-reduced-motion: en esos casos queda el poster.
   6. El video aparece con fade solo cuando ya está
      reproduciendo (sin cuadros negros).

   API opcional: window.OzzMedia.observe(rootElement)
   para registrar contenedores añadidos dinámicamente.
========================================================= */

(function () {
  "use strict";

  if (window.OzzMedia) return; // evita doble inicialización

  const CONFIG = {
    selector: "[data-media-video]",
    loadMargin: "300px 0px", // empezar a descargar antes de entrar
    maxDpr: 1.5, // en video no hace falta densidad 3x
    tolerance: 1.25, // acepta hasta 25% de escalado antes de subir de fuente
    resizeDebounce: 250,
  };

  const state = new WeakMap();
  const tracked = new Set();

  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const connection =
    navigator.connection ||
    navigator.mozConnection ||
    navigator.webkitConnection;

  /* ---------------- Utilidades ---------------- */

  function isLowData() {
    if (!connection) return false;
    return (
      connection.saveData === true ||
      /(^|-)2g$/.test(connection.effectiveType || "")
    );
  }

  function videoAllowed() {
    return !reduceMotion.matches && !isLowData();
  }

  function debounce(fn, wait) {
    let t;
    return function () {
      clearTimeout(t);
      t = setTimeout(fn, wait);
    };
  }

  function parseSources(el) {
    try {
      const cfg = JSON.parse(el.getAttribute("data-media-video") || "{}");
      return (Array.isArray(cfg.sources) ? cfg.sources : [])
        .filter((s) => s && s.src)
        .map((s) => {
          const w = Number(s.w) || 1920;
          const h = Number(s.h) || Math.round((w * 9) / 16);
          return { src: s.src, w, h };
        })
        .sort((a, b) => a.w - b.w);
    } catch (err) {
      console.warn("[ResponsiveMedia] data-media-video inválido en", el, err);
      return [];
    }
  }

  /**
   * Ancho en píxeles físicos que necesita el video para cubrir
   * el contenedor (object-fit: cover).
   */
  function requiredWidth(el, source) {
    // offsetWidth/Height = tamaño de layout (ignora transform: scale de
    // las animaciones, que inflaría la medida)
    const boxW = el.offsetWidth;
    const boxH = el.offsetHeight;
    if (!boxW || !boxH) return 0;

    const ratio = source.w / source.h;
    const renderedWidth = Math.max(boxW, boxH * ratio);
    const dpr = Math.min(window.devicePixelRatio || 1, CONFIG.maxDpr);

    return renderedWidth * dpr;
  }

  /** La fuente más liviana que alcanza; si ninguna, la mayor. */
  function pickSource(el, sources) {
    for (const s of sources) {
      if (s.w * CONFIG.tolerance >= requiredWidth(el, s)) return s;
    }
    return sources[sources.length - 1];
  }

  function tryPlay(video) {
    const p = video.play();
    if (p && typeof p.catch === "function") {
      // iOS en ahorro de batería u otras políticas: se queda el poster
      p.catch(() => {});
    }
  }

  /* ---------------- Video ---------------- */

  function createVideo(el, st) {
    const v = document.createElement("video");

    // muted ANTES de asignar src para que el autoplay esté permitido
    v.muted = true;
    v.defaultMuted = true;
    v.loop = true;
    v.playsInline = true;
    v.setAttribute("muted", "");
    v.setAttribute("playsinline", "");
    v.setAttribute("loop", "");
    v.setAttribute("aria-hidden", "true");
    v.setAttribute("tabindex", "-1");
    v.disablePictureInPicture = true;
    v.preload = "auto";

    v.addEventListener("playing", () => el.classList.add("is-playing"));
    v.addEventListener("error", () => {
      el.classList.remove("is-playing");
      el.setAttribute("data-media-error", "true");
      console.warn("[ResponsiveMedia] No se pudo cargar", v.currentSrc || v.src);
    });

    el.appendChild(v);
    st.video = v;
    return v;
  }

  function applySource(el, st, source) {
    // Solo subir de calidad, nunca bajar
    if (st.current && source.w <= st.current.w) return;

    const video = st.video || createVideo(el, st);
    const resumeAt = st.current ? video.currentTime : 0;

    st.current = source;
    el.classList.remove("is-playing");
    el.setAttribute("data-media-current", String(source.w));

    video.src = source.src;

    if (resumeAt > 0) {
      video.addEventListener(
        "loadedmetadata",
        () => {
          try {
            video.currentTime = resumeAt % (video.duration || Infinity);
          } catch (e) {
            /* sin seek, no pasa nada */
          }
        },
        { once: true },
      );
    }

    if (st.visible && !document.hidden) tryPlay(video);
  }

  function activate(el) {
    const st = state.get(el);
    if (!st || st.active || !videoAllowed()) return;
    if (!st.sources.length) return;

    st.active = true;
    applySource(el, st, pickSource(el, st.sources));
    resizeObserver && resizeObserver.observe(el);
  }

  /* ---------------- Observadores ---------------- */

  // 1) Cerca del viewport → descargar
  const nearObserver =
    "IntersectionObserver" in window
      ? new IntersectionObserver(
          (entries) => {
            for (const entry of entries) {
              if (!entry.isIntersecting) continue;
              nearObserver.unobserve(entry.target);
              activate(entry.target);
            }
          },
          { rootMargin: CONFIG.loadMargin, threshold: 0 },
        )
      : null;

  // 2) Visible → reproducir / fuera → pausar
  const viewObserver =
    "IntersectionObserver" in window
      ? new IntersectionObserver(
          (entries) => {
            for (const entry of entries) {
              const st = state.get(entry.target);
              if (!st) continue;

              st.visible = entry.isIntersecting;
              if (!st.video) continue;

              if (st.visible && !document.hidden) tryPlay(st.video);
              else st.video.pause();
            }
          },
          { threshold: 0 },
        )
      : null;

  // 3) Cambio real de tamaño del contenedor → ¿hace falta más calidad?
  const pendingResize = new Set();

  const flushResize = debounce(() => {
    pendingResize.forEach((el) => {
      const st = state.get(el);
      if (st && st.active) applySource(el, st, pickSource(el, st.sources));
    });
    pendingResize.clear();
  }, CONFIG.resizeDebounce);

  const resizeObserver =
    "ResizeObserver" in window
      ? new ResizeObserver((entries) => {
          entries.forEach((e) => pendingResize.add(e.target));
          flushResize();
        })
      : null;

  /* ---------------- Registro ---------------- */

  function register(el) {
    if (state.has(el)) return;

    state.set(el, {
      sources: parseSources(el),
      current: null,
      video: null,
      visible: false,
      active: false,
    });
    tracked.add(el);

    if (nearObserver && viewObserver) {
      viewObserver.observe(el);
      nearObserver.observe(el);
    } else {
      // Navegadores muy antiguos: cargar directamente
      state.get(el).visible = true;
      activate(el);
    }
  }

  function observe(root) {
    (root || document).querySelectorAll(CONFIG.selector).forEach(register);
  }

  /* ---------------- Eventos globales ---------------- */

  // Pestaña oculta → pausar todo; al volver, reanudar lo visible
  document.addEventListener("visibilitychange", () => {
    tracked.forEach((el) => {
      const st = state.get(el);
      if (!st || !st.video) return;
      if (document.hidden) st.video.pause();
      else if (st.visible) tryPlay(st.video);
    });
  });

  // El usuario activa "reducir movimiento" en caliente
  const onMotionChange = () => {
    tracked.forEach((el) => {
      const st = state.get(el);
      if (!st) return;

      if (reduceMotion.matches) {
        if (st.video) st.video.pause();
        el.classList.remove("is-playing");
      } else if (st.video) {
        if (st.visible) tryPlay(st.video);
      } else if (st.visible) {
        activate(el);
      }
    });
  };

  if (reduceMotion.addEventListener) {
    reduceMotion.addEventListener("change", onMotionChange);
  } else if (reduceMotion.addListener) {
    reduceMotion.addListener(onMotionChange);
  }

  /* ---------------- Init ---------------- */

  window.OzzMedia = { observe };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => observe(document));
  } else {
    observe(document);
  }
})();
