(() => {
  const modal = document.getElementById("service-modal");
  if (!modal) return;

  // —— Portal: saca el modal de #services y llévalo a <body> para evitar stacking contexts —
  if (modal.parentNode !== document.body) {
    document.body.appendChild(modal);
  }

  const titleEl = document.getElementById("service-modal-title");
  const descEl = document.getElementById("service-modal-desc");
  const listEl = document.getElementById("service-modal-bullets");
  const closeBtn = modal.querySelector(".service-modal__close");
  const dialogEl = modal.querySelector(".service-modal__dialog");

  const byId = (id) => document.getElementById(id);

  const templates = {
    "brand-seeds": byId("tpl-brand-seeds"),
    "visual-identity": byId("tpl-visual-identity"),
    "development-strategy": byId("tpl-development-strategy"),
    "content-direction": byId("tpl-content-direction"),
    "brand-activation": byId("tpl-brand-activation"),
    "creative-strategy": byId("tpl-creative-strategy"),
  };

  // —— Accesibilidad: recordemos el elemento con foco antes de abrir —
  let lastFocused = null;

  // —— Bloquear scroll de la página mientras el modal está abierto —
  const disableScroll = () => {
    const scrollY = window.scrollY;
    document.body.style.position = "fixed";
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = "100%";
    document.body.dataset.scrollY = scrollY;
  };

  // —— Restaurar scroll al cerrar —
  const enableScroll = () => {
    const scrollY = document.body.dataset.scrollY || "0";
    document.body.style.position = "";
    document.body.style.top = "";
    document.body.style.width = "";
    window.scrollTo(0, parseInt(scrollY, 10));
    delete document.body.dataset.scrollY;
  };

  // —— Abrir modal con contenido desde <template> —
  const openModal = (key) => {
    const tpl = templates[key];
    if (!tpl) return;

    const t = tpl.content;
    titleEl.textContent = t.querySelector("[data-title]")?.textContent ?? "";
    descEl.textContent = t.querySelector("[data-desc]")?.textContent ?? "";

    listEl.innerHTML = "";
    const items = [...t.querySelectorAll("[data-bullets] li")];
    items.forEach((li) => {
      const liClone = document.createElement("li");
      liClone.textContent = li.textContent;
      listEl.appendChild(liClone);
    });

    lastFocused =
      document.activeElement instanceof HTMLElement
        ? document.activeElement
        : null;

    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    // Aseguramos atributos ARIA adecuados
    modal.setAttribute("aria-modal", "true");
    dialogEl?.setAttribute("role", "document");

    disableScroll();

    // Enfocar el botón cerrar para usuarios de teclado
    closeBtn?.focus();
  };

  // —— Cerrar modal —
  const closeModal = () => {
    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    modal.removeAttribute("aria-modal");
    enableScroll();

    // Restaurar foco donde estaba
    if (lastFocused && document.body.contains(lastFocused)) {
      lastFocused.focus();
    } else {
      // Si no existe, al menos quitemos foco del botón
      document.activeElement && document.activeElement.blur();
    }
  };

  // —— Delegación de clicks para abrir —
  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".service-card[data-modal]");
    if (btn) {
      e.preventDefault();
      const key = btn.getAttribute("data-modal");
      openModal(key);
    }
  });

  // —— Cerrar por botón —
  closeBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    closeModal();
  });

  // —— Cerrar al hacer click en el overlay (pero no dentro del diálogo) —
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  // Evitar que clicks dentro del diálogo burbujeen al overlay
  dialogEl?.addEventListener("click", (e) => {
    e.stopPropagation();
  });

  // —— Cerrar con Escape —
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.classList.contains("is-open")) {
      e.preventDefault();
      closeModal();
    }
  });
})();
