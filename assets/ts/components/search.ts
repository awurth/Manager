// Do not submit the search form unless the search query is at least 3 characters long
document.querySelector('#search_bar').addEventListener('submit', (event: Event) => {
  event.preventDefault();

  const form = event.currentTarget as HTMLFormElement;
  const input = form.querySelector('input[type="text"]') as HTMLInputElement;

  if (input.value.length > 2) {
    form.submit();
  }
});
