// You will use that file to import all your scripts
// Ex: import gallery from './gallery'
import svgIcons from '../icons/svg-icons';
import nav from 'molecules/nav/nav';
import card from 'molecules/card/card';
import checkbox from 'molecules/checkbox/checkbox';
import multiStep from 'organisms/multi-step';
import formControl from 'molecules/form-control/form-control';
import formAjax from 'molecules/form-control/form-ajax';
import formSubmit from 'molecules/form-control/form-submit';

svgIcons();

(function($) {
  $(document).ready(function() {
    card();
    nav();
    multiStep();
    formControl();
    formAjax();
    formSubmit();
    checkbox();
  });
})(jQuery);
