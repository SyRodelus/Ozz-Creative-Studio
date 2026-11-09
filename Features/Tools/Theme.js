// ===============================
// Theme.js — Gestión de temas + evento "theme:changed"
// ===============================

// Definición de colores y cursor para los temas
const themes = {
  dark: {
    "--color-bg": "#000000",
    "--color-bg-rgb": "0, 0, 0",
    "--color-text-rgb": "255, 255, 255",
    "--color-text": "#ffffff",
    "--color-border": "#777777",
    "--color-input-bg": "#1e1e1eb3",
    "--color-input-text": "#ccccccb3",
    "--color-placeholder": "#484848",
    "--color-hover-text": "#000000",
    "--color-hover-bg": "#ffffff",
    "--color-cta": "#1f1f1f",
    "--cursor": "url(arrowheadlight.svg), auto",
    "--cursor-pointer": "url(handpointer.png), pointer",
    "--cursor-select": "url(textselectdark.png), pointer",
  },
  light: {
    "--color-bg": "#ffffff",
    "--color-bg-rgb": "255, 255, 255",
    "--color-text-rgb": "0, 0, 0",
    "--color-text": "#000000",
    "--color-border": "#333333",
    "--color-input-bg": "#f0f0f0",
    "--color-input-text": "#333333",
    "--color-placeholder": "#e5e5e5",
    "--color-hover-text": "#ffffff",
    "--color-hover-bg": "#000000",
    "--color-cta": "#FDFDFB",
    "--cursor": "url(arrowhead.svg), auto",
    "--cursor-pointer": "url(handpointerdark.png), pointer",
    "--cursor-select": "url(textselect.png), pointer",
  },
};

// Aplica el tema y notifica al resto de la app
function applyTheme(isDarkMode) {
  const root = document.documentElement;
  const theme = isDarkMode ? themes.dark : themes.light;

  for (const [prop, value] of Object.entries(theme)) {
    root.style.setProperty(prop, value);
  }

  window.currentTheme = isDarkMode;

  // Notificar que el tema cambió (Vanta u otros escuchan esto)
  document.dispatchEvent(
    new CustomEvent("theme:changed", { detail: { isDarkMode } }),
  );
}

// Actualiza iconos (opcional)
function updateIcons(isDarkMode) {
  const defaultIcon = document.querySelector(".default-icon");
  const hoverIcon = document.querySelector(".hover-icon");
  if (!defaultIcon || !hoverIcon) return;

  if (isDarkMode) {
    defaultIcon.style.display = "block";
    hoverIcon.style.display = "none";
  } else {
    defaultIcon.style.display = "none";
    hoverIcon.style.display = "block";
  }
}

// Aplicar tema lo antes posible (evitar FOIT)
(function () {
  const savedTheme = localStorage.getItem("theme");
  const systemPrefersDark = window.matchMedia(
    "(prefers-color-scheme: dark)",
  ).matches;
  const isDarkMode = savedTheme ? savedTheme === "dark" : systemPrefersDark;
  applyTheme(isDarkMode);
})();

// UI y listeners
document.addEventListener("DOMContentLoaded", () => {
  const themeToggle = document.getElementById("theme-toggle");
  if (!themeToggle) return;

  let isDarkMode = window.currentTheme ?? false;

  updateIcons(isDarkMode);

  themeToggle.addEventListener("click", (e) => {
    e.preventDefault();
    isDarkMode = !isDarkMode;
    applyTheme(isDarkMode);
    localStorage.setItem("theme", isDarkMode ? "dark" : "light");
    updateIcons(isDarkMode);
  });

  // Cambios del SO (si no hay override manual)
  window
    .matchMedia("(prefers-color-scheme: dark)")
    .addEventListener("change", (e) => {
      if (!localStorage.getItem("theme")) {
        isDarkMode = e.matches;
        applyTheme(isDarkMode);
        updateIcons(isDarkMode);
      }
    });
});
