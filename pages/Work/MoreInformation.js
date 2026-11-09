document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.querySelector(".project-header");
  const projectInfo = document.querySelector(".project-info");
  const mediaGrid = document.querySelector(".media-grid");
  const projectSection = document.querySelector(".project-section");

  if (!toggleBtn || !projectInfo || !mediaGrid || !projectSection) return;

  const GRID_MS = 400;
  const FADE_MS = 400;
  const SAFETY = 30;
  const wait = (ms) => new Promise((res) => setTimeout(res, ms));

  let lastScrollY = 0;

  async function openPanel() {
    lastScrollY = window.scrollY;
    toggleBtn.classList.add("open");
    mediaGrid.classList.add("collapsed");
    await wait(GRID_MS + SAFETY);
    projectInfo.classList.add("open");

    // ⇩⇩ NUEVO: scroll al top de .project-section con offset del header sticky
    const headerOffset = (() => {
      const cs = getComputedStyle(toggleBtn);
      const topVal = parseFloat(cs.top);
      // Si el header no está sticky o top no es numérico, usamos 0
      return Number.isFinite(topVal) ? Math.max(0, topVal) : 0;
    })();

    const sectionTop =
      projectSection.getBoundingClientRect().top +
      window.pageYOffset -
      headerOffset;

    window.scrollTo({
      top: sectionTop < 0 ? 0 : sectionTop,
      behavior: "smooth",
    });
  }

  async function closePanel() {
    projectInfo.classList.remove("open");
    toggleBtn.classList.remove("open"); // ⬅️ quitamos "relative"
    await wait(FADE_MS + SAFETY);
    mediaGrid.classList.remove("collapsed");
    window.scrollTo({ top: lastScrollY, behavior: "smooth" });
  }

  toggleBtn.addEventListener("click", () => {
    if (projectInfo.classList.contains("open")) {
      closePanel();
    } else {
      openPanel();
    }
  });

  // 👇 Mostrar el header SOLO si .project-section está en viewport
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          toggleBtn.style.visibility = "visible";
          toggleBtn.style.opacity = "1";
        } else {
          toggleBtn.style.visibility = "hidden";
          toggleBtn.style.opacity = "0";
        }
      });
    },
    {
      root: null,
      threshold: 0, // con que entre un píxel ya cuenta como dentro
    }
  );

  observer.observe(projectSection);
});
