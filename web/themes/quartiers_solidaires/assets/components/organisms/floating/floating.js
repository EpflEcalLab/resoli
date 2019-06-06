import Stickyfill from 'stickyfilljs';

const floating = () => {
  (function ($) {
    let elements = $('.sticky-bottom, .sticky-top');
    Stickyfill.add(elements);

    $(document).on('DOMNodeInserted', function(e) {
      // Resticky only new elements of inserted target or himself.
      const els = $(e.target).find('.sticky-bottom, .sticky-top').addBack('.sticky-bottom, .sticky-top');
      Stickyfill.add(els);

      // Refresh only newly added sticky element.
      if (els.length > 0) {
        for (let i = 0; i <= els.length; i++) {
          try {
            const sticky = new Stickyfill.Sticky(els[i]);
            sticky.refresh();
          } catch (e) {
            console.log(e);
          }
        }
      }
    });

  })(jQuery);
};

export default floating;
