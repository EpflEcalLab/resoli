const formControl = () => {
  (function ($) {
    if (typeof google != 'undefined') {
      google.maps.event.addDomListener(window, 'load', locationInitialize);
    }

    /**
     *  Location Search by using Google Place Autocomplete.
     */
    function locationInitialize() {
      const $inputs = $('input[data-google-autocomplete]');

      // Enable the Google Place API for each inputs.
      $inputs.each(function(i, el) {
        const inputLat = $(el).data('googleInputLat');
        const inputLng = $(el).data('googleInputLng');
        const autocomplete = new google.maps.places.Autocomplete(el, {
          componentRestrictions: {country: 'ch'},
        });

        autocomplete.addListener('place_changed', function() {
          var place = autocomplete.getPlace();
          let lat = '';
          let lng = '';

          if (place.geometry) {
            lat = place.geometry.location.lat();
            lng = place.geometry.location.lng();
          }

          jQuery(`input[data-drupal-selector="${inputLat}"]`).val(lat);
          jQuery(`input[data-drupal-selector="${inputLng}"]`).val(lng);
        });
      });
    }

    /**
     * Radios group, add class to buttons before the clicked one
     */

    function togglePrevRadios(el) {
      el.closest('label').prevAll().addClass('active-alt');
    }

    $('.js-radio-group').on('change', '.form-radio', function() {
      // Remove all active-alt class
      $(this).closest('.js-radio-group').find('.active-alt').removeClass('active-alt');
      togglePrevRadios($(this));
    });

    togglePrevRadios($('.js-radio-group').find('.active'));

  })(jQuery);
};

export default formControl;
