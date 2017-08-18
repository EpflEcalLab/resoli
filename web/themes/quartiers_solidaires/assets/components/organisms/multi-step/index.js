const multiStep = () => {
  const $form = $('.form-multistep');

  if ($form.length > 0) {
    $form.each(function() {
      const id = $(this).attr('id');
      const $fieldsets = $(this).find('fieldset');
      $(`<ol class="step-nav nav nav-tabs" id="stepnav-${id}"></ol>`).prependTo($(this));

      $fieldsets.each(function(index) {
        const currentFieldset = $(this);
        const $parent = currentFieldset.parents('.form-multistep');
        const stepLabel = currentFieldset.data('step');
        const fieldsetId = currentFieldset.attr('id');
        const nextFieldsetId = $(this).next('fieldset').attr('id');

        // Generate link to step
        const $link = $('<a/>')
          .addClass('step-nav-link btn btn-outline-invert btn-circle')
          .attr({
            'href': `#${fieldsetId}`,
            'title': stepLabel,
            'aria-label': stepLabel,
            'id': `steptab-${fieldsetId}`,
            'aria-controls': fieldsetId,
            'aria-expanded': 'false'
          })
          .on('click', function (e) {
            e.preventDefault();

            // Forbid opening the tab if some fields are empty and required
            // in the previous fieldset
            if (currentFieldset.prev().find('input[required]').val() !== '') {
              $(this).tab('show');
            } else {
              alert('fill in the field!');
            }
          });

        // Append the step nav to the form
        $('<li/>')
          .addClass('step-nav-item')
          .appendTo(`#stepnav-${id}`)
          .append($link);

        // Add a next button on all steps except last
        if (index + 1 < $fieldsets.length) {
          $('<button/>')
            .addClass('btn btn-outline-invert btn-icon btn-icon-right')
            .text('Étape suivante')
            .on('click', function (e) {
              e.preventDefault();

              // Forbid opening the tab if some fields are empty and required
              // in the previous fieldset
              if (currentFieldset.find('input[required]').val() !== '') {
                $(`#steptab-${nextFieldsetId}`).triggerHandler('click');
              } else {
                alert('fill in the field!');
              }
            })
            .appendTo(currentFieldset)
            .append('<span class="icon" aria-hidden="true"><svg><use xlink:href="#icon-chevron-right"></use></svg></span>');
        }
      });

      // Show the first tab on load
      $(this).find('a.step-nav-link:first').tab('show');
    });


  }
};

export default multiStep;
