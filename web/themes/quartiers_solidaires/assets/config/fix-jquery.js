// Drupal 8.4 introduced jQuery 3 and with it a series of issues. We have to
// define $ as a result of this. Hoping this won't break anything else...
if (typeof $ === 'undefined' && typeof jQuery !== 'undefined') {
  $ = jQuery;
}
