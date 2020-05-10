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
  $(':checkbox').iCheck({
    checkboxClass: 'icheckbox_flat',
    radioClass: 'iradio_flat'
  });
});
