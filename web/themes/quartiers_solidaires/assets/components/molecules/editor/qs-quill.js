"use strict";

/* globals jQuery, Quill */
(function ($) {
  $(document).ready(function () {
    // Initialized Quill rich-text editor
    const quill = new Quill('.editor', {
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
