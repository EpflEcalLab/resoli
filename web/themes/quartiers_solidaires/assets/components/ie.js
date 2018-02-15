const ie = () => {
  (function ($) {
    // This will fix the flag icons
    const $flags_use = $('.flag use:last-child');

    $flags_use.each(function() {
      const transform = $(this).css('transform');
      if (transform !== 'none') {
        let values = transform.replace(/[^0-9\-.,]/g, '').split(',');
        const x = values[0];
        const y = values[3];

        // origin x
        values[4] = x * 3;
        // origin y
        values[5] = y * 3;


        $(this).attr('transform', `matrix(${values.join(',')})`);
        console.log(`matrix(${values.join(',')})`);
      }
    });

    // Detect IE like a boss
    // https://stackoverflow.com/a/21660466/1722653
    const ua = navigator.userAgent;
    const doc = $('html');

    if ((ua.match(/MSIE 10.0/i))) {
      doc.addClass('ie10');
    } else if ((ua.match(/rv:11.0/i))) {
      doc.addClass('ie11');
    }
  })(jQuery);
};

export default ie;
