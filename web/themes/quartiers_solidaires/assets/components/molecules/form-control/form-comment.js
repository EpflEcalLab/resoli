const formComment = () => {
  (function ($) {
    const $textareas = $('.js-comment-form').find('textarea');

    if ($textareas.length > 1) {

      // Copy the text to each textarea.
      const copyToOthers = text => {
        $textareas.each(function() {
          $(this).val(text);
        });
      }

      // Add a copy button after the first textarea
      const btn = $(`<button class="btn btn-outline-invert mt-2">${Drupal.t('qs.comment.copy_to_other')}</button>`)
        .on('click', function(e) {
          e.preventDefault();

          const text = $(this).prev().val();
          copyToOthers(text);
        });
      $textareas.first().after(btn);
    }

  })(jQuery);
};

export default formComment;
