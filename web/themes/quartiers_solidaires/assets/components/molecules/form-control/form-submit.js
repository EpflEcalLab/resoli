const formSubmit = () => {
  (function ($) {
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
