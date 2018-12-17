const multiStep = () => {
  (function ($, Drupal) {
    const $form = $('.form-multistep');
    let processed = false;

    /**
     * Update the step description dynamically.
     *
     * @param step string
     *   The step ID to search for.
     * @param description string
     *   The description to replace by.
     */
    $.fn.updateStepDescription = function(step, description) {
      const currentForm = $(this);
      const $fieldsets = currentForm.find('[data-step]');

      const $lead = $fieldsets.closest(step).find('.lead');
      if ($lead.length <= 0) {
        return;
      }

      $lead.html(description);
    };

    function handleMultisteps() {
      $form.each(function() {
        const currentForm = $(this);
        const id = currentForm.attr('id');
        const modalHeader = currentForm.closest('.modal-content').find('.modal-header');
        const $fieldsets = currentForm.find('[data-step]');
        let nextTab = null;
        let prevTab = null;
        let currentTab = null;

        // Init the step nav above form
        const stepNav = $(`<ul class="step-nav nav nav-tabs col-sm-10 col-md-8 mx-auto" id="stepnav-${id}"></ul>`);

        currentForm.find('.js-hide-in-multistep').hide();

        if (modalHeader.length > 0) {
          stepNav.prependTo(modalHeader);
        } else {
          stepNav.prependTo(currentForm);
        }

        $('<div/>')
          .addClass('modal-footer justify-content-center sticky-bottom')
          .appendTo(currentForm.find(`.tab-content`));

        // Create the "Prev step" button below the form
        $('<button/>')
          .addClass(`btn btn-outline-invert btn-circle btn-icon-left align-self-center shadow-to-bottom prev-btn-${id}`)
          .html(typeof (Drupal) !== 'undefined' ? `<span class="d-none d-sm-inline">${Drupal.t('qs.previous')}</span>` : `<span class="d-none d-sm-inline">previous</span>`)
          .on('click', function(e) {
            e.preventDefault();

            // @TODO make it work: disable the nav if current tab pane has required and empty fields
            // if (checkRequired(currentTab)) {
            prevTab.tab('show');
            // };
          })
          .appendTo(currentForm.find(`.modal-footer`))
          .append(
            '<span class="icon" aria-hidden="true"><svg><use xlink:href="#icon-arrow-left"></use></svg></span>'
          );

        // Create the "Next step" button below the form
        $('<button/>')
          .addClass(`btn btn-outline-invert btn-icon btn-icon-right align-self-center shadow-to-bottom next-btn-${id}`)
          .text(typeof (Drupal) !== 'undefined' ? Drupal.t('qs.next') : 'next')
          .on('click', function(e) {
            e.preventDefault();
            // @TODO make it work: disable the nav if current tab pane has required and empty fields
            // if (checkRequired(currentTab)) {
            nextTab.tab('show');
            // };
          })
          .appendTo(currentForm.find(`.modal-footer`))
          .append(
            '<span class="icon" aria-hidden="true"><svg><use xlink:href="#icon-arrow"></use></svg></span>'
          );

        $(`#${id} .js-form-submit:not(.js-form-normal)`)
          .appendTo(currentForm.find(`.modal-footer`));

        // Add step nav at top of form.
        $fieldsets.each(function(index) {
          const currentFieldset = $(this);
          const $parent = currentFieldset.parents('.form-multistep');
          const stepLabel = currentFieldset.data('step');
          const fieldsetId = currentFieldset.attr('id');
          const nextFieldsetId = $(this).next('fieldset').attr('id');

          // Generate link to step
          const $link = $('<a/>', {
            'class': 'step-nav-link btn btn-outline-invert btn-circle shadow-to-bottom',
            'href': `#${fieldsetId}`,
            'title': stepLabel,
            'aria-label': stepLabel,
            'id': `steptab-${fieldsetId}`,
            'aria-controls': fieldsetId,
            'aria-selected': 'false',
            'data-toggle': 'tab',
            'data-last': index + 1 === $fieldsets.length ? 'true' : 'false',
            'role': 'tab',
          });

          // Append the step nav to the form
          $('<li/>')
            .addClass('step-nav-item')
            .appendTo(`#stepnav-${id}`)
            .append($link);
        });

        // show next tab on click
        $('a.step-nav-link').on('show.bs.tab', function(e) {
          const target = $(e.relatedTarget).attr('href');
          currentTab = target ? $(target) : currentForm.find('fieldset:first-of-type');
          nextTab = $(e.target).parent().next().find('a.step-nav-link');
          prevTab = $(e.target).parent().prev().find('a.step-nav-link');

          // Toggle buttons depending on current step
          if (prevTab.length <= 0) {
            $(`.prev-btn-${id}`).hide();
          } else {
            $(`.prev-btn-${id}`).show();
          }

          // Toggle buttons depending on current step
          if (nextTab.length <= 0) {
            $(`#${id} .js-form-submit`).show();
            $(`.next-btn-${id}`).hide();
          } else {
            $(`#${id} .js-form-submit`).hide();
            $(`.next-btn-${id}`).show();
          }
        });
      });

      // Show the first tab on load
      $('a.step-nav-link:first').tab('show');

      // Check if there are any errors in the form and display the corresponding tab
      const tabWithError = $form.find('input.is-invalid, .radios-wrapper.is-invalid').first().closest('.tab-pane').attr('id');

      if (tabWithError) {
        $(`a[href="#${tabWithError}"]`).tab('show');
      }
    }

    if ($form.length > 0 && !processed) {
      processed = true;
      handleMultisteps();
    }

    $(document).on('DOMNodeInserted', function() {
      if (!processed) {
        handleMultisteps();
      }
    });
  })(jQuery, Drupal);
};

export default multiStep;
