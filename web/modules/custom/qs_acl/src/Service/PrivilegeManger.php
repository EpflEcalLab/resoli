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
   * @param Drupal\taxonomy\TermInterface $entity
   *   The Drupal Content Entity for the privilege.
   *
   * @return Drupal\Core\Session\AccountInterface[]
   *   A collection of members.
   */
  public function fetchMembers(EntityInterface $community) {
    // TODO Refactoring using $connection for futur filter, order by name user, ...
    $query = $this->queryFactory->get('privilege')
      ->condition('status', 1)
      ->condition('bundle', 'taxonomy_term')
      ->condition('entity', $community->id())
      ->groupBy('user');

    $or = $query->orConditionGroup();
    $or->condition('privilege', 'community_members');
    $or->condition('privilege', 'community_organizers');
    $or->condition('privilege', 'community_managers');
    $query->condition($or);

    $ids = $query->execute();

    if (empty($ids)) {
      return NULL;
    }

    $privileges = $this->privilegeStorage->loadMultiple($ids);
    $members = [];
    foreach ($privileges as $privilege) {
      $members[] = $privilege->user->entity;
    }

    return $members;
  }

}
