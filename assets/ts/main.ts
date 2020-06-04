import '@fortawesome/fontawesome-free/css/all.css';
import '../scss/main.scss';

import 'icheck/skins/flat/flat.css';

import $ from 'jquery';
import 'icheck';

import './components/search';
import Dropdown from './components/dropdown';
import LinkClickConfirmation from './components/link-click-confirmation';
import Select from './components/select';

new Dropdown('.dropdown');
new LinkClickConfirmation();
$(':checkbox,:radio').iCheck({
  checkboxClass: 'icheckbox_flat',
  radioClass: 'iradio_flat'
});

document.querySelectorAll('select').forEach((select: HTMLSelectElement) => {
  new Select(select);
});
