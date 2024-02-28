<?php

/**
 * @file
 * Contains alteration of default events dates.
 */

/**
 * Global function to alter default events with date according now.
 */
function qs_default_content_photos() {
  /** @var \Drupal\Core\Entity\EntityTypeManagerInterface */
  $entityManager = Drupal::service('entity_type.manager');
  $nodeStorage = $entityManager->getStorage('node');

  $photo_41 = $nodeStorage->load(41);
  $photo_41->field_image->generateSampleItems();
  $photo_41->save();

  $photo_42 = $nodeStorage->load(42);
  $photo_42->field_image->generateSampleItems();
  $photo_42->save();

  $photo_43 = $nodeStorage->load(43);
  $photo_43->field_image->generateSampleItems();
  $photo_43->save();

  $photo_44 = $nodeStorage->load(44);
  $photo_44->field_image->generateSampleItems();
  $photo_44->save();

  $photo_45 = $nodeStorage->load(45);
  $photo_45->field_image->generateSampleItems();
  $photo_45->save();

  $photo_46 = $nodeStorage->load(46);
  $photo_46->field_image->generateSampleItems();
  $photo_46->save();

  $photo_47 = $nodeStorage->load(47);
  $photo_47->field_image->generateSampleItems();
  $photo_47->save();

  $photo_48 = $nodeStorage->load(48);
  $photo_48->field_image->generateSampleItems();
  $photo_48->save();

  $photo_49 = $nodeStorage->load(49);
  $photo_49->field_image->generateSampleItems();
  $photo_49->save();

  $photo_50 = $nodeStorage->load(50);
  $photo_50->field_image->generateSampleItems();
  $photo_50->save();

  $photo_51 = $nodeStorage->load(51);
  $photo_51->field_image->generateSampleItems();
  $photo_51->save();

  $photo_52 = $nodeStorage->load(52);
  $photo_52->field_image->generateSampleItems();
  $photo_52->save();

  $photo_53 = $nodeStorage->load(53);
  $photo_53->field_image->generateSampleItems();
  $photo_53->save();
}
