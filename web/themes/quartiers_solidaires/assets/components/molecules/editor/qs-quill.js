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
            [{ header: [4, 5, false] }],
            ['bold', 'italic', 'underline', 'link'],
          ]
        },
        placeholder: placeholder,
        theme: 'bubble'
      });

      // Form group for the textarea of the form which will contains the input from the Quill editor
      const textareaFormGroup = $(`#${identifier}`).parent().prev();
      // Textarea form group is hidden with js to prevent accessibility issue
      textareaFormGroup.attr('hidden', true);

      // Set the content of the textarea in the quill editor
      quill.root.innerHTML = textareaFormGroup.find('textarea').val();

      // Translate the toolbar heading options
      const h4 = Drupal.t('qs.quill.editor.h4');
      const h5 = Drupal.t('qs.quill.editor.h5');
      $(`#${identifier} .ql-picker-item[data-value="4"]`).attr('data-h4-translation', h4);
      $(`#${identifier} .ql-picker-item[data-value="5"]`).attr('data-h5-translation', h5);
      $(`#${identifier} .ql-picker.ql-header .ql-picker-label[data-value="4"]`).attr('data-h4-translation', h4);
      $(`#${identifier} .ql-picker.ql-header .ql-picker-label[data-value="5"]`).attr('data-h5-translation', h5);

      // Translate the current selected option
      const translateHeading = () => {
        $(`#${identifier} .ql-picker.ql-header .ql-picker-label[data-value="4"]`).attr('data-h4-translation', h4);
        $(`#${identifier} .ql-picker.ql-header .ql-picker-label[data-value="5"]`).attr('data-h5-translation', h5);
      }
      quill.on('text-change', translateHeading);
      quill.on('selection-change', translateHeading);

      // Add the content of the quill editor to the hidden textarea
      quill.on('text-change', function() {
        textareaFormGroup.find('textarea').val(quill.root.innerHTML);
      });

      // Remove custom style on copy-paste
      quill.clipboard.addMatcher(Node.ELEMENT_NODE, (node, delta) => {
        delta.ops = delta.ops.map(op => {
          return {
            insert: op.insert
          }
        })
        return delta
      })
    });
  });
})(jQuery);


