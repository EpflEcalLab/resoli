const checkbox = () => {
  (function ($) {
    // Listen to click events on the ALL button
    // Always toggle all other checkboxes
    $(document).on('click', '#js-all-themes', function() {
      toggleSelected(true);
    });

    // We can't listen to `change` event as it would trigger every time
    $(document).on('click', '.js-filter-themes .btn.checkbox', function() {

      // whether the clicked element will be checked
      const selfChecked = !$(this).find('input[type=checkbox]').prop('checked');
      // how many options are currently checked
      const checkedOptions = $('.js-filter-themes .btn.checkbox').find('input[type=checkbox]:checked');
      // total of checked options after the change event triggers
      const counter = selfChecked ? checkedOptions.length + 1 : checkedOptions.length - 1;

      toggleAllBtn(counter);
    });

    const toggleAllBtn = (counter) => {
      // If the ALL button is active and some other themes are selected,
      // uncheck the ALL button
      if ((counter > 0 && $('#js-all-themes').hasClass('active'))) {
        $('#js-all-themes').removeClass('active');
      } else if (counter === 0) {
        $('#js-all-themes').addClass('active');
      }
    }

    const toggleSelected = (state) => {
      // Uncheck all checkboxes when we click on the ALL button
      $('.js-filter-themes .btn.checkbox').each(function() {
        const checkbox_checked = $(this).find('input[type=checkbox]').prop('checked');
        if (checkbox_checked === state) {
          $(this).button('toggle');
        }
      });
    }

    // We need to check on load if the button has to be enabled
    // (Workaround BigPipe)
    $(document).on('DOMNodeInserted', function() {
      const checboxesLength = $('.js-filter-themes .btn.checkbox').find('input[type=checkbox]:checked').length;
      if ($('.js-filter-themes').length > 0) {
        toggleAllBtn(checboxesLength);
      }
    });

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
