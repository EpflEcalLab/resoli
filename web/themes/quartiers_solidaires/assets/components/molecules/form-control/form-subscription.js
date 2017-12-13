const formSubscription = () => {
  (function ($) {
    const $subscriptionButtons = $('.js-btn-subscription');

    if ($subscriptionButtons.length > 0) {
      $(document).ajaxSuccess(function( event, xhr, settings ) {
        const response = xhr.responseJSON;
        if (settings.url.indexOf('subscriptions/request') !== -1 && response.status) {
          const id = response.subscription.entity[0].value;
          $(`#subscribe${id}`)
            .addClass('btn-secondary')
            .removeClass('btn-outline-secondary btn-white');
        }
      });
    }

  })(jQuery);
};

export default formSubscription;
