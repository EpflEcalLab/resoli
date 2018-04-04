import Stickyfill from 'stickyfilljs';

const floating = () => {
  (function ($) {
    let elements = $('.sticky-bottom, .sticky-top');
    Stickyfill.add(elements);

    $(document).on('DOMNodeInserted', function() {
      elements = $('.sticky-bottom, .sticky-top');
      Stickyfill.add(elements);
      Stickyfill.refreshAll();
    });

  })(jQuery);
};

export default floating;
