const card = () => {
  (function ($) {
    // Toggle card body on card-pill header click
    $(document).on('show.bs.collapse hide.bs.collapse', function(e) {
      $(e.target).parents('.card').toggleClass('card-open');

      // Make sure we close all flipped cards
      $('.flip').removeClass('flip');
      // And close back all collapse elements
      // const parent = $(this).attr('data-parent');
      // $(parent).find('.collapse').each(function() { $(this).collapse('hide'); });
    });

    // Swap the card to display the other side
    $('[data-toggle=flip]').on('click', function() {
      const $card = $(this).parents('.card-flippable');

      if ($card.length > 0) {
        // $('html, body').animate({ scrollTop: $card.offset().top - 100 }, 200);
        $card.toggleClass('flip');
      }

      // Load the google map into the card-body.
      if (typeof($(this).prop('hash')) != 'undefined' && $(this).prop('hash').indexOf('#map-') == 0) {
        const event_id = $(this).prop('hash').replace('#map-', '');
        const map_container = $(`#map-container-${event_id}`).first();
        if (map_container.length > 0) {
          const lat   = parseFloat(map_container.data('lat'));
          const lng   = parseFloat(map_container.data('lng'));
          const label = map_container.data('label');
          const info = map_container.data('info');
          const latLng = {lat: lat, lng: lng};

          // Wait on anim. 100ms more than the CSS anim. to avoid issue.
          setTimeout(function () {
            const map = new google.maps.Map(document.getElementById(map_container.attr('id')), {
              center: latLng,
              zoom: 17,
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

    $('.card .collapse').on('show.bs.collapse', function(e) {

      // Update the hash in URL on collapse
      const id = $(e.currentTarget).attr('id');
      if (id) {
        const card_id = id.replace('collapse-', '');

        if (history.pushState) {
          history.replaceState(null, null, `#card${card_id}`);
        } else {
          window.location.hash = `card${card_id}`;
        }
      }

      // Collapse all the other collapses on the page
      const parent = $(e.currentTarget).data('parent');
      if (parent && parent.match("^#events-accordion")) {
        $.each($('[id*=events-accordion]'), function() {
          const $collapse = $(this).find('.collapse');

          if ('#' + $collapse.attr('id') === parent) {
            return;
          }

          $collapse.collapse('hide');
        });
      }
    });

    // Get the hash in the URL
    const hash = window.location.hash;

    let $card = '';
    let triggered = false;
    function onReady() {
      const $pills = $(`.card-pill[data-toggle=collapse]`);
      if ($pills.length > 0 && !triggered) {
        triggered = true;
        if (hash && hash.includes('card')) {
          const newHash = hash.replace('card', 'collapse-');
          $card = $(document).find(`.card-pill[data-toggle=collapse][href="${newHash}"]`);
        } else {
          $card = $('.card:first .card-pill[data-toggle=collapse]');
        }

        if ($card.length > 0) {
          // Always toggle the first card or the one from the URL on load
          $card.trigger('click');
          // const top = $card.offset().top;
          // $('html, body').animate({ scrollTop: top }, 200);
        }
      }
    }

    // Make sure the card is unflipped when clicking on the back button
    // see issue #520
    $(window).on('hashchange', function() {
      const hash = window.location.hash;
      if (hash && hash.includes('card')) {
        $('.flip').removeClass('flip');
      }
    });

    // Workaround Bigpipe
    $(document).on('DOMNodeInserted', function() {
      onReady();
    });

  })(jQuery);
};

export default card;
