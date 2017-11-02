<?php

/**
 * @file
 * Contains alteration of default events dates.
 */

use Drupal\Core\StreamWrapper\PublicStream;

/**
 * Global function to alter default events with date according now.
 *
 * @SuppressWarnings(PHPMD)
 */
function qs_default_content_photos() {
  // @var \Drupal\Core\Entity\EntityTypeManagerInterface
  $entityManager = \Drupal::service('entity_type.manager');
  $fileStorage = $entityManager->getStorage('file');
  $nodeStorage = $entityManager->getStorage('node');

  file_unmanaged_copy(drupal_get_path('module', 'qs_default_content') . '/assets/default.png', PublicStream::basePath());
  $file = $fileStorage->create([
    'uri' => 'public://default.png',
    'status' => FILE_STATUS_PERMANENT,
  ]);
  $file->save();

  $photo_41 = $nodeStorage->load(41);
  $photo_41->set('field_image', $file);
  $photo_41->save();

  $photo_41 = $nodeStorage->load(41);
  $photo_41->set('field_image', $file);
  $photo_41->save();

  $photo_42 = $nodeStorage->load(42);
  $photo_42->set('field_image', $file);
  $photo_42->save();

  $photo_43 = $nodeStorage->load(43);
  $photo_43->set('field_image', $file);
  $photo_43->save();

  $photo_44 = $nodeStorage->load(44);
  $photo_44->set('field_image', $file);
  $photo_44->save();

  $photo_45 = $nodeStorage->load(45);
  $photo_45->set('field_image', $file);
  $photo_45->save();

  $photo_46 = $nodeStorage->load(46);
  $photo_46->set('field_image', $file);
  $photo_46->save();

  $photo_47 = $nodeStorage->load(47);
  $photo_47->set('field_image', $file);
  $photo_47->save();

  $photo_48 = $nodeStorage->load(48);
  $photo_48->set('field_image', $file);
  $photo_48->save();

  $photo_49 = $nodeStorage->load(49);
  $photo_49->set('field_image', $file);
  $photo_49->save();

  $photo_50 = $nodeStorage->load(50);
  $photo_50->set('field_image', $file);
  $photo_50->save();

  $photo_51 = $nodeStorage->load(51);
  $photo_51->set('field_image', $file);
  $photo_51->save();
}
