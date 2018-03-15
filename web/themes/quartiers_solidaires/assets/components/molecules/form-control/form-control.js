const formControl = () => {
  (function ($) {
    // Use to initialize inputs autocomplete only once.
    let initialized = false;

    /**
     *  Location Search by using Google Place Autocomplete.
     */
    function locationInitialize() {
      // Initialize inputs only once.
      if (initialized) {
        return;
      }
      initialized = true;

      const $inputs = $('input[data-google-autocomplete]');

      // Enable the Google Place API for each inputs.
      $inputs.each(function(i, el) {
        // We will store the user choosen place to check if before submit
        // he change the autocomplete value whitout choosing from the list
        // & never trigger the 'place_changed' event.
        let choosen = '';

        // Will store the initial value of this field, used when updating values.
        // Indeed, if the change the place for a custom one, we never fire the 'place_changed' event-
        let initial = $(el).val();

        const inputLat = $(el).data('googleInputLat');
        const inputLng = $(el).data('googleInputLng');
        const autocomplete = new google.maps.places.Autocomplete(el, {
          componentRestrictions: {country: 'ch'},
        });

        // Detect IE 11 to fire the initialization of google map.
        // stackoverflow.com/questions/21825157/internet-explorer-11-detection
        const isIE11 = !!window.MSInputMethodContext && !!document.documentMode;
        if (isIE11) {
          // Fix autocomplete position in Windows Phone 8.1. Also see CSS rules.
          // Wait until the autocomplete element is added to the document.
          // stackoverflow.com/questions/18259294/google-places-autocomplete-not-working-in-windows-mobile-ie-browser
          const fixEutocompleteInterval = window.setInterval(function(){
            const $container = $('body > .pac-container');
            if ($container.length == 0) return;
            // Move the autocomplete element just below the input.
            $container.appendTo($(el).parent());
            // Add parent as relative.
            $(el).parent().css('position', 'relative');
            // The fix is finished, stop working.
            window.clearInterval(fixEutocompleteInterval);
          }, 500);
        }

        /**
         * Cleanup the lat/lng fields when user chose custom place instead of Google one.
         */
        $(el).parents('form').on('submit', function() {
          // Get the latest google place selected by the user.
          const place = autocomplete.getPlace();

          // Check if the current input value is the same as previous google selected.
          // If different, cleanup the lat/lng value cause the user chose a custom address.
          if ((typeof place != 'undefined' && $(el).val() != choosen) || initial != '' && initial != $(el).val() && typeof place == 'undefined') {
            $(`input[data-drupal-selector="${inputLat}"]`).val('');
            $(`input[data-drupal-selector="${inputLng}"]`).val('');
          }
        });

        $(document).on('keypress', function(e) {
          if ($('#edit-venue') && e.which == '13') {
            e.preventDefault();
          }
        });

        autocomplete.addListener('place_changed', function() {
          const place = autocomplete.getPlace();
          choosen = $(el).val();
          let lat = '';
          let lng = '';

          if (place.geometry) {
            lat = place.geometry.location.lat();
            lng = place.geometry.location.lng();
          }

          $(`input[data-drupal-selector="${inputLat}"]`).val(lat);
          $(`input[data-drupal-selector="${inputLng}"]`).val(lng);
        });
      });
    }

    if (typeof google != 'undefined') {
      google.maps.event.addDomListener(window, 'load', locationInitialize);

      // Detect IE 11 to fire the initialization of google map.
      // stackoverflow.com/questions/21825157/internet-explorer-11-detection
      const isIE11 = !!window.MSInputMethodContext && !!document.documentMode;

      // Detect Edge.
      const ua = window.navigator.userAgent;
      const isEdge = ua.indexOf('Edge/');
      if (isIE11 || isEdge > 0) {
        locationInitialize();
      }
    }

    /**
     * Radios group, add class to buttons before the clicked one
     */
    function togglePrevRadios(el) {
      el.closest('label').prevAll().addClass('active-alt');
    }

    $(document).on('change', '.js-radio-group .form-radio', function() {
      // Remove all active-alt class
      $(this).closest('.js-radio-group').find('.active-alt').removeClass('active-alt');
      togglePrevRadios($(this));
    });

    togglePrevRadios($('.js-radio-group').find('.active'));
    // Workaround Bigpipe
    $(document).on('DOMNodeInserted', function() {
      togglePrevRadios($('.js-radio-group').find('.active'));
    });

    /**
     * Improvements to the file input
     */
    function humanFileSize(bytes, si) {
      var thresh = si ? 1000 : 1024;
      if(Math.abs(bytes) < thresh) {
        return bytes + ' B';
      }
      var units = si
        ? ['kB','MB','GB','TB','PB','EB','ZB','YB']
        : ['KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];
      var u = -1;
      do {
        bytes /= thresh;
        ++u;
      } while(Math.abs(bytes) >= thresh && u < units.length - 1);
      return bytes.toFixed(1)+' '+units[u];
    }

    /**
     * Files upload
     */
    $(document).on('change', '.form-control-file input', function() {
      const files = $(this)[0].files;
      const $list = $(this).parent().next('.form-control-files-list');

      // Check the files list exists.
      if ($list.length === 0) {
        return;
      }

      // reset list content before continuing.
      $list.html('');

      if (files.length > 0) {
        const filesList = $('<ul />');
        for (let i = 0; i < files.length; i++) {
          filesList.append(`<li>${files[i].name} — <strong>${humanFileSize(files[i].size)}</strong></li>`);
        }
        $list.append(filesList)
      }
    });

    // Workaround Bigpipe.
    $(document).on('DOMNodeInserted', function() {
      locationInitialize();
    });

  })(jQuery);
};

export default formControl;
