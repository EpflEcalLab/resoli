<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\Component\FileSecurity\FileSecurity;
use Drupal\Core\File\FileSystemInterface;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines application features from the specific context.
 */
class PhotosContext extends RawDrupalContext {

  /**
   * Generate the "Photos" private folder.
   *
   * Make sure the private://photos folder exists & is writable.
   *
   * @BeforeScenario @photos
   */
  public function setupPhotos($event) {
    /** @var \Drupal\Core\File\FileSystemInterface $fso */
    $fso = \Drupal::service('file_system');
    $private_path = $fso->realpath('private://photos');

    // Attempts to create the directory.
    if (!is_dir($private_path)) {
      mkdir($private_path, 0777, TRUE);
    }

    $fso->prepareDirectory($private_path, FileSystemInterface::CREATE_DIRECTORY);
    $fso->prepareDirectory($private_path, FileSystemInterface::MODIFY_PERMISSIONS);
    FileSecurity::writeHtaccess($private_path, TRUE);
  }

}
