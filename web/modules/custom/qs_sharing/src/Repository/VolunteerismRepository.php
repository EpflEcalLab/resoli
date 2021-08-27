<?php

namespace Drupal\qs_sharing\Repository;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * The Volunteerism Repository.
 */
class VolunteerismRepository {

  /**
   * The Volunteerism storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  private $volunteerismStorage;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->volunteerismStorage = $entity_type_manager->getStorage('volunteerism');
  }

  /**
   * Get all volunteering for the $user in the given $community.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\user\UserInterface $user
   *   The user entity.
   *
   * @return \Drupal\qs_sharing\Entity\volunteerism[]|null
   *   A collection of volunteerism. Otherwise, an empty array.
   */
  public function getAllByCommunityUser(TermInterface $community, UserInterface $user): ?array {
    $query = $this->volunteerismStorage->getQuery()
      ->accessCheck()
      ->condition('user', $user->id())
      ->condition('community', $community->id());

    $ids = $query->execute();

    if (empty($ids) || !\is_array($ids)) {
      return NULL;
    }

    return $this->volunteerismStorage->loadMultiple($ids);
  }

}
