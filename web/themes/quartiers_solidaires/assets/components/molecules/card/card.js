const card = () => {
  (function ($) {
    // Toggle card body on card-pill header click
    $('.collapse').on('show.bs.collapse hide.bs.collapse', function(e) {
      $(this).parents('.card').toggleClass('card-open');
    });
  })(jQuery);
};

export default card;
