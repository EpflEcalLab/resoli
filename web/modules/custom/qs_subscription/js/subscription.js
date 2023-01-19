"use strict";

/**
 * @file
 * Confirm modal dialog for specific ajax submits.
 * See https://www.drupal.org/project/ajax_confirm
 */
(function ($, Drupal) {
  var timer = null;
  var delay = 2500;

  /**
   * Attaches the confirm dialog to all enabled elements for ajax confirmation.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the ajax confirmation behaviors.
   * @prop {Drupal~behaviorDetach} detach
   *   Detaches the autocomplete behaviors.
   */
  Drupal.behaviors.subscriptionConfirm = {
    attach: function attach(context, settings) {

      /**
       * Defer the confirmation step.
       *
       * @param $el
       *   The dom element.
       * @returns {*}
       *   Return the Promise.
       */
      function confirmDeferred($el) {
        // This is pretty much the same as the form-ajax.js script.
        var defer = $.Deferred();
        var text = $el.text();
        var $icon = $el.find('.icon').clone();
        var confirmText = $el.attr('data-confirm'); // Send request if we are in pending state

        if ($el.data('pending')) {
          window.clearTimeout(timer);
          $el.removeData('pending');
          defer.resolve('yes');
          return defer.promise();
        } // Set the pending state.


        $el.text(confirmText).prepend($icon).data('confirm', text).data('pending', 'true').addClass('btn-confirm'); // Set a timeout, after which we cancel the demand.

        timer = window.setTimeout(function () {
          $el.text(text).prepend($icon).data('confirm', confirmText).removeData('pending').removeClass('btn-confirm');
          defer.resolve('no');
        }, delay);
        return defer.promise();
      }

      /**
       * Returns the ajax instance corresponding to an element.
       *
       * @param element
       *   The element for which to return its ajax instance.
       *
       * @returns {Drupal.Ajax | null}
       *   The ajax instance if found, otherwise null.
       */
      function findAjaxInstance(element) {
        var ajax = null;
        var selector = "#".concat(element.id);

        for (var index in Drupal.ajax.instances) {
          var ajaxInstance = Drupal.ajax.instances[index];

          if (ajaxInstance && ajaxInstance.selector === selector) {
            ajax = ajaxInstance;
            break;
          }
        }

        return ajax;
      }

      /**
       * Confirm the subscription, avoid sending form if not ready yet.
       */
      function subscriptionConfirm(context, settings) {
        if (typeof settings.subscriptionConfirm !== 'undefined') {
          $(context).find('[data-confirm]').each(function () {
            var ajax = findAjaxInstance(this);
            var $this = $(this);

            if (ajax) {
              // Store the original beforeSend function, which will be called
              // if the user confirms the action.
              ajax.options.originalBeforeSend = ajax.options.beforeSend; // Overwrite the original beforeSend function, so that we first
              // interrupt the ajax submit and then show confirmation button and
              // if the user confirms the action then the ajax action will be
              // triggered again and the original beforeSend function will be
              // called.

              ajax.options.beforeSend = function (xmlhttprequest, options) {
                if (!ajax.alreadyConfirmed) {
                  // Wait for an user input and if desired trigger the ajax
                  // submission again but flag the ajax object so that the
                  // next time we do not interrupt the submission.
                  confirmDeferred($this, settings.subscriptionConfirm).then(function (answer) {
                    if (answer === 'yes') {
                      ajax.alreadyConfirmed = true;
                      $(ajax.element).trigger(ajax.elementSettings.event);
                    }
                  }); // Interrupt the ajax submission.

                  ajax.ajaxing = false;
                  return false;
                }

                ajax.alreadyConfirmed = false;
                return null;
              };
            }
          });
        }
      }

      subscriptionConfirm(context, settings);
    }
  };
})(jQuery, Drupal);
