<?php

namespace Drupal\qs_activity\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_subscription\Service\SubscriptionManager;
use Drupal\taxonomy\TermInterface;

/**
 * The Event Manager.
 */
class EventManager {

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
   * The pager manager.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface
   */
  protected $pagerManager;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  protected $privilegeManager;

  /**
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  protected $subscriptionManager;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $connection, PrivilegeManager $privilege_manager, SubscriptionManager $subscription_manager, MailManagerInterface $mail, PagerManagerInterface $pager_manager) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->connection = $connection;
    $this->privilegeManager = $privilege_manager;
    $this->subscriptionManager = $subscription_manager;
    $this->mail = $mail;
    $this->pagerManager = $pager_manager;
  }

  /**
   * Create an Event.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   The activity this event will belongs to.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   * @param array|null $data
   *   Optional data to override default activity value.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *
   * @return \Drupal\node\NodeInterface
   *   The created event.
   */
  public function create(NodeInterface $activity, DrupalDateTime $date_start, DrupalDateTime $date_end, ?array $data = NULL) {

    // Change timezone for storage.
    $date_end->setTimezone(new \DateTimeZone('UTC'));
    $date_start->setTimezone(new \DateTimeZone('UTC'));

    $title = isset($data['title']) ? $data['title'] : $activity->title->value;
    $body = isset($data['body']) ? $data['body'] : $activity->body->value;
    $contact_name = isset($data['contact_name']) ? $data['contact_name'] : $activity->field_contact_name->value;
    $contact_mail = isset($data['contact_mail']) ? $data['contact_mail'] : $activity->field_contact_mail->value;
    $contact_phone = isset($data['contact_phone']) ? $data['contact_phone'] : $activity->field_contact_phone->value;
    $contribution = isset($data['contribution']) ? $data['contribution'] : NULL;
    $venue = isset($data['venue']) ? $data['venue'] : $activity->field_venue->value;
    $venue_lat = isset($data['venue_lat']) ? $data['venue_lat'] : $activity->field_venue_lat->value;
    $venue_long = isset($data['venue_long']) ? $data['venue_long'] : $activity->field_venue_long->value;

    $event = $this->nodeStorage->create([
      'type' => 'event',
      'status' => TRUE,
      'field_activity' => $activity->id(),
      'field_start_at' => $date_start->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT),
      'field_end_at' => $date_end->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT),
      'title' => $title,
      'body' => ['value' => $body, 'format' => 'light_html'],
      'field_contact_name' => $contact_name,
      'field_contact_mail' => $contact_mail,
      'field_contact_phone' => $contact_phone,
      'field_contribution' => $contribution,
      'field_venue' => $venue,
      'field_venue_lat' => $venue_lat,
      'field_venue_long' => $venue_long,
    ]);

    $event->save();

    return $event;
  }

  /**
   * Get all events.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   The activity which we want the retrieve future events.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Event. Otherwise an empty array.
   */
  public function getAll(NodeInterface $activity) {
    // Get every activity that belongs to the current community.
    $query = $this->nodeStorage->getQuery()
      ->condition('type', 'event')
      ->condition('field_activity', $activity->id());

    $nids = $query->execute();
    $events = NULL;

    if ($nids) {
      $events = $this->nodeStorage->loadMultiple($nids);
    }

    return $events;
  }

  /**
   * Get all the next event for the given activity.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   The activity which we want the retrieve future events.
   * @param int $limit
   *   Maximum of events to retrieve.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Event. Otherwise an empty array.
   */
  public function getAllNext(NodeInterface $activity, $limit = NULL) {
    $now = new DrupalDateTime();

    // Get every activity that belongs to the current community.
    $query = $this->nodeStorage->getQuery()
      ->condition('type', 'event')
      ->condition('field_end_at', $now, '>')
      ->condition('status', TRUE)
      ->condition('field_activity', $activity->id())
      ->sort('field_start_at', 'ASC');

    if ($limit) {
      $rows = $query->execute();
      $this->pagerManager->createPager(\count($rows), $limit);
      $query->range($this->pagerManager->findPage() * $limit, $limit);
    }

    $nids = $query->execute();
    $events = NULL;

    if ($nids) {
      $events = $this->nodeStorage->loadMultiple($nids);
    }

    return $events;
  }

  /**
   * Get all the previous event for the given activity.
   *
   * An event is considered as "Past" when it finish today.
   * To summarize, the return collection contain all Activities w/ event that
   * ends today.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   The activity which we want the retrieve past events.
   * @param int $limit
   *   Maximum of events to retrieve.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Event. Otherwise an empty array.
   */
  public function getAllPrev(NodeInterface $activity, $limit = NULL) {
    $today = new DrupalDateTime();
    $today->setTimezone(new \DateTimeZone('UTC'));
    $today->setTime(23, 59, 59);

    // Get every activity that belongs to the current community.
    $query = $this->nodeStorage->getQuery()
      ->condition('type', 'event')
      ->condition('field_end_at', $today->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '<')
      ->condition('status', TRUE)
      ->condition('field_activity', $activity->id())
      ->sort('field_end_at', 'DESC');

    if ($limit) {
      $rows = $query->execute();
      $this->pagerManager->createPager(\count($rows), $limit);
      $query->range($this->pagerManager->findPage() * $limit, $limit);
    }

    $nids = $query->execute();
    $events = NULL;

    if ($nids) {
      $events = $this->nodeStorage->loadMultiple($nids);
    }

    return $events;
  }

  /**
   * Get only the events (nearest from $date_start) for the given date range.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Event. Otherwise an empty array.
   */
  public function getByDate(TermInterface $community, DrupalDateTime $date_start, DrupalDateTime $date_end) {
    $query = $this->connection->select('node_field_data', 'event');
    $query->fields('event', ['nid'])
      ->condition('event.type', 'event')
      ->condition('event.status', TRUE);

    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = event.nid');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_activity.field_activity_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('node__field_start_at', 'field_start_at', 'field_start_at.entity_id = event.nid');
    $query->condition('field_start_at.field_start_at_value', [
      $date_start->format('c'),
      $date_end->format('c'),
    ], 'BETWEEN');

    $query->orderBy('field_start_at.field_start_at_value', 'ASC');

    $rows = $query->execute()->fetchAll();

    $nids = [];

    foreach ($rows as $row) {
      $nids[$row->nid] = $row->nid;
    }

    $events = NULL;

    if ($nids) {
      $events = $this->nodeStorage->loadMultiple($nids);
    }

    return $events;
  }

  /**
   * Get only the next event (nearest from now) for the given activities.
   *
   * @param array $activities_nids
   *   Activities ID for which one we want the nearest next event.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Event. Otherwise an empty array.
   */
  public function getNext(array $activities_nids) {
    $now = new DrupalDateTime();
    $now->setTimezone(new \DateTimeZone('UTC'));

    if (!$activities_nids) {
      return NULL;
    }

    // Get every activity that belongs to the current community.
    $query = $this->connection->select('node_field_data', 'event');
    $query->condition('event.status', TRUE);

    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = event.nid');
    $query->condition('field_activity.field_activity_target_id', $activities_nids, 'IN');

    $query->leftJoin('node__field_start_at', 'field_start_at', 'field_start_at.entity_id = event.nid');
    $query->condition('field_start_at.field_start_at_value', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=');

    $query->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = event.nid');
    $query->condition('field_end_at.field_end_at_value', $now->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT), '>=');

    $query->fields('field_activity', ['field_activity_target_id']);
    $query->fields('event', ['nid']);
    $query->addExpression('MIN(field_start_at.field_start_at_value)');
    $query->groupBy('event.nid');
    $query->groupBy('field_start_at.field_start_at_value');
    $query->groupBy('field_activity.field_activity_target_id');
    $query->orderBy('field_start_at.field_start_at_value', 'ASC');
    $query->orderBy('field_activity.field_activity_target_id', 'ASC');

    $rows = $query->execute()->fetchAll();

    if (!$rows) {
      return NULL;
    }

    $nids = [];

    foreach ($rows as $row) {
      // Take only 1 event (the first one - which is the closest to now)
      // by activity.
      if (isset($nids[$row->field_activity_target_id])) {
        continue;
      }

      $nids[$row->field_activity_target_id] = $row->nid;
    }

    return $this->nodeStorage->loadMultiple($nids);
  }

  /**
   * Send a mail to alert users about the deletion of event.
   *
   * Sent mails to subscribers & all activity_organizers/activity_maintainers.
   *
   * @param \Drupal\node\NodeInterface $event
   *   The deleted event.
   * @param \Drupal\Core\Session\AccountInterface $author
   *   The author of deletion.
   */
  public function sendDeleted(NodeInterface $event, AccountInterface $author) {
    $activity = $event->field_activity->entity;
    $ids = [];

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

    // Get all subscribed users of event.
    $query_subscribers = $this->subscriptionManager->querySubscribers($event);
    $rows = $query_subscribers->execute()->fetchAll();

    foreach ($rows as $row) {
      $ids[$row->user] = $row->user;
    }

    // Load user with community_managers privilege & send them mail.
    $users = NULL;

    if ($ids) {
      $users = $this->userStorage->loadMultiple($ids);

      // Load the user entity from proxy session.
      $author = $this->userStorage->load($author->id());

      foreach ($users as $user) {
        $this->mail->mail('qs_activity', 'activity_event_deleted', $user->getEmail(), $user->getPreferredLangcode(), [
          'author' => $author,
          'event' => $event,
        ]);
      }
    }
  }

  /**
   * Send mail that event has change to subscribers, organizer(s) & maintainers.
   *
   * Sent mails to subscribers & all activity_organizers/activity_maintainers.
   *
   * @param \Drupal\node\NodeInterface $original_event
   *   The original event.
   * @param \Drupal\node\NodeInterface $updated_event
   *   The updated event.
   * @param \Drupal\Core\Session\AccountInterface $author
   *   The author of edition.
   */
  public function sendUpdated(NodeInterface $original_event, NodeInterface $updated_event, AccountInterface $author) {
    $activity = $updated_event->field_activity->entity;
    $ids = [];

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

    // Get all subscribed users of event.
    $query_subscribers = $this->subscriptionManager->querySubscribers($updated_event);
    $rows = $query_subscribers->execute()->fetchAll();

    foreach ($rows as $row) {
      $ids[$row->user] = $row->user;
    }

    // Load user with community_managers privilege & send them mail.
    $users = NULL;

    if ($ids) {
      $users = $this->userStorage->loadMultiple($ids);

      // Load the user entity from proxy session.
      $author = $this->userStorage->load($author->id());

      foreach ($users as $user) {
        $this->mail->mail('qs_activity', 'activity_event_updated', $user->getEmail(), $user->getPreferredLangcode(), [
          'author' => $author,
          'original_event' => $original_event,
          'updated_event' => $updated_event,
        ]);
      }
    }
  }

  /**
   * Update an Event.
   *
   * Only update given fields.
   *
   * @param \Drupal\node\NodeInterface $event
   *   The event to update.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   * @param array $fields
   *   The fields to update with the new value.
   *
   * @return \Drupal\node\NodeInterface
   *   The updated activity.
   */
  public function update(NodeInterface $event, DrupalDateTime $date_start, DrupalDateTime $date_end, array $fields) {
    // Change timezone for storage.
    $date_start->setTimezone(new \DateTimeZone('UTC'));
    $date_end->setTimezone(new \DateTimeZone('UTC'));

    $event->set('field_start_at', $date_start->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT));
    $event->set('field_end_at', $date_end->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT));

    foreach ($fields as $key => $value) {
      if ($event->hasField($key)) {
        $event->set($key, $value);
      }
    }

    $event->save();

    return $event;
  }

}
