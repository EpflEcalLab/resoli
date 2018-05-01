const formAjax = () => {
  (function ($) {

    // Handle buttons with confirmation step
    let timer = null;
    const delay = 2500;

    $('form[data-ajax="true"]').on('click', '[type="submit"][data-confirm]', function(e) {
      const $this = $(this);
      const text = $this.text();
      const $icon = $this.find('.icon').clone();
      const confirmText = $this.data('confirm');

      // Send request if we are in pending state
      if ($this.data('pending')) {
        window.clearTimeout(timer);
        $this
          .removeData('pending');
        return true;
      }

      $this
        .text(confirmText)
        .prepend($icon)
        .data('confirm', text)
        .data('pending', 'true')
        .addClass('btn-confirm');

      timer = window.setTimeout(function() {
        $this
          .text(text)
          .prepend($icon)
          .data('confirm', confirmText)
          .removeData('pending')
          .removeClass('btn-confirm');
      }, delay);

      return false;
    });

    $(document).on('submit', 'form[data-ajax="true"]', function(e) {
      e.preventDefault();
      const $this = $(this);

      $.ajax({
          type: $this.attr('method'),
          url:  $this.attr('action'),
          data: $this.serialize(),
          success: function (data) {
            const behavior = $this.data('behavior');

            switch (behavior) {
              case 'fadeout-parent':
                const $parent = $(`#${$this.data('parent')}`);
                if ($parent.length > 0) {
                  // When animation is finished, remove element from DOM.
                  $parent.on('animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd', function(e) {
                    if ($(this).hasClass('card-fadeout')) {
                      $(this).remove();
                    }
                    $(this).off(e);
                  });
                  // Toggle animation.
                  $parent.toggleClass('card-fadeout');
                }
                break;
            }

          },
          error: function (data) {
              console.error('An error occurred.', data);
          },
      });
    });
  })(jQuery);
};

export default formAjax;
