import Stickyfill from 'stickyfilljs';

const floating = () => {
  (function ($) {
    let elements = $('.sticky-bottom, .sticky-top');
    Stickyfill.add(elements);

    $(document).on('DOMNodeInserted', function(e) {
      // Resticky only new elements of inserted target or himself.
      const elements = $(e.target).find('.sticky-bottom, .sticky-top').addBack('.sticky-bottom, .sticky-top');
      Stickyfill.add(elements);

      // Refresh only newly added sticky element.
      if (elements.length > 0) {
        elements.forEach(element => {
          const sticky = new Stickyfill.Sticky(element);
          sticky.refresh();
        });
      }
    });

  })(jQuery);
};

export default floating;
