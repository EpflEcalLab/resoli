const formSubmit = () => {
  (function ($) {
    // On load remove every checked on data-submit.
    $('[data-submit]:checked').prop('checked', false);
    $('[data-submit]:checked').removeAttr('checked');

    const $checkboxes = $('[data-submit]');
    $checkboxes.on('change click', function(){
      const $form = $(this).parents('form');

      if ($form.length > 0) {
        $form.submit();
      }
    });
  })(jQuery);
};

export default formSubmit;
