const toggle = document.getElementById("OpenMenu");
const mobileMenu = document.getElementById("MobileMenu");

// ======================================
// FUNCIONES
// ======================================

function openMenu() {
  mobileMenu.classList.add("active");
  toggle.classList.add("active");
  document.body.classList.add("mobile-menu-open");
}

function closeMenu() {
  mobileMenu.classList.remove("active");
  toggle.classList.remove("active");
  document.body.classList.remove("mobile-menu-open");

  document.querySelectorAll(".mobile-item.open").forEach((item) => {
    item.classList.remove("open");
  });
}

function toggleMenu() {
  if (mobileMenu.classList.contains("active")) {
    closeMenu();
  } else {
    openMenu();
  }
}

// ======================================
// ABRIR / CERRAR MENÚ
// ======================================

toggle.addEventListener("click", toggleMenu);

// ======================================
// ACORDEONES
// ======================================

document.querySelectorAll(".mobile-title").forEach((button) => {
  button.addEventListener("click", () => {
    const currentItem = button.parentElement;

    document.querySelectorAll(".mobile-item.open").forEach((item) => {
      if (item !== currentItem) {
        item.classList.remove("open");
      }
    });

    currentItem.classList.toggle("open");
  });
});

// ======================================
// CERRAR AL HACER CLICK EN UN LINK
// ======================================

document.querySelectorAll(".mobile-menu a").forEach((link) => {
  link.addEventListener("click", closeMenu);
});

// ======================================
// CERRAR CON ESC
// ======================================

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && mobileMenu.classList.contains("active")) {
    closeMenu();
  }
});

// ======================================
// SI LA PANTALLA PASA A DESKTOP,
// CERRAR EL MENÚ AUTOMÁTICAMENTE
// ======================================

window.addEventListener("resize", () => {
  if (window.innerWidth > 1024) {
    closeMenu();
  }
});
