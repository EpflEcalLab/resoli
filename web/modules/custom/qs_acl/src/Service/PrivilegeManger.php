<?php

namespace Drupal\qs_acl\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityInterface;

/**
 * PrivilegeManger.
 */
class PrivilegeManger {
  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $currentUser;

  /**
   * The Privilege Storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  private $privilegeStorage;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Class constructor.
   */
  public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entity_type_manager, QueryFactory $query_factory, Connection $connection) {
    $this->currentUser      = $currentUser;
    $this->privilegeStorage = $entity_type_manager->getStorage('privilege');
    $this->userStorage      = $entity_type_manager->getStorage('user');
    $this->queryFactory     = $query_factory;
    $this->connection       = $connection;
  }

  /**
   * Request the collection of Privilege active for a given entiy & the user.
   *
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return Drupal\qs_acl\Entity\Privilege[]
   *   A collection of active Privilege according the user & the entity given.
   */
  public function fetchActive(EntityInterface $entity, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $query = $this->queryFactory->get('privilege')
      ->condition('status', 1)
      ->condition('bundle', $entity->getEntityTypeId())
      ->condition('user', $user->id())
      ->condition('entity', $entity->id());

    if ($entity->bundle() === 'communities') {
      $or = $query->orConditionGroup();
      $or->condition('privilege', 'community_members');
      $or->condition('privilege', 'community_organizers');
      $or->condition('privilege', 'community_managers');
      $query->condition($or);
    }
    elseif ($entity->bundle() === 'activity') {
      $or = $query->orConditionGroup();
      $or->condition('privilege', 'activity_members');
      $or->condition('privilege', 'activity_maintainers');
      $or->condition('privilege', 'activity_organizers');
      $query->condition($or);
    }

    $ids = $query->execute();
    $privileges = [];
    if ($ids) {
      $privileges = $this->privilegeStorage->loadMultiple($ids);
    }

    return $privileges;
  }

  /**
   * Request a new privilege for the user on the given entity.
   *
   * @param string $privilege_requested
   *   The requested string privilege.
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return Drupal\Core\Entity\EntityInterface
   *   The created privilege request.
   */
  public function request($privilege_requested, EntityInterface $entity, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $privilege = $this->privilegeStorage->create([
      'entity' => $entity->id(),
      'user'   => $user->id(),
    ]);
    $privilege->setPrivilege($privilege_requested);
    $privilege->setBundle($entity->getEntityTypeId());
    $privilege->save();

    return $privilege;
  }

  /**
   * Accept the privilege & add the privilege to the user.
   *
   * To add a privilege, we load the entity of this privlege (using the bundle)
   * & check add the user in the field of the named privilege field.
   * TOOD: code the function.
   *
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The privilege to accepte.
   */
  public function accepte(EntityInterface $entity) {
    $reviewer = $this->currentUser;
    dump($reviewer);
    dump('accepte');
    die();
  }

  /**
   * Decline the privilege.
   *
   * TOOD: code the function.
   *
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The privilege to accepte.
   */
  public function decline(EntityInterface $entity) {
    $reviewer = $this->currentUser;
    dump($reviewer);
    dump('decline');
    die();
  }

  /**
   * Remove the privilege for a given user.
   *
   * To remove a privilege, we use the givne $privlege as field of $entity
   * and remove the $account from it.
   *
   * TOOD: code the function.
   *
   * @param string $privilege
   *   The privilege.
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   */
  public function remove($privilege, EntityInterface $entity, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }
    dump($user);
    dump('remove');
    die();
  }

  /**
   * Request the collection of members for a given community.
   *
   * An account is processed as member of community if it has one privilege:
   *  - community_members
   *  - community_organizers
   *  - community_managers.
   *
   * @param Drupal\taxonomy\TermInterface $community
   *   The Community Entity for the privilege.
   *
   * @return Drupal\Core\Session\AccountInterface[]
   *   A collection of members.
   */
  public function fetchMembersWithPrivileges(TermInterface $community) {
    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['user', 'privilege'])
      ->condition('privileges.status', 1)
      ->condition('privileges.bundle', 'taxonomy_term')
      ->condition('privileges.entity', $community->id());

    $or = $query->orConditionGroup();
    $or->condition('privileges.privilege', 'community_members');
    $or->condition('privileges.privilege', 'community_organizers');
    $or->condition('privileges.privilege', 'community_managers');
    $query->condition($or);

    // Join the users data for filters criteria.
    // TODO: Add Filter block by name, firstname, lastname.
    $query->leftJoin('users_field_data', 'users', 'users.uid = privileges.user');

    $query->orderBy('users.name', 'ASC');
    $query->groupBy('privileges.privilege');
    $query->groupBy('privileges.user');
    $query->groupBy('users.name');

    $rows = $query->execute()->fetchAll();

    $uids = [];
    $privileges = [];
    foreach ($rows as $row) {
      $uids[] = $row->user;
      $privileges[$row->user][] = $row->privilege;
    }

    // Load user entities whitout privileges.
    $members = $this->userStorage->loadMultiple($uids);

    // Add privileges to users.
    foreach ($members as $member) {
      $member->privileges = $privileges[$member->id()];
    }

    return $members;
  }

  /**
   * Request the collection of Accounts waiting for Approval on the community.
   *
   * Accounts are referenced as waiting for Approval when it has
   * at leaset one pending privilege on the community.
   * Even if the user already has the privilege or another one(s)
   * on the community.
   *  - community_members
   *  - community_organizers
   *  - community_managers.
   *
   * @param Drupal\taxonomy\TermInterface $community
   *   The Community Entity for the privilege.
   *
   * @return array
   *   A collection of requested privileges.
   */
  public function fetchWaitingApproval(TermInterface $community) {
    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['user', 'id'])
      ->condition('privileges.status', NULL, 'IS')
      ->condition('privileges.bundle', 'taxonomy_term')
      ->condition('privileges.entity', $community->id());

    $or = $query->orConditionGroup();
    $or->condition('privileges.privilege', 'community_members');
    $or->condition('privileges.privilege', 'community_organizers');
    $or->condition('privileges.privilege', 'community_managers');
    $query->condition($or);

    // Join the users data for filters criteria.
    // TODO: Add Filter block by name, firstname, lastname.
    $query->leftJoin('users_field_data', 'users', 'users.uid = privileges.user');

    $query->orderBy('users.name', 'ASC');
    $rows = $query->execute()->fetchAll();

    $ids = [];
    foreach ($rows as $row) {
      $ids[] = $row->id;
    }
    // Load user entities whitout privileges.
    $privileges = $this->privilegeStorage->loadMultiple($ids);

    return $privileges;
  }

}
