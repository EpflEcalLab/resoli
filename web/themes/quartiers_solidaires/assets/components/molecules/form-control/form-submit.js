const formSubmit = () => {
  (function ($) {
    const $checkboxes = $('form[data-submit="checkbox"] input[type=checkbox]');
    $checkboxes.change(function(){
      const $form = $(this).parents('form');

      if ($form.length > 0) {
        $form.submit();
      }
    });
  })(jQuery);
};

export default formSubmit;
