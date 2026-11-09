document.addEventListener("DOMContentLoaded", () => {
  // Selecciona todas las secciones y el footer
  const elements = Array.from(document.querySelectorAll("section, footer"));

  // Fallback para navegadores antiguos
  if (!("IntersectionObserver" in window)) {
    elements.forEach((el) => el.classList.add("is-visible"));
    return;
  }

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting || entry.intersectionRatio > 0) {
          entry.target.classList.add("is-visible");
          obs.unobserve(entry.target); // Elimina esta línea si quieres que se repita al volver a aparecer
        }
      });
    },
    {
      root: null,
      threshold: 0.01, // Activa cuando al menos el 1% es visible
      rootMargin: "0px 0px -10% 0px", // Ajusta para disparar un poco antes de salir
    }
  );

  elements.forEach((el) => observer.observe(el));
});
