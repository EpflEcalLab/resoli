<?php

namespace Drupal\qs_acl\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Database\Connection;

/**
 * SubscriptionManager.
 */
class SubscriptionManager {
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
   * Get for the given entity IDs, if they have w/ the user some subscriptions.
   *
   * @param integer[] $entities
   *   A collection of entites IDs.
   * @param bool $status
   *   The required status for the subscriptions.
   * @param Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return array[]
   *   A collection of Entities ID.
   */
  public function getSubscription(array $entities, $status = TRUE, AccountInterface $account = NULL) {
    return [];
  }

  /**
   * Count for the given entity IDs, if they have w/ the user some subscription.
   *
   * @param integer[] $entities
   *   A collection of entites IDs.
   * @param bool $status
   *   The required status for the subscriptions.
   * @param Drupal\Core\Session\AccountInterface $account
   *   User used to check access. Otherwise use current user.
   *
   * @return array[]
   *   A collection of Entities ID.
   */
  public function countSubscriptions(array $entities, $status = TRUE, AccountInterface $account = NULL) {
    return [];
  }

}
