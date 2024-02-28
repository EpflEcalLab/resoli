<?php

namespace Drupal\qs_subscription\Service;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_subscription\Entity\Subscription;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;

/**
 * The Subscription Manager.
 */
class SubscriptionManager {

  /**
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Composes and optionally sends an email message.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mail;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  protected $privilegeManager;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

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
   * Class constructor.
   */
  public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entity_type_manager, Connection $connection, CacheTagsInvalidatorInterface $cache_tags_invalidator, PrivilegeManager $privilege_manager, MailManagerInterface $mail) {
    $this->currentUser = $currentUser;
    $this->subscriptionStorage = $entity_type_manager->getStorage('subscription');
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->connection = $connection;
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
    $this->privilegeManager = $privilege_manager;
    $this->mail = $mail;
  }

  /**
   * Confirm a previously requested subscription.
   *
   * @param \Drupal\qs_subscription\Entity\Subscription $subscription
   *   The subscription to confirm.
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
   * Get all subscriptions for the $user in the given $community.
   *
   * This method will fetch every subscriptions (excepted cancel ones).
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\user\UserInterface $user
   *   The user entity.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Event. Otherwise an empty array.
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

  /**
   * Request the collection of subscriber for a given event.
   *
   * @param \Drupal\node\NodeInterface $event
   *   The event.
   *
   * @return \Drupal\Core\Database\Query\SelectInterface
   *   The database query.
   */
  public function querySubscribers(NodeInterface $event) {
    $query = $this->connection->select('subscriptions', 'subscriptions');
    $query->fields('subscriptions', ['user', 'id'])
      ->condition('subscriptions.status', 1)
      ->condition('subscriptions.entity', $event->id());

    // Join the users data for filters criteria.
    // @todo Add Filter block by name, firstname, lastname.
    $query->leftJoin('users_field_data', 'users', 'users.uid = subscriptions.user');
    $query->fields('users', ['mail']);

    $query->orderBy('users.name', 'ASC');

    return $query;
  }

  /**
   * Request the collection of Accounts waiting for subscription on the event.
   *
   * @param \Drupal\node\NodeInterface $event
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
    // @todo Add Filter block by name, firstname, lastname.
    $query->leftJoin('users_field_data', 'users', 'users.uid = subscriptions.user');
    $query->fields('users', ['mail']);

    $query->orderBy('users.created', 'ASC');
    $query->orderBy('users.name', 'ASC');

    return $query;
  }

  /**
   * Request a new subscription for the user on the given event.
   *
   * @param \Drupal\node\NodeInterface $event
   *   The event.
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   Account for who we will request the subscription.
   * @param bool $mail_to_organizers
   *   Does the organizer(s) will receive the "waiting approval" mail ?
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The created subscription request.
   */
  public function request(NodeInterface $event, ?AccountInterface $account = NULL, $mail_to_organizers = TRUE) {
    $user = $this->currentUser;

    if ($account !== NULL) {
      $user = $account;
    }

    $activity = $event->field_activity->entity;

    // Get all organizers of this activities's event.
    $query_organizers = $this->privilegeManager->queryPrivilege($activity, 'activity_organizers');
    $rows = $query_organizers->execute()->fetchAll();

    foreach ($rows as $row) {
      $ids[$row->user] = $row->user;
    }

    // Get all maintainer of this activities's event.
    $query_maintainers = $this->privilegeManager->queryPrivilege($activity, 'activity_maintainers');
    $rows = $query_maintainers->execute()->fetchAll();

    foreach ($rows as $row) {
      $ids[$row->user] = $row->user;
    }

    // Load user with activity_organizers or activity_maintainers privilege(s)
    // & send them mail about new event subscription request.
    $accounts = NULL;

    if ($ids && $mail_to_organizers) {
      $accounts = $this->userStorage->loadMultiple($ids);

      // Load the user entity from proxy session.
      $author = $this->userStorage->load($user->id());

      foreach ($accounts as $account) {
        $this->mail->mail('qs_subscription', 'subscription_event_waiting_approval_request_organizers', $account->getEmail(), $account->getPreferredLangcode(), [
          'user' => $author,
          'event' => $event,
        ]);
      }
    }

    // Check we don't already have a subscription.
    $subscriptions = $this->subscriptionStorage->loadByProperties([
      'entity' => $event->id(),
      'user' => $user->id(),
    ]);
    $this->cacheTagsInvalidator->invalidateTags(['node:' . $event->id()]);

    // If the subscription already exists.
    if ($subscriptions) {
      $subscription = reset($subscriptions);

      // Previously declined ? Change as request again.
      if ($subscription->getStatus()->value === 0) {
        $subscription->setStatus(NULL);
        $subscription->reviewer = NULL;
        $subscription->reviewed = NULL;
        $subscription->save();
      }

      return $subscription;
    }

    $requested = $this->subscriptionStorage->create([
      'entity' => $event->id(),
      'user' => $user->id(),
    ]);
    $requested->save();

    return $requested;
  }

}
