document.addEventListener("DOMContentLoaded", async () => {
  const fonts = [
    "'Nabla'", // Experimental multicolor
    "'Monoton'", // Neón retro
    "'Rubik Glitch'", // Distorsión digital / glitch
    "'Protest Guerrilla'", // Urbano / cartel
    "'Limelight'", // Art Déco / Broadway
    "'Turret Road'", // Geométrica arquitectónica
  ];

  /**
   * Espera hasta que exista un elemento en el DOM.
   */
  function waitForElement(selector) {
    return new Promise((resolve) => {
      const check = () => {
        const element = document.querySelector(selector);

        if (element) {
          resolve(element);
        } else {
          requestAnimationFrame(check);
        }
      };

      check();
    });
  }

  try {
    // Esperar a que todas las fuentes del documento estén listas
    await document.fonts.ready;

    // Forzar la carga de las fuentes que vamos a utilizar
    await Promise.all([
      document.fonts.load("1em Nabla"),
      document.fonts.load("1em Orbitron"),
      document.fonts.load("1em 'Zen Tokyo Zoo'"),
      document.fonts.load("1em Ephesis"),
      document.fonts.load("1em 'Bona Nova SC'"),
      document.fonts.load("1em 'Varela Round'"),
    ]);

    // Esperar a que Reveal haya creado las líneas
    const targetLine = await waitForElement(
      ".hero-title .reveal-line:nth-child(2) span",
    );

    let currentindex = 0;

    // Aplicar inmediatamente la primera fuente
    targetLine.style.fontFamily = fonts[currentindex];

    // Opcional: suaviza ligeramente el renderizado
    targetLine.style.fontKerning = "normal";
    targetLine.style.fontVariantLigatures = "common-ligatures";
    targetLine.style.willChange = "font-family";

    currentindex++;

    // Cambio continuo
    setInterval(() => {
      targetLine.style.fontFamily = fonts[currentindex];

      currentindex++;

      if (currentindex >= fonts.length) {
        currentindex = 0;
      }
    }, 1200);
  } catch (error) {
    console.error("Error cargando las fuentes:", error);
  }
});
