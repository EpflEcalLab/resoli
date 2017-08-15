// You will use that file to import all your scripts
// Ex: import gallery from './gallery'
import svgIcons from '../icons/svg-icons';
import nav from 'molecules/nav/nav';
import card from 'molecules/card/card';

svgIcons();

$(document).ready(function() {
  card();
  nav();
});
