const calendar = () => {
  (function ($) {
    const $calendar = $('.calendar');

    // Onload, focus active element
    $calendar.find('.calendar-item.active').focus();

    // Handle different keydown events in calendar
    $(document).on('keydown', '.calendar-item', function(e) {
      const index = $(e.currentTarget).index();

      switch (e.keyCode) {
        // left = 37
        case 37:
          select(index - 1);
          break;
        // up = 38
        case 38:
          select(index - 7);
          break;
        // right = 39
        case 39:
          select(index + 1);
          break;
        // down = 40
        case 40:
          select(index + 7);
          break;
        // enter = 13
        // space = 32
        case 13:
        case 32:
          $(e.currentTarget).trigger('click');
          break;
      }
    });

    // Focus the new index on keydown
    const select = new_index => {
      $calendar.find('.calendar-item').eq(new_index).focus();
    }

  })(jQuery);
};

export default calendar;
