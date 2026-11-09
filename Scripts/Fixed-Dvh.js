let lastWidth = window.innerWidth;

function setFixedDvh() {
  const temp = document.createElement("div");
  temp.style.position = "absolute";
  temp.style.top = "0";
  temp.style.left = "0";
  temp.style.width = "0";
  temp.style.height = "100dvh";
  temp.style.visibility = "hidden";
  document.body.appendChild(temp);

  const real100dvh = temp.getBoundingClientRect().height;
  document.documentElement.style.setProperty("--fixed-dvh", `${real100dvh}px`);
  temp.remove();
}

window.addEventListener("load", setFixedDvh);

window.addEventListener("resize", () => {
  const currentWidth = window.innerWidth;
  if (currentWidth !== lastWidth) {
    setFixedDvh();
    lastWidth = currentWidth;
  }
});
