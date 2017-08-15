<?php

namespace Drupal\qs_acl\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\taxonomy\TermInterface;

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
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * Class constructor.
   */
  public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entity, QueryFactory $query_factory) {
    $this->currentUser  = $currentUser;
    $this->termStorage  = $entity->getStorage('taxonomy_term');
    $this->queryFactory = $query_factory;
  }

  /**
   * Check if the user has access on the given community.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community against we check pending approval.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User against check access. Otherwise use current user.
   *
   * @return bool
   *   Does the user has at least one access for this community.
   */
  public function hasAccessCommunity(TermInterface $community, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    return $this->hasCommunityByUser($community, $user);
  }

  /**
   * Check if the user is waiting for at least one Privilege on this community.
   *
   * If the user has already one privilege it will alwayse return FALSE.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community against we check pending approval.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User against check access. Otherwise use current user.
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

    $query = $this->queryFactory->get('request_privileges')
      ->condition('status', 0)
      ->condition('entity', $community->id());

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
   *   Drupal Entity User against check access. Otherwise use current user.
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
   * Check if the user belongs to at least one community.
   *
   * This only check if the users belongs to a community
   * as Member or Organizer or Managers.
   * It doesn't get pending request..
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User against check access. Otherwise use current user.
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
   * Check if the user belongs to more than one community.
   *
   * This only check if the users belongs to a community
   * as Member or Organizer or Managers.
   * It doesn't get pending request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User against check access. Otherwise use current user.
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

    $query = $this->queryFactory->get('taxonomy_term')
      ->condition('vid', 'communities');

    $or = $query->orConditionGroup();
    $or->condition('field_community_members', $user->id());
    $or->condition('field_community_organizers', $user->id());
    $or->condition('field_community_managers', $user->id());
    $query->condition($or);

    $entities = [];
    $tids = $query->execute();
    if (!empty($tids)) {
      $entities = $this->termStorage->loadMultiple($tids);
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
    $query = $this->queryFactory->get('taxonomy_term')
      ->condition('vid', 'communities');

    $or = $query->orConditionGroup();
    $or->condition('field_community_members', $account->id());
    $or->condition('field_community_organizers', $account->id());
    $or->condition('field_community_managers', $account->id());
    $query->condition($or);

    return $query->count()->execute();
  }

  /**
   * Does the given user has access on the given community.
   *
   * This only count relation as Member or Organizer or Managers.
   * It doesn't count pending request.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community against we check pending approval.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User.
   *
   * @return bool
   *   Does the user has access on the community.
   */
  private function hasCommunityByUser(TermInterface $community, AccountInterface $account) {
    $query = $this->queryFactory->get('taxonomy_term')
      ->condition('vid', 'communities')
      ->condition('tid', $community->id());

    $or = $query->orConditionGroup();
    $or->condition('field_community_members', $account->id());
    $or->condition('field_community_organizers', $account->id());
    $or->condition('field_community_managers', $account->id());
    $query->condition($or);

    return $query->count()->execute() > 0 ? TRUE : FALSE;
  }

}
