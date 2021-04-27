"use strict";

/* globals jQuery, Quill */
(function ($) {
  $(document).ready(function () {
    // Initialized Quill rich-text editor for each instance of ".editor"
    $('.quill-editor').each(function () {
      const identifier = $(this).attr('id');
      const placeholder = $(`#${identifier}`).attr('data-placeholder-translation');

      const quill = new Quill(`#${identifier}`, {
        modules: {
          toolbar: [
            [{ header: [1, 2, false] }],
            ['bold', 'italic', 'underline'],
          ]
        },
        placeholder: placeholder,
        theme: 'bubble'
      });

      // Translate the toolbar heading options
      const h1 = Drupal.t('qs.quill.editor.h1');
      const h2 = Drupal.t('qs.quill.editor.h2');
      $(`#${identifier} .ql-picker-item[data-value="1"]`).attr('data-h1-translation', h1);
      $(`#${identifier} .ql-picker-item[data-value="2"]`).attr('data-h2-translation', h2);
      $(`#${identifier} .ql-picker.ql-header .ql-picker-label[data-value="1"]`).attr('data-h1-translation', h1);
      $(`#${identifier} .ql-picker.ql-header .ql-picker-label[data-value="2"]`).attr('data-h2-translation', h2);

      // Translate the current selected option
      quill.on('text-change', function() {
        $(`#${identifier} .ql-picker.ql-header .ql-picker-label[data-value="1"]`).attr('data-h1-translation', h1);
        $(`#${identifier} .ql-picker.ql-header .ql-picker-label[data-value="2"]`).attr('data-h2-translation', h2);
      });

      // Add the content of the quill editor to the hidden textarea
      quill.on('text-change', function() {
        $(`#${identifier}`).parent().prev().find('textarea').val(quill.root.innerHTML);
      });
    });
  });
})(jQuery);
