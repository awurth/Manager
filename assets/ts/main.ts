import '@fortawesome/fontawesome-free/css/all.css'
import '../scss/main.scss';

import Dropdown from './dropdown';

document.addEventListener('DOMContentLoaded', () => {
  new Dropdown('.dropdown');
});
