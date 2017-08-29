import $ from 'jquery';

const multiStep = () => {
  const $form = $('.form-multistep');

  if ($form.length > 0) {
    $form.each(function() {
      const id = $(this).attr('id');
      const $fieldsets = $(this).find('fieldset');
      let nextTab = null;
      let currentTab = null;

      $(`<ol class="step-nav nav nav-tabs col-sm-10 col-md-8 mx-auto" id="stepnav-${id}"></ol>`).prependTo($(this));

      // Create the "Next step" button below the form
      $('<button/>')
        .attr('id', `next-btn-${id}`)
        .addClass('btn btn-outline-invert btn-icon btn-icon-right')
        .text('Étape suivante')
        .on('click', function(e) {
          e.preventDefault();

          nextTab.tab('show');
        })
        .appendTo($(this).find(`.tab-content`))
        .append(
          '<span class="icon" aria-hidden="true"><svg><use xlink:href="#icon-chevron-right"></use></svg></span>'
        );

      $fieldsets.each(function(index) {
        const currentFieldset = $(this);
        const $parent = currentFieldset.parents('.form-multistep');
        const stepLabel = currentFieldset.data('step');
        const fieldsetId = currentFieldset.attr('id');
        const nextFieldsetId = $(this).next('fieldset').attr('id');

        // Generate link to step
        const $link = $('<a/>', {
          class: 'step-nav-link btn btn-outline-invert btn-circle',
          href: `#${fieldsetId}`,
          title: stepLabel,
          'aria-label': stepLabel,
          id: `steptab-${fieldsetId}`,
          'aria-controls': fieldsetId,
          'aria-expanded': 'false',
          'data-last': index + 1 === $fieldsets.length ? 'true' : 'false',
        }).on('click', function(e) {
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
      });

      // show next tab on click
      $('a.step-nav-link').on('show.bs.tab', function(e) {
        currentTab = $(e.relatedTarget).find('a.step-nav-link');

        nextTab = $(e.target)
          .parent()
          .next()
          .find('a.step-nav-link');

        if (nextTab.length <= 0) {
          $(`#${id} input[type=submit]`).show();
          $(`#next-btn-${id}`).hide();
        } else {
          $('input[type=submit]').hide();
          $(`#next-btn-${id}`).show();
        }
      });
    });

    // Show the first tab on load
    $('a.step-nav-link:first').tab('show');

    // Hide submit button on load
    // $('input[type=submit]').hide();
  }
};

export default multiStep;
