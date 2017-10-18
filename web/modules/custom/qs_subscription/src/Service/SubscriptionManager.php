<?php

namespace Drupal\qs_subscription\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Database\Connection;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
use Drupal\qs_subscription\Entity\Subscription;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Datetime\DrupalDateTime;

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
   * The Subscription Storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  private $subscriptionStorage;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

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
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * Class constructor.
   */
  public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entity_type_manager, QueryFactory $query_factory, Connection $connection, CacheTagsInvalidatorInterface $cache_tags_invalidator) {
    $this->currentUser          = $currentUser;
    $this->subscriptionStorage  = $entity_type_manager->getStorage('subscription');
    $this->nodeStorage          = $entity_type_manager->getStorage('node');
    $this->queryFactory         = $query_factory;
    $this->connection           = $connection;
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
  }

  /**
   * Request a new subscription for the user on the given event.
   *
   * @param Drupal\node\NodeInterface $event
   *   The event.
   * @param Drupal\Core\Session\AccountInterface $account
   *   Account for who we will request the subscription.
   *
   * @return Drupal\Core\Entity\EntityInterface
   *   The created subscription request.
   */
  public function request(NodeInterface $event, AccountInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    // Check we don't already have a subscription.
    $subscriptions = $this->subscriptionStorage->loadByProperties([
      'entity' => $event->id(),
      'user'   => $user->id(),
    ]);
    $this->cacheTagsInvalidator->invalidateTags(['node:' . $event->id()]);

    // If the subscription already exists.
    if ($subscriptions) {
      $subscription = reset($subscriptions);

      // Previously declined ? Change as request again.
      if ($subscription->getStatus()->value == 0) {
        $subscription->setStatus(NULL);
        $subscription->reviewer = NULL;
        $subscription->reviewed = NULL;
        $subscription->save();
      }
      return $subscription;
    }

    $requested = $this->subscriptionStorage->create([
      'entity' => $event->id(),
      'user'   => $user->id(),
    ]);
    $requested->save();

    return $requested;
  }

  /**
   * Confirm a previously requested subscription.
   *
   * @param \Drupal\qs_subscription\Entity\Subscription $subscription
   *   The subscription to confirme.
   *
   * @return \Drupal\qs_subscription\Entity\Subscription
   *   The confirmed subscription.
   */
  public function confirm(Subscription $subscription) {
    $reviewer = $this->currentUser;

    $subscription->setStatus(1);
    $subscription->setReviewer($reviewer);
    $subscription->setReviewedTime(time());
    $subscription->save();

    $this->cacheTagsInvalidator->invalidateTags(['node:' . $subscription->entity->value]);

    return $subscription;
  }

  /**
   * Decline a previously requested subscription.
   *
   * @param \Drupal\qs_subscription\Entity\Subscription $subscription
   *   The subscription to decline.
   *
   * @return \Drupal\qs_subscription\Entity\Subscription
   *   The declined subscription.
   */
  public function decline(Subscription $subscription) {
    $reviewer = $this->currentUser;

    $subscription->setStatus(0);
    $subscription->setReviewer($reviewer);
    $subscription->setReviewedTime(time());
    $subscription->save();

    $this->cacheTagsInvalidator->invalidateTags(['node:' . $subscription->entity->value]);

    return $subscription;
  }

  /**
   * Request the collection of subscriber for a given event.
   *
   * @param Drupal\node\NodeInterface $event
   *   The event.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   The database query.
   */
  public function querySubscribers(NodeInterface $event) {
    $query = $this->connection->select('subscriptions', 'subscriptions');
    $query->fields('subscriptions', ['user'])
      ->condition('subscriptions.status', 1)
      ->condition('subscriptions.entity', $event->id());

    // Join the users data for filters criteria.
    // TODO: Add Filter block by name, firstname, lastname.
    $query->leftJoin('users_field_data', 'users', 'users.uid = subscriptions.user');

    $query->orderBy('users.name', 'ASC');

    return $query;
  }

  /**
   * Request the collection of Accounts waiting for subscription on the event.
   *
   * @param Drupal\node\NodeInterface $event
   *   The event.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   The database query.
   */
  public function queryWaitingApproval(NodeInterface $event) {
    $query = $this->connection->select('subscriptions', 'subscriptions');
    $query->fields('subscriptions', ['user', 'id'])
      ->condition('subscriptions.status', NULL, 'is')
      ->condition('subscriptions.entity', $event->id());

    // Join the users data for filters criteria.
    // TODO: Add Filter block by name, firstname, lastname.
    $query->leftJoin('users_field_data', 'users', 'users.uid = subscriptions.user');

    $query->orderBy('users.created', 'ASC');
    $query->orderBy('users.name', 'ASC');

    return $query;
  }

  /**
   * Get all subscriptions for the $user in the given $community.
   *
   * This method will fetch every subscriptions (excepted cancel ones).
   *
   * @param Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param Drupal\user\UserInterface $user
   *   The user entity.
   *
   * @return Drupal\node\NodeInterface[]
   *   A collection of node's Event. Oterwhise an empty array.
   */
  public function getByUser(TermInterface $community, UserInterface $user) {
    $now = new DrupalDateTime();

    $query = $this->connection->select('node_field_data', 'event');
    $query->fields('event', ['nid'])
      ->condition('event.type', 'event')
      ->condition('event.status', TRUE);

    // Get only event in the given community.
    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = event.nid');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_activity.field_activity_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    // Get only event in the futur.
    $query->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = event.nid');
    $query->condition('field_end_at.field_end_at_value', $now, '>');

    // Get only given user subscriptions.
    $query->leftJoin('subscriptions', 'subscriptions', 'subscriptions.entity = event.nid');
    $query->condition('subscriptions.user', $user->id());

    $or = $query->orConditionGroup();
    $or->condition('subscriptions.status', NULL, 'is');
    $or->condition('subscriptions.status', 1);
    $query->condition($or);

    $query->groupBy('event.nid');
    $rows = $query->execute()->fetchAll();

    $nids = [];
    foreach ($rows as $row) {
      $nids[] = $row->nid;
    }

    $events = [];
    if ($nids) {
      $events = $this->nodeStorage->loadMultiple($nids);
    }

    return $events;
  }

}
