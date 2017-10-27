const formSelectize = () => {
  (function ($) {
    var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
    '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';

    const options = $('select.selectize-members').data('options');

    $('select.selectize-members').selectize({
      persist: false,
      maxItems: 1,
      items: null,
      valueField: 'uid',
      labelField: 'displayname',
      searchField: ['email', 'displayname'],
      sortField: [
        { field: 'displayname', direction: 'asc' }
      ],
      options: options,
      plugins: ['remove_button'],
      render: {
        item: function(item, escape) {
          return '<div>' +
            (item.displayname ? '<span class="name">' + escape(item.displayname) + '</span>' : '') +
          (item.email ? ' <span class="email">(' + escape(item.email) + ')</span>' : '') +
          '</div>';
        },
        option: function(item, escape) {
          var label = item.displayname || item.email;
          var caption = item.displayname ? item.email : null;
          return '<div>' +
          '<span class="label">' + escape(label) + '</span>' +
          (caption ? ' <span class="caption">(' + escape(caption) + ')</span>' : '') +
          '</div>';
        }
      }
    });
  })(jQuery);
};

export default formSelectize;
