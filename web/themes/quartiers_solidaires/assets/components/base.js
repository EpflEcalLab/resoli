// You will use that file to import all your scripts
// Ex: import gallery from './gallery'
import svgIcons from '../icons/svg-icons';
import nav from 'molecules/nav/nav';
import card from 'molecules/card/card';
import button from 'atoms/button/button';
import checkbox from 'molecules/checkbox/checkbox';
import multiStep from 'organisms/multi-step/multi-step';
import formControl from 'molecules/form-control/form-control';
import formAjax from 'molecules/form-control/form-ajax';
import formSubmit from 'molecules/form-control/form-submit';
import formSelectize from 'molecules/form-control/form-selectize';
import formComment from 'molecules/form-control/form-comment';
import calendar from 'molecules/calendar/calendar';
import modal from 'organisms/modal/modal';
import floating from 'organisms/floating/floating';
import ie from 'ie';

svgIcons();

(function($) {
  $(document).ready(function() {
    card();
    nav();
    multiStep();
    formControl();
    formAjax();
    formSubmit();
    formSelectize();
    calendar();
    checkbox();
    button();
    modal();
    formComment();
    ie();
    floating();
  });
})(jQuery);
