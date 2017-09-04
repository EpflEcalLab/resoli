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
        const $inputLat = $(el).data('googleInputLat');
        const $inputLng = $(el).data('googleInputLng');
        const autocomplete = new google.maps.places.Autocomplete(el);

        autocomplete.addListener('place_changed', function() {
          var place = autocomplete.getPlace();

          let lat = '';
          let lng = '';
          if (place.geometry) {
            console.log(place.geometry.location);
            lat = place.geometry.location;
            lng = place.geometry.location;
          }

          $inputLat.val(lat);
          $inputLat.val(lng);
        });
      });
    }
  })(jQuery);
};

export default formControl;
