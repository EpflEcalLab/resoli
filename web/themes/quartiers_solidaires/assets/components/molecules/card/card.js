const card = () => {
  (function ($) {
    // Toggle card body on card-pill header click
    $('.collapse').on('show.bs.collapse hide.bs.collapse', function(e) {
      $(this).parents('.card').toggleClass('card-open');

      // Make sure we close all flipped cards
      $('.flip').removeClass('flip');
      // And close back all collapse elements
      // const parent = $(this).attr('data-parent');
      // $(parent).find('.collapse').each(function() { $(this).collapse('hide'); });
    });

    // Swap the card to display the other side
    $('[data-toggle=flip]').on('click', function() {
      $(this).parents('.card-flippable').toggleClass('flip');
    });

  })(jQuery);
};

export default card;
