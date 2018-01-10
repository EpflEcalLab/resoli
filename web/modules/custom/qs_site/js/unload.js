(function ($, Drupal) {
  Drupal.behaviors.unload = {
    attach: function attach(context, settings) {
      // Warning
      $(window).on('beforeunload', function() {
        return "Any changes will be lost";
      });

      // Form Submit
      $(document).on("submit", "form", function(event) {
        // disable warning
        $(window).off('beforeunload');
      });
    },
    detach: function detach(context, settings, trigger) {
      if (trigger === 'unload') {
        $(window).off('beforeunload');
      }
    }
  };
})(jQuery, Drupal);
