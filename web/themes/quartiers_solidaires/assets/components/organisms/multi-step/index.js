const checkRequired = (el) => {
  // Forbid opening the tab if some fields are empty and required
  // in the previous fieldset
  return el.find('input[required]').val() !== '';
}

const multiStep = () => {
  (function ($) {
    const $form = $('.form-multistep');

    if ($form.length > 0) {
      $form.each(function() {
        const id = $(this).attr('id');
        const currentForm = $(this);
        const $fieldsets = currentForm.find('fieldset');
        let nextTab = null;
        let currentTab = null;

        // Init the step nav above form
        $(`<ol class="step-nav nav nav-tabs col-sm-10 col-md-8 mx-auto" id="stepnav-${id}"></ol>`).prependTo(currentForm);

        // Create the "Next step" button below the form
        $('<button/>')
          .attr('id', `next-btn-${id}`)
          .addClass('btn btn-outline-invert btn-icon btn-icon-right align-self-center')
          .text('Étape suivante')
          .on('click', function(e) {
            e.preventDefault();

            // @TODO make it work: disable the nav if current tab pane has required and empty fields
            // if (checkRequired(currentTab)) {
              nextTab.tab('show');
            // };
          })
          .appendTo(currentForm.find(`.tab-content`))
          .append(
            '<span class="icon" aria-hidden="true"><svg><use xlink:href="#icon-arrow"></use></svg></span>'
          );

        $fieldsets.each(function(index) {
          const currentFieldset = $(this);
          const $parent = currentFieldset.parents('.form-multistep');
          const stepLabel = currentFieldset.data('step');
          const fieldsetId = currentFieldset.attr('id');
          const nextFieldsetId = $(this).next('fieldset').attr('id');

          // Generate link to step
          const $link = $('<a/>', {
            'class': 'step-nav-link btn btn-outline-invert btn-circle',
            'href': `#${fieldsetId}`,
            'title': stepLabel,
            'aria-label': stepLabel,
            'id': `steptab-${fieldsetId}`,
            'aria-controls': fieldsetId,
            'aria-expanded': 'false',
            'data-last': index + 1 === $fieldsets.length ? 'true' : 'false',
          }).on('click', function(e) {
            e.preventDefault();

            // @TODO make it work: disable the nav if current tab pane has required and empty fields
            // if (checkRequired(currentFieldset)) {
              $(this).tab('show');
            // };
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
          currentTab = e.relatedTarget ? $(target) : currentForm.find('fieldset:first-of-type');
          nextTab = $(e.target).parent().next().find('a.step-nav-link');

          // Toggle buttons depending on current step
          if (nextTab.length <= 0) {
            $(`#${id} .js-form-submit`).show();
            $(`#next-btn-${id}`).hide();
          } else {
            $(`#${id} .js-form-submit`).hide();
            $(`#next-btn-${id}`).show();
          }
        });
      });

      // Show the first tab on load
      $('a.step-nav-link:first').tab('show');
    }
  })(jQuery);
};

export default multiStep;
