// Timeline de animación del botón
window.basicTimeline = anime.timeline({
  autoplay: false,
});

window.basicTimeline
  .add({ targets: ".SubmitTxt", duration: 1, opacity: 0 })
  .add({ targets: ".SubmitBtn", duration: 300, height: 10, width: 100 }) // inicio rápido
  .add({
    targets: ".progress-bar",
    duration: 3000, // duración mínima de la barra
    width: "100%",
    easing: "linear",
  })
  .add({
    targets: ".progress-bar",
    duration: 200,
    opacity: 0,
    easing: "easeOutQuad",
  })
  .add({
    targets: ".SubmitBtn",
    duration: 500,
    height: 40,
    width: 100,
    begin: function () {
      anime({
        targets: ".SubmitTxt",
        opacity: [0, 1],
        duration: 300,
        easing: "easeOutQuad",
      });
    },
    complete: function () {
      $(".progress-bar").css({ width: 0, opacity: 1 });
    },
  });

// Estado actual
let submitStatus = "loading";

// Función para disparar animación según estado
window.triggerSubmitAnimation = (status) => {
  const submitText = document.querySelector(".SubmitTxt");
  if (!submitText) return;

  submitStatus = status;

  if (status === "loading") {
    submitText.textContent = ""; // no mostrar texto aún
    if (window.basicTimeline) window.basicTimeline.restart();
  } else if (status === "success") {
    submitText.textContent = "Submitted";
    resetAfterDelay();
  } else if (status === "error") {
    submitText.textContent = "Error";
    resetAfterDelay();
  }
};

// 🔹 Reiniciar el botón a estado base después de 1 min
function resetAfterDelay() {
  setTimeout(() => {
    const submitText = document.querySelector(".SubmitTxt");
    if (submitText) {
      submitText.textContent = "Submit";
      submitText.style.opacity = 1;
    }
    $(".SubmitBtn").css({ height: 40, width: 100 });
    $(".progress-bar").css({ width: 0, opacity: 1 });
  }, 60000); // 1 minuto
}
