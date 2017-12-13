const modal = () => {
  (function ($) {
    $(window).on('load', function() {
      const $modalFooters = $('.modal-body .modal-footer:not(.cloned-footer)');

      $modalFooters.each(function() {
        const _this = $(this);
        const clone = _this.clone(true);
        // Append the modal-footer after the modal-body
        _this.addClass('cloned-footer d-none').removeClass('row').closest('.modal-body').after(clone);

        // Send the form when clicking the submit only if it's now outside the form.
        clone.on('click', '[type=submit]', function() {
          // We have to clone & click instead of form.submit to make it works
          // with Drupal multi-submit handlers.
          if (clone.parent('form').length == 0) {
            clone.closest('.modal').find(`form #${$(this).attr('id')}`).click();
          }
        })
      });
    });
  })(jQuery);
};

export default modal;

