<?php

namespace Drupal\qs_acl\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\qs_acl\Entity\Privilege;

/**
 * PrivilegeManager.
 */
class PrivilegeManager {
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
   * @param string $privilege
   *   The requested string privilege.
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param Drupal\Core\Session\AccountInterface $account
   *   Account for who we will request de privilege.
   *
   * @return Drupal\Core\Entity\EntityInterface
   *   The created privilege request.
   */
  public function request($privilege, EntityInterface $entity, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    // Check we don't already have the same privilege.
    $privileges = $this->privilegeStorage->loadByProperties([
      'bundle'    => $entity->getEntityTypeId(),
      'entity'    => $entity->id(),
      'user'      => $user->id(),
      'privilege' => $privilege,
    ]);

    // If the privilege already exists.
    if ($privileges) {
      $privilege = reset($privileges);

      // Previously declined ? Change as request again.
      if ($privilege->getStatus()->value == 0) {
        $privilege->setStatus(NULL);
        $privilege->reviewer = NULL;
        $privilege->reviewed = NULL;
        $privilege->save();
      }
      return $privilege;
    }

    $requested = $this->privilegeStorage->create([
      'entity' => $entity->id(),
      'user'   => $user->id(),
    ]);
    $requested->setPrivilege($privilege);
    $requested->setBundle($entity->getEntityTypeId());
    $requested->save();

    return $requested;
  }

  /**
   * Create a new privilege for the user on the given entity.
   *
   * @param string $privilege
   *   The requested string privilege.
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param Drupal\Core\Session\AccountInterface $user
   *   Account for who we will request de privilege.
   *
   * @return Drupal\Core\Entity\EntityInterface
   *   The created privilege.
   */
  public function create($privilege, EntityInterface $entity, AccountInterface $user) {
    $current_user = $this->currentUser;

    // Check we don't already have the same privilege.
    $privileges = $this->privilegeStorage->loadByProperties([
      'bundle'    => $entity->getEntityTypeId(),
      'entity'    => $entity->id(),
      'user'      => $user->id(),
      'privilege' => $privilege,
    ]);

    // If the privilege already exists, just confirm it.
    if ($privileges) {
      $privilege = reset($privileges);
      return $this->confirm($privilege);
    }

    // When the privilege don't exists, create one.
    $created = $this->privilegeStorage->create([
      'bundle'    => $entity->getEntityTypeId(),
      'entity'    => $entity->id(),
      'user'      => $user->id(),
      'status'    => 1,
      'privilege' => $privilege,
      'reviewer'  => $current_user->id(),
      'reviewed'  => time(),
    ]);
    $created->save();

    return $created;
  }

  /**
   * Confirm a previously requested privilege.
   *
   * @param \Drupal\qs_acl\Entity\Privilege $privilege
   *   The privilege to confirme.
   *
   * @return \Drupal\qs_acl\Entity\Privilege
   *   The confirmed privilege.
   */
  public function confirm(Privilege $privilege) {
    $reviewer = $this->currentUser;

    $privilege->setStatus(1);
    $privilege->setReviewer($reviewer);
    $privilege->setReviewedTime(time());
    $privilege->save();

    return $privilege;
  }

  /**
   * Decline a previously requested privilege.
   *
   * @param \Drupal\qs_acl\Entity\Privilege $privilege
   *   The privilege to decline.
   *
   * @return \Drupal\qs_acl\Entity\Privilege
   *   The declined privilege.
   */
  public function decline(Privilege $privilege) {
    $reviewer = $this->currentUser;

    $privilege->setStatus(0);
    $privilege->setReviewer($reviewer);
    $privilege->setReviewedTime(time());
    $privilege->save();

    return $privilege;
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
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   The database query.
   */
  public function queryMembersWithPrivileges(TermInterface $community) {
    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['user', 'privilege'])
      ->condition('privileges.status', 1)
      ->condition('privileges.bundle', 'taxonomy_term')
      ->condition('privileges.entity', $community->id());

    // Remove current user from the list.
    $query->condition('privileges.user', $this->currentUser->id(), '<>');

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

    return $query;
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
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   The database query.
   */
  public function queryWaitingApproval(TermInterface $community) {
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

    $query->orderBy('users.created', 'ASC');
    $query->orderBy('users.name', 'ASC');

    return $query;
  }

}
