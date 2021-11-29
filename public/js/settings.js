function showHideInput() {
  let checkbox = document.getElementById('limitCheckBox');
  let inputValue = document.getElementById('limitInputValue');
  inputValue.style.display = checkbox.checked ? "block" : "none";
}