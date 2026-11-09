/* =========================================================
   LangSwitch.js — cambio de idioma SIN recargar la página
   ---------------------------------------------------------
   El servidor renderiza el idioma actual (bueno para SEO)
   y deja la traducción en atributos:
     data-alt="Texto en el otro idioma"        → textContent
     data-alt-{atributo}="valor"               → atributo
       (href, aria-label, alt, title, placeholder, content,
        value, data-label…)
   Al pulsar el botón [data-lang-switch]:
     1. Intercambia cada texto/atributo por su traducción
        (y guarda el actual, así se puede volver).
     2. Cambia <html lang>, el <title> y la meta description.
     3. Actualiza la URL (index.php ↔ index-es.php) con
        history.replaceState: recargar o compartir mantiene
        el idioma elegido.
     4. Emite el evento "ozz:langchange" { detail: { lang } }
        para scripts con textos dinámicos.
   Si la página no está unificada (no carga este script),
   el botón funciona como un enlace normal.
========================================================= */

(function () {
  "use strict";

  const ATTRS = [
    "href",
    "aria-label",
    "alt",
    "title",
    "placeholder",
    "content",
    "value",
    "data-label",
    "data-text",
    "data-tooltip",
    "data-tooltip-title",
    "data-tooltip-meta",
    "data-title",
    "data-description",
    "hreflang",
  ];

  function swapText(el) {
    // Los textos con animación de líneas (reveal.js) viven en un span interno
    const target =
      (el.classList.contains("reveal-lines") &&
        el.querySelector(":scope > .reveal-line > span")) ||
      el;

    const next = el.getAttribute("data-alt");
    el.setAttribute("data-alt", target.textContent.trim());
    target.textContent = next;

    // Títulos que ajustan su tamaño al número de letras (Poster.css)
    if (el.style.getPropertyValue("--chars")) {
      el.style.setProperty("--chars", String(Math.max(1, next.length)));
    }
  }

  // Contenido con formato (<em>, <strong>, <br>…): se intercambia el HTML.
  // Solo se usa con textos propios del sitio (JSON del servidor).
  function swapHtml(el) {
    const target =
      (el.classList.contains("reveal-lines") &&
        el.querySelector(":scope > .reveal-line > span")) ||
      el;

    const next = el.getAttribute("data-alt-html");
    el.setAttribute("data-alt-html", target.innerHTML.trim());
    target.innerHTML = next;
  }

  function swapAttr(el, attr) {
    const key = "data-alt-" + attr;
    const next = el.getAttribute(key);
    const current = el.getAttribute(attr);

    el.setAttribute(key, current == null ? "" : current);
    if (next === "" && attr !== "value") el.removeAttribute(attr);
    else el.setAttribute(attr, next);
  }

  function switchLanguage(trigger) {
    const root = document.documentElement;
    const from = (root.lang || "es").slice(0, 2);
    const to = from === "es" ? "en" : "es";
    const targetUrl = trigger.getAttribute("href");

    // Raíces a recorrer: el documento + el contenido de cada <template>
    // (p. ej. los textos de los modales de Estudio viven en plantillas)
    const roots = [document];
    document.querySelectorAll("template").forEach((t) => roots.push(t.content));

    roots.forEach((scope) => {
      scope.querySelectorAll("[data-alt]").forEach(swapText);
      scope.querySelectorAll("[data-alt-html]").forEach(swapHtml);

      ATTRS.forEach((attr) => {
        scope
          .querySelectorAll("[data-alt-" + attr + "]")
          .forEach((el) => swapAttr(el, attr));
      });
    });

    root.lang = to;

    if (document.title) {
      const titleEl = document.querySelector("title");
      if (titleEl) document.title = titleEl.textContent;
    }

    // Recordar la elección (evita la redirección automática por idioma)
    document.cookie = "lang_redirect=1; path=/; max-age=" + 60 * 60 * 24 * 30;

    try {
      if (targetUrl) history.replaceState(history.state, "", targetUrl);
    } catch (e) {
      /* file:// u orígenes distintos: se ignora */
    }

    document.dispatchEvent(
      new CustomEvent("ozz:langchange", { detail: { lang: to, from } }),
    );
  }

  document.addEventListener("click", (event) => {
    const trigger = event.target.closest("[data-lang-switch]");
    if (!trigger) return;

    // Ctrl/Cmd/clic medio: dejar que abra la otra versión en otra pestaña
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.button !== 0) {
      return;
    }

    event.preventDefault();
    switchLanguage(trigger);
  });

  window.OzzLang = {
    get current() {
      return (document.documentElement.lang || "es").slice(0, 2);
    },
    /** Texto según idioma: OzzLang.t({ es: "Hola", en: "Hello" }) */
    t(dict) {
      return dict[this.current] || dict.en || "";
    },
  };
})();
