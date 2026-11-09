/* =========================================================
   ContactForm.js — validación y envío del formulario de
   contacto (fetch → php/sendemail.php). Mensajes en el
   idioma activo (también tras cambiar de idioma sin recargar).
========================================================= */

(function () {
  "use strict";

  const form = document.getElementById("ctForm");
  if (!form) return;

  const status = form.querySelector(".ct-status");
  const submitText = form.querySelector(".ct-submit__text");

  const MSG = {
    es: {
      required: "Por favor complete los campos obligatorios.",
      email: "Ingrese un correo electrónico válido.",
      name: "El nombre y el apellido solo pueden contener letras.",
      consent: "Debe autorizar el tratamiento de sus datos para continuar.",
      sending: "Enviando…",
      network: "No fue posible enviar su solicitud. Por favor inténtelo de nuevo o escríbanos a team@ozzcreativestudio.com.",
      success: "Gracias por escribirnos. Hemos recibido su solicitud y nos pondremos en contacto con usted a la brevedad.",
    },
    en: {
      required: "Please complete the required fields.",
      email: "Please enter a valid email address.",
      name: "First and last name can only contain letters.",
      consent: "You must authorize the processing of your data to continue.",
      sending: "Sending…",
      network: "We could not send your request. Please try again or write to team@ozzcreativestudio.com.",
      success: "Thank you for reaching out. We have received your request and will contact you shortly.",
    },
  };

  const lang = () => ((document.documentElement.lang || "es").slice(0, 2) === "en" ? "en" : "es");
  const t = (key) => MSG[lang()][key];

  const NAME_RE = /^[A-Za-zÀ-ÖØ-öø-ÿÑñ' -]{1,25}$/;
  const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  function setStatus(text, type) {
    status.textContent = text || "";
    status.classList.toggle("is-success", type === "success");
    status.classList.toggle("is-error", type === "error");
  }

  function markField(input, invalid) {
    const wrap = input.closest(".ct-field, .ct-consent");
    if (wrap) wrap.classList.toggle("is-invalid", invalid);
    input.setAttribute("aria-invalid", invalid ? "true" : "false");
  }

  function validate() {
    const f = form.elements;
    let firstError = null;
    let message = "";

    const check = (input, ok, msg) => {
      markField(input, !ok);
      if (!ok && !firstError) {
        firstError = input;
        message = msg;
      }
    };

    check(f.firstName, f.firstName.value.trim() !== "", t("required"));
    check(f.lastName, f.lastName.value.trim() !== "", t("required"));
    check(f.email, f.email.value.trim() !== "", t("required"));
    check(f.message, f.message.value.trim() !== "", t("required"));

    if (!firstError) {
      check(f.firstName, NAME_RE.test(f.firstName.value.trim()), t("name"));
      check(f.lastName, NAME_RE.test(f.lastName.value.trim()), t("name"));
      check(f.email, EMAIL_RE.test(f.email.value.trim()), t("email"));
    }

    check(f.accept, f.accept.checked, t("consent"));

    if (firstError) {
      setStatus(message, "error");
      firstError.focus();
      return false;
    }

    return true;
  }

  // Limpia el error del campo al corregirlo
  form.addEventListener("input", (e) => {
    if (e.target.matches(".ct-input, [name=accept]")) markField(e.target, false);
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (form.classList.contains("is-sending")) return;
    if (!validate()) return;

    const original = submitText.textContent;
    form.classList.add("is-sending");
    submitText.textContent = t("sending");
    setStatus("", null);

    const started = Date.now();

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: new FormData(form),
        credentials: "same-origin",
        headers: { Accept: "application/json" },
      });

      const data = await response.json();

      // Mínimo 1,2 s para que la barra de progreso se perciba
      const wait = Math.max(0, 1200 - (Date.now() - started));
      await new Promise((r) => setTimeout(r, wait));

      if (data.new_csrf_token) form.elements.csrf_token.value = data.new_csrf_token;

      if (data.ok) {
        form.reset();
        setStatus(data.message || t("success"), "success");
      } else {
        setStatus(data.message || t("network"), "error");
      }
    } catch (err) {
      console.error("[ContactForm]", err);
      setStatus(t("network"), "error");
    } finally {
      form.classList.remove("is-sending");
      submitText.textContent = original;
    }
  });

  // Si cambia el idioma con un mensaje visible, se limpia para no mezclar idiomas
  document.addEventListener("ozz:langchange", () => setStatus("", null));
})();
