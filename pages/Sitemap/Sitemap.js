(function () {
  const listEl = document.getElementById("sm-list");
  const searchEl = document.getElementById("sm-search");
  const groupEl = document.getElementById("sm-group");

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

  const firstSegment = (href) => {
    try {
      const u = new URL(href);
      const seg = u.pathname.split("/").filter(Boolean)[0] || "/";
      return seg.toLowerCase();
    } catch {
      return "/";
    }
  };

  /* Orden: home primero, luego por fecha desc (se elimina prioridad) */
  const byHomeThenDate = (a, b) => {
    const isHome = (loc) => /^https?:\/\/[^/]+\/?$/.test(loc);
    if (isHome(a.loc) && !isHome(b.loc)) return -1;
    if (!isHome(a.loc) && isHome(b.loc)) return 1;
    const d = (s) => Date.parse(s || "") || 0;
    return d(b.lastmod) - d(a.lastmod);
  };

  /* AHORA DEVUELVE UN <a class="sm-card"> PARA QUE TODA LA TARJETA SEA LINK */
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
        <span class="sm-badge" aria-label="Last modified">⏱ ${fmtDate(
          lastmod
        )}</span>
      </div>
    `;
    return a;
  };

  const render = (urls, mode = "none", q = "") => {
    const query = q.trim().toLowerCase();
    const filtered = query
      ? urls.filter((u) => u.loc.toLowerCase().includes(query))
      : urls.slice();
    listEl.innerHTML = "";

    if (mode === "prefix") {
      const groups = new Map();
      filtered.forEach((u) => {
        const key = firstSegment(u.loc);
        if (!groups.has(key)) groups.set(key, []);
        groups.get(key).push(u);
      });

      [...groups.entries()].sort().forEach(([key, items]) => {
        const group = document.createElement("div");
        group.className = "sm-group";
        group.innerHTML = `<h3 class="sm-group-title">${
          key === "/" ? "Root" : key
        }</h3><div class="sm-cards"></div>`;
        const wrap = group.querySelector(".sm-cards");
        items.forEach((item) => wrap.appendChild(makeCard(item)));
        listEl.appendChild(group);
      });
    } else {
      const wrap = document.createElement("div");
      wrap.className = "sm-cards";
      filtered.forEach((item) => wrap.appendChild(makeCard(item)));
      listEl.appendChild(wrap);
    }
  };

  const parseSitemap = (txt) => {
    const xml = new DOMParser().parseFromString(txt, "text/xml");
    const urlsNS = Array.from(
      xml.getElementsByTagNameNS(
        "http://www.sitemaps.org/schemas/sitemap/0.9",
        "url"
      )
    );
    const nodes = urlsNS.length
      ? urlsNS
      : Array.from(xml.getElementsByTagName("url"));

    return nodes
      .map((node) => {
        const useNS = urlsNS.length > 0;
        const get = (tag) =>
          useNS
            ? node.getElementsByTagNameNS(
                "http://www.sitemaps.org/schemas/sitemap/0.9",
                tag
              )[0]
            : node.getElementsByTagName(tag)[0];
        return {
          loc: get("loc")?.textContent.trim() || "",
          lastmod: get("lastmod")?.textContent.trim() || "",
        };
      })
      .filter((x) => x.loc);
  };

  let urls = [];
  fetch("/sitemap.xml", { credentials: "omit" })
    .then((r) => r.text())
    .then((txt) => {
      urls = parseSitemap(txt);
      urls.sort(byHomeThenDate);
      render(urls);
    })
    .catch(() => {
      listEl.innerHTML = `<p class="sm-desc">Could not load <code>/sitemap.xml</code>. Check that it exists and is public.</p>`;
    });

  // Interacciones
  searchEl.addEventListener("input", () =>
    render(urls, groupEl.value, searchEl.value)
  );
  groupEl.addEventListener("change", () =>
    render(urls, groupEl.value, searchEl.value)
  );
})();
