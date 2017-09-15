const checkbox = () => {
  (function ($) {
    // When clicking the All Themes button, we want to enable / disable ALL the
    // themes.
    const $btn_check_all = $('#js-all-themes');
    const $themes = $('.js-filter-themes .btn.checkbox');

    if ($btn_check_all.length > 0) {
      // Listen to click events on the ALL button
      // Always toggle all other checkboxes
      $btn_check_all.on('click', function() {
        toggleSelected(true);
      });

      // We can't listen to `change` event as it would trigger every time
      $themes.on('click', function() {

        // wether the clicked element will be checked
        const selfChecked = !$(this).find('input[type=checkbox]').prop('checked');
        // how many options are currently checked
        const checkedOptions = $themes.find('input[type=checkbox]:checked');
        // total of checked options after the change event triggers
        const counter = selfChecked ? checkedOptions.length + 1 : checkedOptions.length - 1;

        toggleAllBtn(counter);
      });

      const toggleAllBtn = (counter) => {
        // If the ALL button is active and some other themes are selected,
        // uncheck the ALL button
        if ((counter > 0 && $btn_check_all.hasClass('active')) || counter === 0) {
          $btn_check_all.toggleClass('active');
        }
      }

      const toggleSelected = (state) => {
        // Uncheck all checkboxes when we click on the ALL button
        $themes.each(function() {
          const checkbox_checked = $(this).find('input[type=checkbox]').prop('checked');
          if (checkbox_checked === state) {
            $(this).button('toggle');
          }
        });
      }

      // We need to check on load if the button has to be enabled
      toggleAllBtn($themes.find('input[type=checkbox]:checked').length);
    }


    // Very simple checbox toggle
    // Enable by pressing space
    $(document).on('keydown', '.checkbox-toggle-btn', function(e) {
      if (e.keyCode === 32) {
        $(this).trigger('click');
      }
    })
  })(jQuery);
}

export default checkbox;
