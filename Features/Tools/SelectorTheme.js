(function () {
  const root = document.documentElement;
  let lastHasSelection = false;

  function updateSelectionClass() {
    const sel = window.getSelection();
    const hasSelection = !!(sel && sel.toString().length > 0);

    if (hasSelection !== lastHasSelection) {
      lastHasSelection = hasSelection;
      if (hasSelection) {
        root.classList.add("selection-active");
      } else {
        root.classList.remove("selection-active");
      }
    }
  }

  document.addEventListener("selectionchange", updateSelectionClass);
  document.addEventListener("mouseup", updateSelectionClass);
  document.addEventListener("keyup", updateSelectionClass);
})();
