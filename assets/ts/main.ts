import '@fortawesome/fontawesome-free/css/all.css'
import '../scss/main.scss';

import 'select2/dist/css/select2.css';

import $ from 'jquery';
import 'select2';
import Dropdown from './components/dropdown';

document.addEventListener('DOMContentLoaded', () => {
  new Dropdown('.dropdown');
  $('select').select2();
});
