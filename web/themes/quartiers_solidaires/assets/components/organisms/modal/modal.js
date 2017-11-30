const modal = () => {
  (function ($) {
    $(document).on('DOMNodeInserted', function() {
      const $modalFooters = $('.modal-body .modal-footer');

      $modalFooters.each(function() {
        $(this).closest('.modal-body').after($(this));
      });
    });
  })(jQuery);
};

export default modal;

