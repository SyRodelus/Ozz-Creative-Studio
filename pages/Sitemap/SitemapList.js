/* =========================================================
   SitemapList.js — mapa del sitio (ES / EN en un solo archivo)
   Sustituye a Sitemap.js y Sitemap-es.js:
   - Lee sitemap.xml (con sus <xhtml:link hreflang>) y muestra
     las URL del idioma activo.
   - Se vuelve a pintar al cambiar de idioma sin recargar.
========================================================= */

(function () {
  "use strict";

  const listEl = document.getElementById("sm-list");
  const searchEl = document.getElementById("sm-search");
  const groupEl = document.getElementById("sm-group");
  if (!listEl) return;

  const TXT = {
    es: {
      lastmod: "Última modificación",
      home: "Inicio",
      empty: "No se encontraron páginas en este idioma en el sitemap.xml.",
      error: "No se pudo cargar sitemap.xml.",
    },
    en: {
      lastmod: "Last modified",
      home: "Home",
      empty: "No pages were found for this language in sitemap.xml.",
      error: "sitemap.xml could not be loaded.",
    },
  };

  const lang = () => ((document.documentElement.lang || "es").slice(0, 2) === "en" ? "en" : "es");
  const t = (k) => TXT[lang()][k];

  const esc = (s) =>
    String(s).replace(/[&<>"']/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c]);

  const fmtDate = (iso) => {
    if (!iso) return "—";
    const d = new Date(iso);
    return isNaN(d)
      ? iso
      : d.toLocaleDateString(lang() === "es" ? "es-CO" : "en-US", {
          year: "numeric",
          month: "short",
          day: "2-digit",
        });
  };

  const firstSegment = (href) => {
    try {
      const u = new URL(href);
      if (u.pathname === "/es" || u.pathname === "/") return "home";
      return (u.pathname.split("/").filter(Boolean)[0] || "home").toLowerCase();
    } catch {
      return "home";
    }
  };

  const isHome = (loc) => firstSegment(loc) === "home";

  const sortEntries = (a, b) => {
    if (isHome(a.loc) && !isHome(b.loc)) return -1;
    if (!isHome(a.loc) && isHome(b.loc)) return 1;
    return (Date.parse(b.lastmod || "") || 0) - (Date.parse(a.lastmod || "") || 0);
  };

  /* ---------- Parseo ---------- */

  const NS_SM = "http://www.sitemaps.org/schemas/sitemap/0.9";
  const NS_XHTML = "http://www.w3.org/1999/xhtml";

  function parse(xml) {
    let nodes = Array.from(xml.getElementsByTagNameNS(NS_SM, "url"));
    if (!nodes.length) nodes = Array.from(xml.getElementsByTagName("url"));

    return nodes.map((node) => {
      const get = (tag) =>
        ((node.getElementsByTagNameNS(NS_SM, tag)[0] || node.getElementsByTagName(tag)[0])?.textContent || "").trim();

      let links = Array.from(node.getElementsByTagNameNS(NS_XHTML, "link"));
      if (!links.length) links = Array.from(node.getElementsByTagName("xhtml:link"));

      const alternates = {};
      links.forEach((ln) => {
        const rel = (ln.getAttribute("rel") || "").toLowerCase();
        const hl = (ln.getAttribute("hreflang") || "").toLowerCase();
        const href = ln.getAttribute("href") || "";
        if (rel === "alternate" && hl && href) alternates[hl] = href;
      });

      return { loc: get("loc"), lastmod: get("lastmod"), alternates };
    });
  }

  function urlFor(entry, l) {
    if (entry.alternates[l]) return entry.alternates[l];
    const variant = Object.keys(entry.alternates).find((k) => k.startsWith(l + "-"));
    if (variant) return entry.alternates[variant];
    // Sin alternativas: el <loc> es la versión en inglés por defecto
    return l === "en" ? entry.loc : null;
  }

  /* ---------- Carga ---------- */

  const BASE = (window.BASE_URL || "").replace(/\/+$/, "");
  const CANDIDATES = [BASE + "/sitemap.xml", "/sitemap.xml"];

  async function load() {
    for (const url of CANDIDATES) {
      try {
        const r = await fetch(url, { credentials: "omit" });
        if (r.ok) return parse(new DOMParser().parseFromString(await r.text(), "text/xml"));
      } catch (_) {
        /* siguiente candidato */
      }
    }
    throw new Error("sitemap.xml");
  }

  /* ---------- Render ---------- */

  let entries = [];
  let failed = false;

  function card({ loc, lastmod }) {
    const a = document.createElement("a");
    a.className = "sm-card";
    a.href = loc;
    a.setAttribute("role", "listitem");
    a.innerHTML =
      `<div class="sm-link">${esc(loc)}</div>` +
      `<div class="sm-meta"><span class="sm-badge" aria-label="${esc(t("lastmod"))}">⏱ ${esc(fmtDate(lastmod))}</span></div>`;
    return a;
  }

  function render() {
    if (failed) {
      listEl.innerHTML = `<p class="sm-desc">${esc(t("error"))}</p>`;
      return;
    }

    const l = lang();
    const query = (searchEl?.value || "").trim().toLowerCase();
    const mode = groupEl?.value || "none";
    const seen = new Set();

    const view = entries
      .map((e) => {
        const loc = urlFor(e, l);
        return loc ? { loc, lastmod: e.lastmod } : null;
      })
      .filter((v) => v && !seen.has(v.loc) && seen.add(v.loc))
      .filter((v) => !query || v.loc.toLowerCase().includes(query))
      .sort(sortEntries);

    listEl.innerHTML = "";

    if (!view.length) {
      listEl.innerHTML = `<p class="sm-desc">${esc(t("empty"))}</p>`;
      return;
    }

    if (mode === "prefix") {
      const groups = new Map();
      view.forEach((v) => {
        const key = firstSegment(v.loc);
        if (!groups.has(key)) groups.set(key, []);
        groups.get(key).push(v);
      });

      [...groups.entries()]
        .sort(([a], [b]) => (a === "home" ? -1 : b === "home" ? 1 : a.localeCompare(b)))
        .forEach(([key, items]) => {
          const group = document.createElement("div");
          group.className = "sm-group";
          group.innerHTML = `<h3 class="sm-group-title">${esc(key === "home" ? t("home") : key)}</h3><div class="sm-cards"></div>`;
          const wrap = group.querySelector(".sm-cards");
          items.forEach((v) => wrap.appendChild(card(v)));
          listEl.appendChild(group);
        });
    } else {
      const wrap = document.createElement("div");
      wrap.className = "sm-cards";
      view.forEach((v) => wrap.appendChild(card(v)));
      listEl.appendChild(wrap);
    }
  }

  load()
    .then((all) => {
      entries = all;
      render();
    })
    .catch(() => {
      failed = true;
      render();
    });

  searchEl?.addEventListener("input", render);
  groupEl?.addEventListener("change", render);

  // Cambio de idioma sin recarga: repintar y mantener la opción elegida
  document.addEventListener("ozz:langchange", () => {
    const display = document.querySelector(".sm-select-display");
    const selected = document.querySelector(
      `.sm-select-options li[data-value="${groupEl?.value || "none"}"]`,
    );
    if (display && selected) display.textContent = selected.textContent;
    render();
  });
})();
