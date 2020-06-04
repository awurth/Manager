import '@fortawesome/fontawesome-free/css/all.css';
import '../scss/main.scss';

import 'icheck/skins/flat/flat.css';

import $ from 'jquery';
import 'icheck';
import Dropdown from './components/dropdown';
import LinkClickConfirmation from './components/link-click-confirmation';
import Select from './components/select';

new Dropdown('.dropdown');
new LinkClickConfirmation();
$(':checkbox,:radio').iCheck({
  checkboxClass: 'icheckbox_flat',
  radioClass: 'iradio_flat'
});

// Do not submit the search form unless the search query is at least 3 characters long
document.querySelector('#search_bar').addEventListener('submit', (event: Event) => {
  event.preventDefault();

  const form = event.currentTarget as HTMLFormElement;
  const input = form.querySelector('input[type="text"]') as HTMLInputElement;

  if (input.value.length > 2) {
    form.submit();
  }
});

document.querySelectorAll('select').forEach((select: HTMLSelectElement) => {
  new Select(select);
});
