const linkTypeField = document.querySelector('.link-link-type') as HTMLSelectElement;
const uriField = document.querySelector('.link-uri') as HTMLInputElement;

let edited = !!uriField.value;
uriField.addEventListener('keyup', () => {
  edited = !!uriField.value;
});

linkTypeField.addEventListener('change', () => {
  const selectedOption = linkTypeField.selectedOptions.item(0);
  const uriPrefix = selectedOption.dataset?.uriPrefix;

  if (uriPrefix && !edited) {
    uriField.value = uriPrefix;
  }
});
