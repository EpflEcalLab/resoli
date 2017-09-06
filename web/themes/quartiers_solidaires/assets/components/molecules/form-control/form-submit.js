const formSubmit = () => {
  (function ($) {
    const $checkboxes = $('input[type=checkbox][data-submit=checkbox]');
    $checkboxes.change(function(){
      const $form = $(this).parents('form');

      if ($form.length > 0) {
        $form.submit();
      }
    });
  })(jQuery);
};

export default formSubmit;
