"use strict";

/* globals jQuery, Quill */
(function ($) {
  $(document).ready(function () {
    // Initialized Quill rich-text editor for each instance of ".editor"
    $('.editor').each(function () {
      const identifier = $(this).attr('id');

      new Quill(`#${identifier}`, {
        modules: {
          toolbar: [
            [{ header: [1, 2, false] }],
            ['bold', 'italic', 'underline'],
          ]
        },
        placeholder: 'Ecrire quelque chose...',
        theme: 'bubble'
      });
    })
  });
})(jQuery);
