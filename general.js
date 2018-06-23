window.onload = function() {
  const clickSelector = document.querySelectorAll(
    ".beans-hook input[type=text]"
  );
  for (let i = 0; i < clickSelector.length; i++) {
    clickSelector[i].addEventListener("click", function() {
      this.select();
    });
  }
};
