(function () {
  const listEl = document.getElementById("sm-list");
  const searchEl = document.getElementById("sm-search");
  const groupEl = document.getElementById("sm-group");

  // ---------------- Utils ----------------
  const fmtDate = (iso) => {
    if (!iso) return "—";
    const d = new Date(iso);
    return isNaN(d)
      ? iso
      : d.toLocaleDateString(undefined, {
          year: "numeric",
          month: "short",
          day: "2-digit",
        });
  };

  // Primer segmento de la ruta; si es exactamente /es, lo tratamos como "inicio"
  const firstSegment = (href) => {
    try {
      const u = new URL(href);
      if (u.pathname === "/es") return "inicio"; // Home español
      const seg = u.pathname.split("/").filter(Boolean)[0] || "/";
      return seg.toLowerCase();
    } catch {
      return "/";
    }
  };

  const isHomeAbs = (loc) => /^https?:\/\/[^/]+\/?$/.test(loc);
  const byHomeThenDate = (a, b) => {
    if (isHomeAbs(a.loc) && !isHomeAbs(b.loc)) return -1;
    if (!isHomeAbs(a.loc) && isHomeAbs(b.loc)) return 1;
    const d = (s) => Date.parse(s || "") || 0;
    return d(b.lastmod) - d(a.lastmod);
  };

  // ---------------- Parseo sitemap (urlset + xhtml:link) ----------------
  const NS_SM = "http://www.sitemaps.org/schemas/sitemap/0.9";
  const NS_XHTML = "http://www.w3.org/1999/xhtml";

  function parseUrlsetWithAlternates(xml) {
    const urlNodes = Array.from(xml.getElementsByTagNameNS(NS_SM, "url")).length
      ? Array.from(xml.getElementsByTagNameNS(NS_SM, "url"))
      : Array.from(xml.getElementsByTagName("url"));

    const entries = urlNodes.map((node) => {
      const get = (tag) => {
        const ns = node.getElementsByTagNameNS(NS_SM, tag);
        const noNs = node.getElementsByTagName(tag);
        return (ns[0] || noNs[0])?.textContent?.trim() || "";
      };

      const loc = get("loc");
      const lastmod = get("lastmod");

      // Captura alternates <xhtml:link rel="alternate" hreflang=".." href="..">
      let altNodes = Array.from(node.getElementsByTagNameNS(NS_XHTML, "link"));
      if (!altNodes.length) {
        // Fallback si el parser deja prefijos como <xhtml:link>
        altNodes = Array.from(node.getElementsByTagName("xhtml:link"));
      }

      const alternates = {};
      altNodes.forEach((ln) => {
        const rel = (ln.getAttribute("rel") || "").toLowerCase();
        const lang = (ln.getAttribute("hreflang") || "").toLowerCase();
        const href = ln.getAttribute("href") || "";
        if (rel === "alternate" && lang && href) {
          alternates[lang] = href;
        }
      });

      return { loc, lastmod, alternates };
    });

    return entries;
  }

  // ---------------- Carga del sitemap ----------------
  const CANDIDATES = ["/sitemap.xml", "/Sitemap.xml"];

  async function loadEntries() {
    let xmlText = null;
    for (const u of CANDIDATES) {
      try {
        const r = await fetch(u, { credentials: "omit" });
        if (r.ok) {
          xmlText = await r.text();
          break;
        }
      } catch (_) {}
    }
    if (!xmlText) throw new Error("No se pudo cargar sitemap.xml");

    const xml = new DOMParser().parseFromString(xmlText, "text/xml");

    // Para este caso esperamos <urlset>; si en el futuro usas <sitemapindex>, se puede extender aquí.
    return parseUrlsetWithAlternates(xml);
  }

  // ---------------- Render ----------------
  const makeCard = ({ loc, lastmod }) => {
    const a = document.createElement("a");
    a.className = "sm-card";
    a.href = loc;
    a.target = "_self";
    a.rel = "noopener";
    a.setAttribute("role", "listitem");
    a.innerHTML = `
      <div class="sm-link">${loc}</div>
      <div class="sm-meta">
        <span class="sm-badge" aria-label="Última modificación">⏱ ${fmtDate(lastmod)}</span>
      </div>
    `;
    return a;
  };

  function pickSpanishURL(entry) {
    // Prioriza 'es' y luego variantes 'es-*'
    if (entry.alternates["es"]) return entry.alternates["es"];
    const esVar = Object.keys(entry.alternates).find((k) =>
      k.startsWith("es-"),
    );
    if (esVar) return entry.alternates[esVar];

    // Fallback por si el <loc> ya fuera ES (no es tu caso típico, pero no estorba)
    try {
      const u = new URL(entry.loc);
      if (u.pathname === "/es" || /(^|\/)[^/]*-es(\/)?$/.test(u.pathname))
        return entry.loc;
    } catch {}
    return null;
  }

  function render(entries, mode = "none", q = "") {
    const query = q.trim().toLowerCase();

    // 1) Transformar cada <url> en su URL española (si existe)
    let view = entries
      .map((e) => {
        const esHref = pickSpanishURL(e);
        return esHref ? { loc: esHref, lastmod: e.lastmod } : null;
      })
      .filter(Boolean);

    // 2) Eliminar duplicados
    const seen = new Set();
    view = view.filter((v) =>
      seen.has(v.loc) ? false : (seen.add(v.loc), true),
    );

    // 3) Búsqueda
    if (query) view = view.filter((u) => u.loc.toLowerCase().includes(query));

    // 4) Orden
    view.sort(byHomeThenDate);

    // 5) Pintar
    listEl.innerHTML = "";
    if (!view.length) {
      listEl.innerHTML = `
        <p class="sm-desc">
          No se hallaron versiones en español mediante <code>&lt;xhtml:link hreflang="es"&gt;</code>
          en el <code>sitemap.xml</code>.
        </p>`;
      return;
    }

    if (mode === "prefix") {
      const groups = new Map();
      view.forEach((u) => {
        const key = firstSegment(u.loc);
        if (!groups.has(key)) groups.set(key, []);
        groups.get(key).push(u);
      });

      [...groups.entries()].sort().forEach(([key, items]) => {
        // Título del grupo: renombramos "/" o "inicio" a "Inicio"
        const title = key === "/" || key === "inicio" ? "Inicio" : key;
        const group = document.createElement("div");
        group.className = "sm-group";
        group.innerHTML = `<h3 class="sm-group-title">${title}</h3><div class="sm-cards"></div>`;
        const wrap = group.querySelector(".sm-cards");
        items.forEach((item) => wrap.appendChild(makeCard(item)));
        listEl.appendChild(group);
      });
    } else {
      const wrap = document.createElement("div");
      wrap.className = "sm-cards";
      view.forEach((item) => wrap.appendChild(makeCard(item)));
      listEl.appendChild(wrap);
    }
  }

  // ---------------- Boot ----------------
  let entries = [];
  loadEntries()
    .then((all) => {
      entries = all;
      render(entries, groupEl?.value || "none", searchEl?.value || "");
    })
    .catch((err) => {
      listEl.innerHTML = `
        <p class="sm-desc">
          No se pudo cargar <code>sitemap.xml</code>.<br/>
          Detalle: ${String(err).replace(/</g, "&lt;")}
        </p>`;
    });

  // Eventos
  if (searchEl)
    searchEl.addEventListener("input", () =>
      render(entries, groupEl.value, searchEl.value),
    );
  if (groupEl)
    groupEl.addEventListener("change", () =>
      render(entries, groupEl.value, searchEl.value),
    );
})();
