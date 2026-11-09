(function () {
  const preloader = document.getElementById("preloader");
  const textEl = document.getElementById("pl-text");

  if (!preloader || !textEl) return;

  /* =============================
     SI YA SE MOSTRÓ ESTA SESIÓN
  ============================== */

  if (sessionStorage.getItem("preloaderShown")) {
    preloader.remove();
    return;
  }

  /* =============================
     BLOQUEO VISUAL GLOBAL
  ============================== */

  document.body.classList.add("has-preloader");

  /* =============================
     CALCULAR SCROLLBAR (FALLBACK)
  ============================== */

  const scrollbarWidth =
    window.innerWidth - document.documentElement.clientWidth;

  document.documentElement.style.setProperty(
    "--scrollbar-width",
    `${scrollbarWidth}px`,
  );

  /* =============================
     BLOQUEAR SCROLL
  ============================== */

  document.documentElement.classList.add("preloader-active");
  document.body.classList.add("preloader-active");

  /* =============================
     CONFIG
  ============================== */

  const visibleTime = 1500;
  const curtainDelay = 50;
  const fadeDuration = 500;

  let phrases = [];
  let idx = 0;
  let minTimePassed = false;
  let pageLoaded = false;

  /* =============================
     IDIOMA
  ============================== */

  const lang = document.documentElement.lang === "es" ? "es" : "en";

  /* =============================
     CARGAR TEXTO
  ============================== */

  fetch("pages/index/preloader-texts.json")
    .then((res) => res.json())
    .then((data) => {
      phrases = data[lang] || data.en;
      startPreloader();
    })
    .catch(() => {
      phrases = ["OZZ"];
      startPreloader();
    });

  /* =============================
     RENDER
  ============================== */

  function renderPhrase(raw) {
    textEl.innerHTML = raw.replace(/\bOZZ\b/g, '<span id="pl-ozz">OZZ</span>');
  }

  /* =============================
     FLOW
  ============================== */

  function startPreloader() {
    const lastindex = phrases.length - 1;

    renderPhrase(phrases[0]);

    setTimeout(() => {
      minTimePassed = true;
      tryHide();
    }, visibleTime * phrases.length);

    function next() {
      if (idx >= lastindex) return;

      idx++;
      renderPhrase(phrases[idx]);

      if (idx === lastindex) {
        preloader.classList.add("last");

        setTimeout(() => {
          preloader.classList.add("curtain");
        }, curtainDelay);

        return;
      }

      setTimeout(next, visibleTime);
    }

    setTimeout(next, visibleTime);
  }

  /* =============================
     SALIDA
  ============================== */

  function tryHide() {
    if (pageLoaded && minTimePassed) hidePreloader();
  }

  function hidePreloader() {
    if (preloader.classList.contains("fade-out")) return;

    /* inicia fade */
    preloader.classList.add("fade-out");

    /* 🔓 LIBERAR REVEAL EN ESTE MISMO FRAME */
    document.body.classList.remove("has-preloader");

    /* 🔔 REVEAL INSTANTÁNEO */
    window.dispatchEvent(new Event("preloader:done"));

    /* cleanup visual */
    setTimeout(() => {
      preloader.remove();

      document.documentElement.classList.remove("preloader-active");
      document.body.classList.remove("preloader-active");

      sessionStorage.setItem("preloaderShown", "true");
    }, fadeDuration);
  }

  if (document.readyState === "complete") {
    pageLoaded = true;
    tryHide();
  } else {
    window.addEventListener(
      "load",
      () => {
        pageLoaded = true;
        tryHide();
      },
      { once: true },
    );
  }
})();
