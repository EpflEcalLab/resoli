<?php

namespace Drupal\qs_acl\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\ConditionInterface;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_acl\Entity\Privilege;

/**
 * PrivilegeManager.
 */
class PrivilegeManager {

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;
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
   * Class constructor.
   */
  public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entity_type_manager, QueryFactory $query_factory, Connection $connection) {
    $this->currentUser = $currentUser;
    $this->privilegeStorage = $entity_type_manager->getStorage('privilege');
    $this->queryFactory = $query_factory;
    $this->connection = $connection;
  }

  /**
   * Confirm a previously requested privilege.
   *
   * @param \Drupal\qs_acl\Entity\Privilege $privilege
   *   The privilege to confirm.
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
   * Create a new privilege for the user on the given entity.
   *
   * @param string $privilege
   *   The requested string privilege.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   Account for who we will request de privilege.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The created privilege.
   */
  public function create($privilege, EntityInterface $entity, AccountInterface $user) {
    $current_user = $this->currentUser;

    // Check we don't already have the same privilege.
    $privileges = $this->privilegeStorage->loadByProperties([
      'bundle' => $entity->getEntityTypeId(),
      'entity' => $entity->id(),
      'user' => $user->id(),
      'privilege' => $privilege,
    ]);

    // If the privilege already exists, just confirm it.
    if ($privileges) {
      $privilege = reset($privileges);

      return $this->confirm($privilege);
    }

    // When the privilege don't exists, create one.
    $created = $this->privilegeStorage->create([
      'bundle' => $entity->getEntityTypeId(),
      'entity' => $entity->id(),
      'user' => $user->id(),
      'status' => 1,
      'privilege' => $privilege,
      'reviewer' => $current_user->id(),
      'reviewed' => time(),
    ]);
    $created->save();

    return $created;
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
   * Request the collection of Privilege active for a given entiy & the user.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return \Drupal\qs_acl\Entity\Privilege[]
   *   A collection of active Privilege according the user & the entity given.
   */
  public function fetchActive(EntityInterface $entity, ?AccountInterface $account = NULL) {
    $user = $this->currentUser;

    if ($account !== NULL) {
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
   * Check for the given entity IDs, if the user has the requested privileges.
   *
   * @param int[] $entities
   *   A collection of entities IDs.
   * @param string[] $privileges
   *   A collection of privileges.
   * @param bool $status
   *   The required status for the privileges.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return array[]
   *   A collection of Entities ID.
   */
  public function getPrivileges(array $entities, array $privileges, $status = TRUE, ?AccountInterface $account = NULL) {
    $user = $this->currentUser;

    if ($account !== NULL) {
      $user = $account;
    }

    $query = $this->queryFactory->get('privilege')
      ->condition('status', $status)
      ->condition('user', $user->id())
      ->condition('entity', $entities, 'IN');

    $or = $query->orConditionGroup();

    foreach ($privileges as $privilege) {
      $or->condition('privilege', $privilege);
    }
    $query->condition($or);

    $ids = $query->execute();
    $entity_ids = [];
    $privileges = NULL;

    if ($ids) {
      $privileges = $this->privilegeStorage->loadMultiple($ids);

      foreach ($privileges as $privilege) {
        $entity_ids[$privilege->entity] = $privilege->entity;
      }
    }

    return $entity_ids;
  }

  /**
   * Request the collection of members for a given entity.
   *
   * An account is processed as member of community if it has one privilege:
   *  - community_members
   *  - community_organizers
   *  - community_managers.
   * An account is processed as member of activity if it has one privilege:
   *  - activity_members
   *  - activity_maintainers
   *  - activity_organizers.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param int $pager
   *   The limit of members. NULL to get all users.
   * @param array $filters
   *   Conditions to apply on the search.
   * @param bool $exclude_current_user
   *   Does the current logged in user should be present in the fetching list.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   The database query.
   */
  public function queryMembersWithPrivileges(EntityInterface $entity, $pager = 50, array $filters = [], $exclude_current_user = TRUE) {
    // We have to get paginated users before getting privileges by users
    // because a user could have 1 or many privileges (so it's not paginable).
    // Query user(s) with privilege(s) in the given entity.
    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['user'])
      ->condition('privileges.status', 1)
      ->condition('privileges.bundle', $entity->getEntityTypeId())
      ->condition('privileges.entity', $entity->id());

    $this->alterQueryHasOneRole($query, $entity);

    // Remove current user from the list.
    if ($exclude_current_user) {
      $query->condition('privileges.user', $this->currentUser->id(), '<>');
    }

    // Remove empty filters to prevent SQL issue.
    $filters = array_filter($filters, static function ($item) {
      return !empty($item);
    });

    // Add filters criteria to the search query.
    if (!empty($filters)) {
      $filters_conditions = $query->orConditionGroup();

      if (isset($filters['firstname']) && !empty($filters['firstname'])) {
        $this->addContainsCondition($filters_conditions, 'firstname.field_firstname_value', $filters['firstname']);
      }

      if (isset($filters['lastname']) && !empty($filters['lastname'])) {
        $this->addContainsCondition($filters_conditions, 'lastname.field_lastname_value', $filters['lastname']);
      }

      if (isset($filters['mail']) && !empty($filters['mail'])) {
        // Join the users data for filters criteria.
        $query->leftJoin('users_field_data', 'user', 'user.uid = privileges.user');
        $this->addContainsCondition($filters_conditions, 'user.mail', $filters['mail']);
      }
      $query->condition($filters_conditions);
    }

    // Join the users data for filters & sorting.
    $query->leftJoin('user__field_firstname', 'firstname', 'firstname.entity_id = privileges.user');
    $query->leftJoin('user__field_lastname', 'lastname', 'lastname.entity_id = privileges.user');

    $query->orderBy('firstname.field_firstname_value', 'ASC');
    $query->orderBy('lastname.field_lastname_value', 'ASC');

    $query->groupBy('privileges.user');
    $query->groupBy('firstname.field_firstname_value');
    $query->groupBy('lastname.field_lastname_value');

    if ($pager) {
      $ids = $query->execute()->fetchAll();
      pager_default_initialize(\count($ids), $pager);
      $page = pager_find_page();
      $query->range($page * $pager, $pager);
    }

    $rows = $query->execute()->fetchAll();
    $uids = [];

    foreach ($rows as $row) {
      $uids[] = $row->user;
    }

    if (!$uids) {
      return NULL;
    }

    // Query the privileges of this users.
    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['user', 'privilege'])
      ->condition('privileges.status', 1)
      ->condition('privileges.user', $uids, 'IN')
      ->condition('privileges.bundle', $entity->getEntityTypeId())
      ->condition('privileges.entity', $entity->id());

    // Join the users data for filters criteria.
    $query->leftJoin('users_field_data', 'users', 'users.uid = privileges.user');
    $query->fields('users', ['mail']);
    $query->leftJoin('user__field_firstname', 'firstname', 'firstname.entity_id = privileges.user');
    $query->leftJoin('user__field_lastname', 'lastname', 'lastname.entity_id = privileges.user');

    $query->orderBy('firstname.field_firstname_value', 'ASC');
    $query->orderBy('lastname.field_lastname_value', 'ASC');

    // Order privilege from low to high permissions.
    // MySQL only.
    if ($entity->bundle() === 'communities') {
      $query->addExpression("find_in_set(privilege, 'community_members,community_organizers,community_managers')", 'order_privileges');
    }
    elseif ($entity->bundle() === 'activity') {
      $query->addExpression("find_in_set(privilege, 'activity_members,activity_maintainers,activity_organizers')", 'order_privileges');
    }
    $query->orderBy('order_privileges');

    $query->groupBy('privileges.privilege');
    $query->groupBy('privileges.user');
    $query->groupBy('firstname.field_firstname_value');
    $query->groupBy('lastname.field_lastname_value');
    $query->groupBy('users.mail');

    return $query;
  }

  /**
   * Request the collection of Accounts with given privilege on the entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param string $privilege
   *   The required privilege.
   * @param bool $status
   *   The required status for the privileges.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   The database query.
   */
  public function queryPrivilege(EntityInterface $entity, $privilege, $status = TRUE) {
    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['user', 'id'])
      ->condition('privileges.bundle', $entity->getEntityTypeId())
      ->condition('privileges.entity', $entity->id())
      ->condition('privileges.privilege', $privilege)
      ->condition('privileges.status', $status);

    return $query;
  }

  /**
   * Request the collection of Accounts waiting for Approval on the entity.
   *
   * Accounts are referenced as waiting for Approval when it has
   * at least one pending privilege on the community.
   * Even if the user already has the privilege or another one(s)
   * on the community.
   *  - community_members
   *  - community_organizers
   *  - community_managers.
   * Accounts are referenced as waiting for Approval when it has
   * at least one pending privilege on the activity.
   * Even if the user already has the privilege or another one(s)
   * on the activity.
   *  - activity_members
   *  - activity_maintainers
   *  - activity_organizers.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   The database query.
   */
  public function queryWaitingApproval(EntityInterface $entity) {
    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['user', 'id'])
      ->condition('privileges.status', NULL, 'is')
      ->condition('privileges.bundle', $entity->getEntityTypeId())
      ->condition('privileges.entity', $entity->id());

    $this->alterQueryHasOneRole($query, $entity);

    // Join the users data for filters criteria.
    // @todo Add Filter block by name, firstname, lastname.
    $query->leftJoin('users_field_data', 'users', 'users.uid = privileges.user');
    $query->leftJoin('user__field_lastname', 'lastname', 'lastname.entity_id = privileges.user');

    $query->orderBy('lastname.field_lastname_value', 'ASC');
    $query->orderBy('users.created', 'ASC');

    return $query;
  }

  /**
   * Request a new privilege for the user on the given entity.
   *
   * @param string $privilege
   *   The requested string privilege.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Account for who we will request de privilege.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The created privilege request.
   */
  public function request($privilege, EntityInterface $entity, ?AccountInterface $account = NULL) {
    $user = $this->currentUser;

    if ($account !== NULL) {
      $user = $account;
    }

    // Check we don't already have the same privilege.
    $privileges = $this->privilegeStorage->loadByProperties([
      'bundle' => $entity->getEntityTypeId(),
      'entity' => $entity->id(),
      'user' => $user->id(),
      'privilege' => $privilege,
    ]);

    // If the privilege already exists.
    if ($privileges) {
      $privilege = reset($privileges);

      // Previously declined ? Change as request again.
      if ($privilege->getStatus()->value === 0) {
        $privilege->setStatus(NULL);
        $privilege->reviewer = NULL;
        $privilege->reviewed = NULL;
        $privilege->save();
      }

      return $privilege;
    }

    $requested = $this->privilegeStorage->create([
      'entity' => $entity->id(),
      'user' => $user->id(),
    ]);
    $requested->setPrivilege($privilege);
    $requested->setBundle($entity->getEntityTypeId());
    $requested->save();

    return $requested;
  }

  /**
   * Alter the given query to add the condition: Should have at least on role.
   *
   * @param \Drupal\Core\Database\Query\SelectInterface $query
   *   The query to alter.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the roles.
   */
  protected function alterQueryHasOneRole(SelectInterface &$query, EntityInterface $entity) {
    if ($entity->bundle() === 'communities') {
      $or = $query->orConditionGroup();
      $or->condition('privileges.privilege', 'community_members');
      $or->condition('privileges.privilege', 'community_organizers');
      $or->condition('privileges.privilege', 'community_managers');
      $query->condition($or);
    }
    elseif ($entity->bundle() === 'activity') {
      $or = $query->orConditionGroup();
      $or->condition('privileges.privilege', 'activity_members');
      $or->condition('privileges.privilege', 'activity_maintainers');
      $or->condition('privileges.privilege', 'activity_organizers');
      $query->condition($or);
    }
  }

  /**
   * Alter the condition to search for every words on the sentence.
   *
   * Alter the given condition to add multiple OR Condition for every words of
   * the sentence.
   *
   * @param \Drupal\Core\Database\Query\ConditionInterface $condition
   *   The condition to alter.
   * @param string $field
   *   The field name to search on.
   * @param string $sentence
   *   The sentence containing words.
   */
  private function addContainsCondition(ConditionInterface $condition, $field, $sentence) {
    preg_match_all('/\w+/', $sentence, $matches);

    if (!isset($matches[0]) || empty($matches[0])) {
      return;
    }

    $words = $matches[0];
    $or = $condition->orConditionGroup();

    foreach ($words as $word) {
      $or->condition($field, '%' . $word . '%', 'LIKE');
    }
    $condition->condition($or);
  }

}
