document.addEventListener("DOMContentLoaded", () => {
  const btnWhatsapp = document.getElementById("BtnWhatsapp");
  if (!btnWhatsapp) return;

  const whatsappLink = btnWhatsapp.querySelector("a");
  if (!whatsappLink) return;

  // ===== Idioma =====
  const lang = document.documentElement.lang?.toLowerCase().startsWith("en")
    ? "en"
    : "es";

  const texts = {
    es: {
      aria: "Contactar a Ozz Estudio Creativo por WhatsApp",
      title: "Chatear por WhatsApp con Ozz Estudio Creativo",
    },
    en: {
      aria: "Contact Ozz Creative Studio on WhatsApp",
      title: "Chat with Ozz Creative Studio on WhatsApp",
    },
  };

  whatsappLink.setAttribute("aria-label", texts[lang].aria);
  whatsappLink.setAttribute("title", texts[lang].title);

  // ===== Scroll (se ejecuta una sola vez) =====
  window.addEventListener(
    "scroll",
    () => {
      btnWhatsapp.classList.add("expanded");
    },
    { once: true }
  );
});
