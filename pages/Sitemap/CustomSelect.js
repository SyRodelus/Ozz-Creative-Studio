document.addEventListener("DOMContentLoaded", () => {
  const nativeSelect = document.getElementById("sm-group");
  const wrapper = nativeSelect.nextElementSibling; // .sm-select-wrapper
  const display = wrapper.querySelector(".sm-select-display");
  const options = wrapper.querySelectorAll(".sm-select-options li");

  // Abrir/cerrar
  display.addEventListener("click", () => {
    wrapper.classList.toggle("open");
  });

  // Selección
  options.forEach((opt) => {
    opt.addEventListener("click", () => {
      const value = opt.getAttribute("data-value");
      const text = opt.textContent;

      // sincronizamos el <select> real
      nativeSelect.value = value;

      // disparamos el evento "change" para que actúe como clic real
      nativeSelect.dispatchEvent(new Event("change", { bubbles: true }));

      // actualizamos UI
      display.textContent = text;
      wrapper.classList.remove("open");
    });
  });

  // Cerrar al hacer clic fuera
  document.addEventListener("click", (e) => {
    if (!wrapper.contains(e.target)) {
      wrapper.classList.remove("open");
    }
  });
});

