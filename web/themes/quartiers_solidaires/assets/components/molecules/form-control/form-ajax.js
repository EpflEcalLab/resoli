const formAjax = () => {
  (function ($) {

    const $forms = $('form[data-ajax="true"]');

    $forms.on('submit', function(e) {
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
                console.log($parent);
                if ($parent.length > 0) {
                  $parent.toggleClass('card-fadeout');
                }
                break;
            }

            console.log('Submission was successful.');
            console.log(data);
          },
          error: function (data) {
              console.error('An error occurred.');
              console.log(data);
          },
      });
    });
  })(jQuery);
};

export default formAjax;
