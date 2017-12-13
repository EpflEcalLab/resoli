const button = () => {
  (function ($) {
    // Swap the card to display the other side
    $(document).on('keydown', '[data-toggle=buttons] .btn', function (e) {
      if (e.keyCode === 32) {
        $(this).button('toggle');
      }
    });

    // Add active class to button groups inputs
    $('[data-toggle=buttons] input[checked=checked]').each(function() {
      $(this).parents('.btn').addClass('active');
    });

  })(jQuery);
};

export default button;
