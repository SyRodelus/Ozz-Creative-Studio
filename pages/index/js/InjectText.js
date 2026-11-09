/**
 * Obtiene un valor anidado usando una ruta tipo:
 * "hero.title" -> data.hero.title
 */
function getNestedValue(obj, path) {
  return path.split(".").reduce((acc, key) => acc?.[key], obj);
}

// Cache global compartida
import { sectionCache } from "../../../Scripts/Cache.js";

/**
 * Carga y traduce una sección
 */
async function loadSection(section) {
  if (section.dataset.i18nLoaded === "true") return;

  const file = `pages/index/texts/${section.id}.json`;

  let data;

  try {
    if (sectionCache.has(file)) {
      data = sectionCache.get(file);
    } else {
      const response = await fetch(file);

      if (!response.ok) {
        throw new Error(`No se pudo cargar ${file}`);
      }

      data = await response.json();
      sectionCache.set(file, data);
    }

    const lang = document.documentElement.lang.toLowerCase().startsWith("es")
      ? "es"
      : "en";

    section.querySelectorAll("[data-i18n]").forEach((element) => {
      const instructions = element.dataset.i18n.split("|").map((s) => s.trim());

      instructions.forEach((instruction) => {
        let attribute = "textContent";
        let key = instruction;

        if (instruction.includes(":")) {
          const parts = instruction.split(":");
          attribute = parts[0].trim();
          key = parts[1].trim();
        }

        const translation = getNestedValue(data, key);
        if (!translation) return;

        const value = translation[lang] || translation.en || "";

        if (attribute === "textContent") {
          element.textContent = value;
        } else {
          element.setAttribute(attribute, value);
        }
      });
    });

    section.dataset.i18nLoaded = "true";

    // 🔥 Notificar al reveal que el texto ya fue inyectado
    section.dispatchEvent(
      new CustomEvent("i18n:sectionLoaded", { bubbles: true }),
    );
  } catch (error) {
    console.error(error);
  }
}

/**
 * Observer
 */
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;

      loadSection(entry.target);
      observer.unobserve(entry.target);
    });
  },
  {
    root: null,
    rootMargin: "600px 0px",
    threshold: 0,
  },
);

/**
 * INIT
 */
document.querySelectorAll("section[id]").forEach((section) => {
  observer.observe(section);
});

/**
 * 🔥 EVENTO GLOBAL cuando i18n termina
 */
window.addEventListener("load", () => {
  window.__I18N_READY__ = true;
  window.dispatchEvent(new Event("i18n:loaded"));
});
