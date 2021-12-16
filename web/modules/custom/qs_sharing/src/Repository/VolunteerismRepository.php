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
   * @param \Drupal\taxonomy\TermInterface $theme
   *   The sharing theme entity.
   * @param \Drupal\user\UserInterface[]|null $exclude_users
   *   An optional collection of user to be excluded.
   *
   * @return \Drupal\qs_sharing\Entity\volunteerism[]|null
   *   A collection of volunteerism. Otherwise, an empty array.
   */
  public function getAllByCommunityTheme(TermInterface $community, TermInterface $theme, ?array $exclude_users = NULL): ?array {
    $query = $this->volunteerismStorage->getQuery()
      ->accessCheck()
      ->condition('theme', $theme->id())
      ->condition('community', $community->id());

    if (!empty($exclude_users)) {
      $exclude_uids = array_map(static function ($user) {
        return $user->id();
      }, $exclude_users);
      $query->condition('user', $exclude_uids, 'NOT IN');
    }

    $query->groupBy('user');

    $ids = $query->execute();

    if (empty($ids) || !\is_array($ids)) {
      return NULL;
    }

    return $this->volunteerismStorage->loadMultiple($ids);
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
