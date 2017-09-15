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
          $(`#${inputLat}`).val(lat);
          $(`#${inputLng}`).val(lng);
        });
      });
    }
  })(jQuery);
};

export default formControl;
