import '@fortawesome/fontawesome-free/css/all.css';
import '../scss/main.scss';

import 'select2/dist/css/select2.css';
import 'icheck/skins/flat/flat.css';

import $ from 'jquery';
import 'select2';
import 'icheck';
import Dropdown from './components/dropdown';
import LinkClickConfirmation from './components/link-click-confirmation';

document.addEventListener('DOMContentLoaded', () => {
  new Dropdown('.dropdown');
  new LinkClickConfirmation();
  $('select').select2();
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
});
