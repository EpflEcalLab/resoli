<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines sharing features from the specific context.
 *
 * @codingStandardsIgnoreFile
 */
class SharingContext extends RawDrupalContext {

  /**
   * @Given I am volunteer on community :community_id for theme :theme_id as user :user_id
   */
  public function iAmVolunteer(string $user_id, string $theme_id, string $community_id): void {
    $volunteerism4 = \Drupal::service('entity_type.manager')->getStorage('volunteerism')->create([
      'theme' => $theme_id,
      'community' => $community_id,
      'user' => $user_id,
    ]);
    $volunteerism4->save();
  }
}
