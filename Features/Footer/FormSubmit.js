// Función para sanitizar entradas preservando la posición del cursor
const sanitizeInput = (input) => {
  const start = input.selectionStart;
  const end = input.selectionEnd;
  const newValue = input.value.replace(/</g, "&lt;").replace(/>/g, "&gt;");
  if (input.value !== newValue) {
    input.value = newValue;
    input.setSelectionRange(start, end);
  }
  return newValue;
};

// Detector de scripts mejorado
const detectScripts = () => {
  const inputs = document.querySelectorAll(
    '#contactForm input[type="text"], #contactForm input[type="email"], #contactForm input[type="tel"]'
  );
  let hasScript = false;
  inputs.forEach((input) => {
    const cleanValue = sanitizeInput(input);
    if (/(<script>|javascript:|<\/script>|eval\(|onload=)/i.test(cleanValue)) {
      hasScript = true;
    }
  });

  const submitBtn = document.getElementById("SubmitBtn");
  if (submitBtn) {
    submitBtn.style.display = hasScript ? "none" : "block";
    submitBtn.disabled = hasScript;
  }

  return !hasScript; // true si todo está limpio
};

// Setup de eventos para detección en vivo
const setupEventListeners = () => {
  document.querySelectorAll("#contactForm input").forEach((input) => {
    ["input", "change", "keyup", "paste"].forEach((evt) => {
      input.addEventListener(evt, () => {
        setTimeout(detectScripts, 10);
      });
    });
  });
};

document.addEventListener("DOMContentLoaded", () => {
  setupEventListeners();
  detectScripts();

  const form = document.getElementById("contactForm");
  const alertContainer = document.getElementById("alert-container");

  if (!form || !alertContainer) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    detectScripts();

    // Validación de campos y scripts
    if (
      !form.checkValidity() ||
      document.getElementById("SubmitBtn").disabled
    ) {
      form.reportValidity();
      alertContainer.innerHTML =
        '<div class="alert-error">Invalid input detected</div>';
      window.triggerSubmitAnimation("error");
      return;
    }

    // 🔹 Lanzar animación inmediatamente
    window.triggerSubmitAnimation("loading");

    const formData = new FormData(form);
    const startTime = Date.now();

    fetch("/PHP/sendemail.php", {
      method: "POST",
      body: formData,
      credentials: "include",
    })
      .then((response) => response.json())
      .then((data) => {
        const elapsed = Date.now() - startTime;
        const remaining = Math.max(0, 3000 - elapsed); // mínimo 3s de carga

        setTimeout(() => {
          if (typeof data.html === "string")
            alertContainer.innerHTML = data.html;

          if (data.new_csrf_token) {
            const csrfInput = document.getElementById("csrf_token");
            if (csrfInput) csrfInput.value = data.new_csrf_token;
          }

          if (data.html && data.html.includes("alert-success")) {
            form.reset();
            detectScripts();
            window.triggerSubmitAnimation("success");
          } else {
            window.triggerSubmitAnimation("error");
          }

          setTimeout(() => {
            alertContainer.innerHTML = "";
          }, 8000);
        }, remaining);
      })
      .catch((error) => {
        console.error("Error:", error);
        window.triggerSubmitAnimation("error");
        alertContainer.innerHTML =
          '<div class="alert-error">Error submitting the form</div>';
      });
  });
});
