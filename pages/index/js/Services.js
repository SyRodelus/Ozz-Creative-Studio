/* =========================================================
   Services.js — sección de servicios (bento)
   - Activa la entrada escalonada de las tarjetas y los
     píxeles del retrato cuando la sección entra en pantalla.
   - En táctil: la tarjeta del centro pasa a color.
========================================================= */

(function () {
  "use strict";

  const section = document.getElementById("services");
  if (!section || !("IntersectionObserver" in window)) return;

  section.classList.add("svc-ready");

  // 1) Entrada de la sección (una vez)
  const enter = new IntersectionObserver(
    (entries) => {
      if (!entries[0].isIntersecting) return;
      section.classList.add("is-inview");
      enter.disconnect();
    },
    { threshold: 0.15 },
  );
  enter.observe(section.querySelector(".svc-layout") || section);

  // 2) Tarjeta en el centro de la pantalla (táctil)
  const cards = section.querySelectorAll(".svc-card");
  const center = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) =>
        entry.target.classList.toggle("is-inview", entry.isIntersecting),
      );
    },
    { rootMargin: "-35% 0px -35% 0px", threshold: 0 },
  );
  cards.forEach((card) => center.observe(card));
})();
