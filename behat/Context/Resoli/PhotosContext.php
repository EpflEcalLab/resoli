<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Defines application features from the specific context.
 */
class PhotosContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Generate the "Photos" private folder.
   *
   * Make sure the private://photos folder exists & is writtable.
   *
   * @BeforeScenario @photos
   */
  public function setupPhotos($event) {
    $fso = \Drupal::service('file_system');
    $private_path = $fso->realpath('private://photos');

    // Attempts to create the directory.
    if (!is_dir($private_path)) {
      mkdir($private_path, 0777, TRUE);
    }

    file_prepare_directory($private_path, FILE_CREATE_DIRECTORY);
    file_prepare_directory($private_path, FILE_MODIFY_PERMISSIONS);
    file_save_htaccess($private_path, TRUE);
  }

}
