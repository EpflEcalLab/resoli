export const layout = () => {
  /* Enable active states on all of the elements.
  This can be a bit slow on huge pages so it might be worth restricting it to certain elements instead of document in the future.
  */
  document.addEventListener('touchstart', function(){}, true);
};

export const preventAutoScroll = () => {
  const scrollToTop = function () {
    window.scrollTo(0, 0);
  };

  if (window.location.hash) {
    // handler is executed at most once
    $(window).one('scroll', scrollToTop);
  }

  // make sure to release scroll 1 second after document readiness
  // to avoid negative UX
  $(function () {
    window.onload = function () {
      $(window).off('scroll', scrollToTop);

      const hash = window.location.hash;
      if (hash && $(hash).length > 0) {
        const offset = $(hash).offset().top - 100;
        $('html, body').animate({ scrollTop: offset });
      }
    };
  });
};

export default layout;
