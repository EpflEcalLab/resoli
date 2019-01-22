/* globals jQuery, Uppy */

(function ($) {
  $( document ).ready(function() {
    const $handler = $('.form-uppy-handler');
    const $handler_input = $('.form-uppy-handler input[type="file"]');

    if ($handler_input.length <= 0) {
      return;
    }

    const extensions = $handler_input.data('extensions');
    const filesize = $handler_input.data('filesize');
    const uploadUrl = $handler_input.data('uploadUrl');
    const allowedFileTypes = $handler_input.data('allowedFileTypes');
    const locale = $handler_input.data('locale');
    const callbackFields = $handler_input.data('callbackFields');
    const hide = $handler_input.data('hide');

    const uppy = Uppy.Core({
      id: 'uppy',
      debug: false,
      autoProceed: true,
      restrictions: {
        maxFileSize: filesize,
        maxNumberOfFiles: false,
        minNumberOfFiles: false,
        allowedFileTypes,
      },
    })
    .use(Uppy.Dashboard, {
      trigger: $handler_input,
      inline: true,
      target: '.form-uppy-handler',
      replaceTargetContent: true,
      height: 200,
      note: extensions,
      locale,
      proudlyDisplayPoweredByUppy: false,
    })
    .use(Uppy.XHRUpload, {
      endpoint: uploadUrl,
      method: 'post',
      formData: true,
      getResponseError: (responseText, xhr) => {
        return new Error(JSON.parse(xhr.response).error);
      },
    })
    .use(Uppy.ProgressBar, {
      target: 'body',
      fixed: true,
      hideAfterFinish: false,
    })
    .run()
    .on('upload-success', (file, body) => {
      if (body.success) {
        const $input = $(`<input type="hidden" name="${callbackFields}[]">`);
        $input.val(JSON.stringify(body.data));
        $handler.append($input);
      }
    });

    uppy.on('file-added', (file) => {
      uppy.setFileMeta(file.id, {
        'activity': $('.form-uppy-handler').parents('form').find('#edit-activity').val(),
        'event': $('.form-uppy-handler').parents('form').find('#edit-event').val(),
      })
    })

  });
})(jQuery);
