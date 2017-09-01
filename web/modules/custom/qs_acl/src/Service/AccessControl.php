<?php

namespace Drupal\qs_acl\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\taxonomy\TermInterface;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;

/**
 * AccessControl.
 */
class AccessControl {
  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $currentUser;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * The Privilege Storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  private $privilegeStorage;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * Class constructor.
   */
  public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entity_type_manager, QueryFactory $query_factory) {
    $this->currentUser      = $currentUser;
    $this->termStorage      = $entity_type_manager->getStorage('taxonomy_term');
    $this->privilegeStorage = $entity_type_manager->getStorage('privilege');
    $this->queryFactory     = $query_factory;
  }

  /**
   * Check if the account has access on the given user dashboard.
   *
   * @param \Drupal\user\UserInterface $user
   *   The community to check access.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access.
   *
   * @return bool
   *   Does the account has access to the user dashboard.
   */
  public function hasAccessAccountDashboard(UserInterface $user, AccountInterface $account) {
    // Check bypass.
    if ($this->hasBypass($account)) {
      return TRUE;
    }

    return $user->id() == $account->id();
  }

  /**
   * Check if the account has write access on the given user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The community to check access.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access.
   *
   * @return bool
   *   Does the account has write access to the user.
   */
  public function hasWriteAccessAccount(UserInterface $user, AccountInterface $account) {
    // Check bypass.
    if ($this->hasBypass($account)) {
      return TRUE;
    }

    return $user->id() == $account->id();
  }

  /**
   * Check if the account has access on the given community.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community to check access.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return bool
   *   Does the user has at least one access for this community.
   */
  public function hasAccessCommunity(TermInterface $community, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    // Check bypass.
    if ($this->hasBypass($user)) {
      return TRUE;
    }

    return $this->hasCommunityByUser($community, $user);
  }

  /**
   * Check if the account has write access on the given community.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community to check access.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return bool
   *   Does the user has at least one write access for this community.
   */
  public function hasWriteAccessCommunity(TermInterface $community, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    // Check bypass.
    if ($this->hasBypass($user)) {
      return TRUE;
    }

    $query = $this->queryFactory->get('privilege')
      ->condition('status', 1)
      ->condition('bundle', 'taxonomy_term')
      ->condition('entity', $community->id())
      ->condition('user', $user->id());

    $or = $query->orConditionGroup();
    $or->condition('privilege', 'community_managers');
    $or->condition('privilege', 'community_organizers');
    $query->condition($or);

    $number = (int) $query->count()->execute();

    return $number > 0 ? TRUE : FALSE;
  }

  /**
   * Check if the account has admin access on the given community.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community to check access.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return bool
   *   Does the user has at least one admin access for this community.
   */
  public function hasAdminAccessCommunity(TermInterface $community, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    // Check bypass.
    if ($this->hasBypass($user)) {
      return TRUE;
    }

    $query = $this->queryFactory->get('privilege')
      ->condition('status', 1)
      ->condition('bundle', 'taxonomy_term')
      ->condition('entity', $community->id())
      ->condition('user', $user->id());

    $or = $query->orConditionGroup();
    $or->condition('privilege', 'community_managers');
    $query->condition($or);

    $number = (int) $query->count()->execute();

    return $number > 0 ? TRUE : FALSE;
  }

  /**
   * Check if the account has write access on the given activity.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   The activity to check access.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return bool
   *   Does the user has at least one write access for this activity.
   */
  public function hasWriteAccessActivity(NodeInterface $activity, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    // Check bypass.
    if ($this->hasBypass($user)) {
      return TRUE;
    }

    $query = $this->queryFactory->get('privilege')
      ->condition('status', 1)
      ->condition('bundle', 'node')
      ->condition('entity', $activity->id())
      ->condition('user', $user->id());

    $or = $query->orConditionGroup();
    $or->condition('privilege', 'activity_organizers');
    $query->condition($or);

    $number = (int) $query->count()->execute();

    return $number > 0 ? TRUE : FALSE;
  }

  /**
   * Check if the account has write access for event on the given activity.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   The event to check access.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return bool
   *   Does the user has at least one write access for this activity.
   */
  public function hasWriteAccessEvent(NodeInterface $activity, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    // Check bypass.
    if ($this->hasBypass($user)) {
      return TRUE;
    }

    $query = $this->queryFactory->get('privilege')
      ->condition('status', 1)
      ->condition('bundle', 'node')
      ->condition('entity', $activity->id())
      ->condition('user', $user->id());

    $or = $query->orConditionGroup();
    $or->condition('privilege', 'activity_organizers');
    $or->condition('privilege', 'activity_maintainers');
    $query->condition($or);

    $number = (int) $query->count()->execute();

    return $number > 0 ? TRUE : FALSE;
  }

  /**
   * Check the account is waiting for at least one Privilege on the community.
   *
   * If the user has already one privilege it will alwayse return FALSE.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community to check access.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return bool
   *   Does the user is waiting for Privilege on this community.
   */
  public function isPendingApproval(TermInterface $community, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    if ($this->hasCommunityByUser($community, $user)) {
      return FALSE;
    }

    $query = $this->queryFactory->get('privilege')
      ->condition('status', 0)
      ->condition('entity', $community->id())
      ->condition('user', $user->id());

    $or = $query->orConditionGroup();
    $or->condition('reviewer', NULL);
    $or->notExists('reviewer');
    $query->condition($or);

    return $query->count()->execute() > 0 ? TRUE : FALSE;
  }

  /**
   * Check if the given user or the current logged one has the role beginner.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return bool
   *   Does the account has the beginner role.
   */
  public function isBeginner(AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $roles = $user->getRoles();

    return in_array('beginner', $roles);
  }

  /**
   * Check if the account belongs to at least one community.
   *
   * This only check if the accounts belongs to a community
   * as Member or Organizer or Managers.
   * It doesn't get pending request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return bool
   *   Does the account belongs to at least one community.
   */
  public function hasCommunity(AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $number = $this->countCommunitiesByUser($user);

    return $number > 0 ? TRUE : FALSE;
  }

  /**
   * Check if the account belongs to more than one community.
   *
   * This only check if the accounts belongs to a community
   * as Member or Organizer or Managers.
   * It doesn't get pending request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return bool
   *   Does the account belongs to more than one community.
   */
  public function hasMultipleCommunities(AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $number = $this->countCommunitiesByUser($user);

    return $number > 1 ? TRUE : FALSE;
  }

  /**
   * Get pending approval communities for a given user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   Collection of communities.
   */
  public function getPendingApprovalCommunities(AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $query = $this->queryFactory->get('privilege')
      ->condition('bundle', 'taxonomy_term')
      ->condition('user', $user->id());

    $or = $query->orConditionGroup();
    $or->condition('status', NULL);
    $or->notExists('status');
    $query->condition($or);

    $or2 = $query->orConditionGroup();
    $or2->condition('reviewer', NULL);
    $or2->notExists('reviewer');
    $query->condition($or2);

    $entities = [];
    $ids = $query->execute();

    if (!empty($ids)) {
      $privileges = $this->privilegeStorage->loadMultiple($ids);
      foreach ($privileges as $privilege) {
        $entities[] = $privilege->getEntity();
      }
    }

    return $entities;
  }

  /**
   * Get communities for a given user.
   *
   * This only retrieve relation as Member or Organizer or Managers.
   * It doesn't get pending request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User.
   *
   * @return \Drupal\taxonomy\TermInterface[]
   *   Collection of communities.
   */
  public function getCommunities(AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    // If user has bypass Access, return the list of all communities.
    if ($this->hasBypass($user)) {
      return $this->termStorage->loadTree('communities', 0, NULL, TRUE);
    }

    $query = $this->queryFactory->get('privilege')
      ->condition('status', 1)
      ->condition('bundle', 'taxonomy_term')
      ->condition('user', $user->id());

    $or = $query->orConditionGroup();
    $or->condition('privilege', 'community_members');
    $or->condition('privilege', 'community_organizers');
    $or->condition('privilege', 'community_managers');
    $query->condition($or);

    $entities = [];
    $ids = $query->execute();

    if (!empty($ids)) {
      $privileges = $this->privilegeStorage->loadMultiple($ids);
      foreach ($privileges as $privilege) {
        $community = $privilege->getEntity();
        $entities[$community->id()] = $community;
      }
    }

    return $entities;
  }

  /**
   * Count communities for a given user.
   *
   * This only count relation as Member or Organizer or Managers.
   * It doesn't count pending request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User.
   *
   * @return int
   *   Number of communities the user belongs to.
   */
  private function countCommunitiesByUser(AccountInterface $account) {
    $query = $this->queryFactory->get('privilege')
      ->condition('status', 1)
      ->condition('bundle', 'taxonomy_term')
      ->condition('user', $account->id());

    $or = $query->orConditionGroup();
    $or->condition('privilege', 'community_members');
    $or->condition('privilege', 'community_organizers');
    $or->condition('privilege', 'community_managers');
    $query->condition($or);

    return (int) $query->count()->execute();
  }

  /**
   * Does the given user has access on the given community.
   *
   * This only count relation as Member or Organizer or Managers.
   * It doesn't count pending request.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community to check access.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User.
   *
   * @return bool
   *   Does the user has access on the community.
   */
  private function hasCommunityByUser(TermInterface $community, AccountInterface $account) {
    $query = $this->queryFactory->get('privilege')
      ->condition('status', 1)
      ->condition('bundle', 'taxonomy_term')
      ->condition('user', $account->id())
      ->condition('entity', $community->id());

    $or = $query->orConditionGroup();
    $or->condition('privilege', 'community_members');
    $or->condition('privilege', 'community_organizers');
    $or->condition('privilege', 'community_managers');
    $query->condition($or);

    return $query->count()->execute() > 0 ? TRUE : FALSE;
  }

  /**
   * Check if the given user can bypass any security restriction.
   *
   * This method has security implications.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return bool
   *   Does the given user has bypass security permission.
   */
  public function hasBypass(AccountInterface $account = NULL, EntityInterface $entity = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    if ($user->hasPermission('bypass node access')) {
      return TRUE;
    }

    if ($entity) {
      // Check user is the original author of the given entity.
      $owner = $entity->getOwner();
      if ($owner->id() == $user->id()) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
