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

      // Load the google map into the card-body.
      if (typeof($(this).prop('hash')) != 'undefined' && $(this).prop('hash').indexOf('#map-') == 0) {
        const event_id = $(this).prop('hash').replace('#map-', '');
        const map_container = $(`#map-container-${event_id}`).first();
        if (map_container.length > 0) {
          const lat   = map_container.data('lat');
          const lng   = map_container.data('lng');
          const label = map_container.data('label');
          const info = map_container.data('info');
          const latLng = {lat: lat, lng: lng};

          // Wait on anim. 100ms more than the CSS anim. to avoid issue.
          setTimeout(function () {
            const map = new google.maps.Map(document.getElementById(map_container.attr('id')), {
              center: latLng,
              zoom: 12,
              zoomControl: true,
              mapTypeControl: false,
              scaleControl: false,
              streetViewControl: false,
              rotateControl: false,
              fullscreenControl: false
            });

            const marker = new google.maps.Marker({
              position: latLng,
              map: map,
              title: label,
              animation: google.maps.Animation.DROP,
            });

            const markerinfo = new google.maps.InfoWindow({
              content: info,
            });

            marker.addListener('click', function() {
              markerinfo.open(map, marker);
            });
          }, 500);
        }
      }
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
        $card = $(hash).find('a');
      }

      // Always toggle the first card or the one from the URL on load
      $card.trigger('click');
      // $('html, body').animate({ scrollTop: $card.offset().top }, 200);
    }

  })(jQuery);
};

export default card;
