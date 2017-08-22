<?php

namespace Drupal\qs_acl\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\Query\QueryFactory;
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
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * Class constructor.
   */
  public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entity, QueryFactory $query_factory) {
    $this->currentUser      = $currentUser;
    $this->privilegeStorage = $entity->getStorage('privilege');
    $this->queryFactory     = $query_factory;
  }

  /**
   * Request a new privilege for the user on the given entity.
   *
   * @param string $privilege_requested
   *   The requested string privilege.
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   The Drupal Content Entity for the privilege.
   * @param Drupal\Core\Session\AccountInterface $account
   *   Drupal Entity User against check access. Otherwise use current user.
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
   *   Drupal Entity User against check access. Otherwise use current user.
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

}
