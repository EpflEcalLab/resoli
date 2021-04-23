"use strict";

/* globals jQuery, Quill */
(function ($) {
  $(document).ready(function () {
    const quill = new Quill($('.editor'), {
      modules: {
        toolbar: [
          [{ header: [1, 2, false] }],
          ['bold', 'italic', 'underline'],
        ]
      },
      placeholder: 'Ecrire quelque chose...',
      theme: 'bubble'
    });
  });
})(jQuery);
