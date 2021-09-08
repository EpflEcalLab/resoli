<?php

namespace Drupal\qs_sharing\Repository;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\taxonomy\TermInterface;

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
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user entity.
   *
   * @return \Drupal\qs_sharing\Entity\volunteerism[]|null
   *   A collection of volunteerism. Otherwise, an empty array.
   */
  public function getAllByCommunityUser(TermInterface $community, AccountInterface $user): ?array {
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

  /**
   * Retrieve the Volunteerism entity for the given theme if it exists.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user entity.
   * @param \Drupal\taxonomy\TermInterface $theme
   *   The user entity.
   *
   * @return \Drupal\qs_sharing\entity\Volunteerism|null
   *   A Volunteerism empty, otherwise null.
   */
  public function isUserVolunteerForTheme(TermInterface $community, AccountInterface $user, TermInterface $theme) {
    $query = $this->volunteerismStorage->getQuery()
      ->accessCheck()
      ->condition('user', $user->id())
      ->condition('community', $community->id())
      ->condition('theme', $theme->id());

    $ids = $query->execute();

    if (empty($ids) || !\is_array($ids)) {
      return NULL;
    }

    return array_values($this->volunteerismStorage->loadMultiple($ids))[0];
  }

}
