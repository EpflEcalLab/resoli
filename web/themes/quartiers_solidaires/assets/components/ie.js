const ie = () => {
  (function ($) {
    // Detect IE like a boss
    // https://stackoverflow.com/a/21660466/1722653
    const ua = navigator.userAgent;
    const doc = $('html');
    let ie = false;

    if ((ua.match(/MSIE 10.0/i))) {
      doc.addClass('ie10');
      ie = true;
    } else if ((ua.match(/rv:11.0/i))) {
      doc.addClass('ie11');
      ie = true;
    }

    // This will fix the flag icons
    if (ie) {
      const $flags_use = $('.flag use:last-child');

      $flags_use.each(function() {
        const transform = $(this).css('transform');
        if (transform !== 'none') {
          let values = transform.replace(/[^0-9\-.,]/g, '').split(',');
          const x = values[0];
          const y = values[3];

          $(this).attr('transform', `scale(${x},${y}) translate(15,15)`);
        }
      });
    }
  })(jQuery);
};

export default ie;
