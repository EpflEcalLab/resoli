import $ from 'jquery';

const checkbox = () => {
  // When clicking the All Themes button, we want to enable / disable ALL the
  // themes.
  const $btn_check_all = $('#js-all-themes');
  const $themes = $('.js-filter-themes .btn.checkbox');
  let checked = $btn_check_all.prop('checked');

  // Listen to click events on the ALL button
  // We can't listen to `change` event as it would trigger every time
  $btn_check_all.on('click', function() {
    checked = !checked;
    toggleSelected(checked);
  });

  // We can't listen to `change` event as it would trigger every time
  $themes.on('click', function() {

    // wether the clicked element will be checked
    const selfChecked = !$(this).find('input[type=checkbox]').prop('checked');
    // how many options are currently checked
    const checkedOptions = $('.js-filter-themes').find('input[type=checkbox]:checked');
    // total of checked options after the change event triggers
    let counter = selfChecked ? checkedOptions.length + 1 : checkedOptions.length - 1;

    // if counter is equal to the total of checkboxes, toggle the ALL button
    // else if counter is less than the total and the ALL button is active, toggle it
    if (counter >= $themes.length) {
      $btn_check_all.button('toggle');
    } else if (counter < $themes.length && $btn_check_all.hasClass('active')) {
      $btn_check_all.button('toggle');
    }
  });

  const toggleSelected = (state) => {
    // Check all checkboxes that are not yet of the same value as ALL button
    $themes.each(function() {
      const checkbox_checked = $(this).find('input[type=checkbox]').prop('checked');
      if (checkbox_checked !== state) {
        $(this).button('toggle');
      }
    });
  }
};

export default checkbox;
