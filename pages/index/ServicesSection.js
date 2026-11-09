(async function () {
  const lang = document.documentElement.lang === "es" ? "es" : "en";
  const isMobile = window.matchMedia("(max-width: 768px)").matches;

  const res = await fetch("pages/index/ServicesSection.json");
  const data = await res.json();

  const servicesData = data.services;
  const servicesKeys = Object.keys(servicesData);

  const servicesList = document.getElementById("servicesList");
  const image = document.getElementById("serviceImage");

  /* ===================== */
  /* RENDER SERVICES */
  /* ===================== */

  servicesKeys.forEach((key, index) => {
    const service = servicesData[key];
    const li = document.createElement("li");

    li.textContent = service.label[lang];
    li.dataset.index = index;

    if (index === 0) li.classList.add("active");

    li.addEventListener("click", () => {
      window.location.href = service.link;
    });

    li.addEventListener("mouseenter", () => {
      if (!isMobile) activateService(index);
    });

    servicesList.appendChild(li);
  });

  function activateService(index) {
    document.querySelectorAll(".services-list li").forEach((el) => {
      el.classList.remove("active");
    });

    const service = servicesData[servicesKeys[index]];
    const li = document.querySelector(`[data-index="${index}"]`);
    if (!li) return;

    li.classList.add("active");

    image.classList.remove("visible");
    requestAnimationFrame(() => {
      image.src = service.image;
      image.alt = service.seo[lang].title;
      image.classList.add("visible");
    });
  }

  /* INIT */
  activateService(0);
  image.classList.add("visible");

  /* ===================== */
  /* DESKTOP AUTOPLAY (NO TOCAR) */
  /* ===================== */

  if (!isMobile) {
    let current = 0;
    setInterval(() => {
      current = (current + 1) % servicesKeys.length;
      activateService(current);
    }, 3000);
    return;
  }

  /* ===================== */
  /* MOBILE STICKY SCROLL LOGIC */
  /* ===================== */

  const items = Array.from(document.querySelectorAll(".services-list li"));
  const preview = document.querySelector(".service-preview");

  let lastActive = 0;

  function updateMobileState() {
    const imageRect = preview.getBoundingClientRect();
    const anchorY = imageRect.bottom + 0;

    let nextActive = null;

    items.forEach((item) => {
      const rect = item.getBoundingClientRect();

      /* desaparece si pasa detrás */
      if (rect.top < imageRect.bottom) {
        item.classList.add("under");
      } else {
        item.classList.remove("under");
      }

      /* primer item justo debajo de la imagen */
      if (!nextActive && rect.top >= anchorY) {
        nextActive = item;
      }
    });

    if (nextActive) {
      const index = Number(nextActive.dataset.index);
      if (index !== lastActive) {
        activateService(index);
        lastActive = index;
      }
    }
  }

  updateMobileState();

  window.addEventListener("scroll", updateMobileState, { passive: true });
  window.addEventListener("resize", updateMobileState);
})();
