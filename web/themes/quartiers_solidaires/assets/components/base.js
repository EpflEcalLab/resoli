// You will use that file to import all your scripts
// Ex: import gallery from './gallery'
import svgIcons from '../icons/svg-icons';
import nav from 'molecules/nav/nav';
import card from 'molecules/card/card';
import multiStep from 'organisms/multi-step';
import formControl from 'molecules/form-control/form-control';

svgIcons();

(function($) {
  $(document).ready(function() {
    card();
    nav();
    multiStep();
    formControl();
  });
})(jQuery);
