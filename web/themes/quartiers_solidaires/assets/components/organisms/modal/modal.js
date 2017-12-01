const modal = () => {
  (function ($) {
    $(document).on('DOMNodeInserted', function() {
      const $modalFooters = $('.modal-body .modal-footer');

      $modalFooters.each(function() {
        const _this = $(this);
        // Append the modal-footer after the modal-body
        _this.closest('.modal-body').after(_this);

        // Send the form when cilcking the submit (it's now outside the form)
        _this.on('click', '[type=submit]', function() {
          _this.closest('.modal').find('form').submit();
        })
      });
    });
  })(jQuery);
};

export default modal;

