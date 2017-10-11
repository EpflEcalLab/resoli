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

    // Update the hash in URL on collapse
    $('.card .collapse').on('show.bs.collapse', function(e) {
      const card_id = $(e.currentTarget).attr('id').replace('collapse-', '');
      window.location.hash = `card${card_id}`;
    });

    // Get the hash in the URL
    const hash = window.location.hash;

    let $card = $('.card:first .card-pill[data-toggle=collapse]');

    if ($card.length) {
      if (hash && hash.includes('card') && $(hash).length) {
        $card = $(hash);
      }

      // Always toggle the first card or the one from the URL on load
      $card.find('a').trigger('click');
      // $('html, body').animate({ scrollTop: $card.offset().top }, 200);
    }

  })(jQuery);
};

export default card;
