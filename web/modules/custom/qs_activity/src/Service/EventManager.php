<?php

namespace Drupal\qs_activity\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;

/**
 * EventManager.
 */
class EventManager {

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
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, QueryFactory $query_factory, Connection $connection) {
    $this->nodeStorage  = $entity_type_manager->getStorage('node');
    $this->queryFactory = $query_factory;
    $this->connection   = $connection;
  }

  /**
   * Get only the next event (nearest from now) for the given activities.
   *
   * @param array $activities_nids
   *   Activities ID for which one we want the nearest next event.
   *
   * @return Drupal\node\NodeInterface[]
   *   A collection of node's Event. Oterwhise an empty array.
   */
  public function getNext(array $activities_nids) {
    $now = new DrupalDateTime();

    if (!$activities_nids) {
      return NULL;
    }

    // Get every activity that belongs to the current community.
    $query = $this->queryFactory->get('node')
      ->condition('type', 'event')
      ->condition('field_start_at', $now, '>=')
      ->condition('field_end_at', $now, '>=')
      ->condition('status', TRUE)
      ->condition('field_activity', $activities_nids, 'IN')
      ->sort('field_start_at', 'ASC')
      ->groupBy('field_activity');

    $nids = $query->execute();
    $events = NULL;
    if ($nids) {
      $events = $this->nodeStorage->loadMultiple($nids);
    }

    return $events;
  }

  /**
   * Get only the next events (nearest from now) for the given date range.
   *
   * @param Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   *
   * @return Drupal\node\NodeInterface[]
   *   A collection of node's Event. Oterwhise an empty array.
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
    $query->condition('field_start_at.field_start_at_value', [$date_start->format('c'), $date_end->format('c')], 'BETWEEN');

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
   * Get all the next event for the given activity.
   *
   * @param Drupal\node\NodeInterface $activity
   *   The activity which we want the retrieve futur events.
   *
   * @return Drupal\node\NodeInterface[]
   *   A collection of node's Event. Oterwhise an empty array.
   */
  public function getAllNext(NodeInterface $activity) {
    $now = new DrupalDateTime();

    // Get every activity that belongs to the current community.
    $query = $this->queryFactory->get('node')
      ->condition('type', 'event')
      ->condition('field_end_at', $now, '>')
      ->condition('status', TRUE)
      ->condition('field_activity', $activity->id())
      ->sort('field_start_at', 'ASC');

    $nids = $query->execute();
    $events = NULL;
    if ($nids) {
      $events = $this->nodeStorage->loadMultiple($nids);
    }

    return $events;
  }

  /**
   * Get all events.
   *
   * @param Drupal\node\NodeInterface $activity
   *   The activity which we want the retrieve futur events.
   *
   * @return Drupal\node\NodeInterface[]
   *   A collection of node's Event. Oterwhise an empty array.
   */
  public function getAll(NodeInterface $activity) {
    // Get every activity that belongs to the current community.
    $query = $this->queryFactory->get('node')
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
   * Create an Event.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   The activiity this event will belongs to.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   * @param array $data
   *   Optional data to override default activity value.
   *
   * @return \Drupal\node\NodeInterface
   *   The created event.
   */
  public function create(NodeInterface $activity, DrupalDateTime $date_start, DrupalDateTime $date_end, array $data = NULL) {

    // Change timezone for storage.
    $date_end->setTimezone(new \DateTimeZone('UTC'));
    $date_start->setTimezone(new \DateTimeZone('UTC'));

    $title = isset($data['title']) ? $data['title'] : $activity->title->value;
    $body = isset($data['body']) ? $data['body'] : $activity->body->value;
    $contact_mail = isset($data['contact_mail']) ? $data['contact_mail'] : $activity->field_contact_mail->value;
    $contact_phone = isset($data['contact_phone']) ? $data['contact_phone'] : $activity->field_contact_phone->value;
    $contribution = isset($data['contribution']) ? $data['contribution'] : NULL;
    $venue = isset($data['venue']) ? $data['venue'] : $activity->field_venue->value;
    $venue_lat = isset($data['venue_lat']) ? $data['venue_lat'] : $activity->field_venue_lat->value;
    $venue_long = isset($data['venue_long']) ? $data['venue_long'] : $activity->field_venue_long->value;

    $event = $this->nodeStorage->create([
      'type'                => 'event',
      'status'              => TRUE,
      'field_activity'      => $activity->id(),
      'field_start_at'      => $date_start->format(DATETIME_DATETIME_STORAGE_FORMAT),
      'field_end_at'        => $date_end->format(DATETIME_DATETIME_STORAGE_FORMAT),
      'title'               => $title,
      'body'                => $body,
      'field_contact_mail'  => $contact_mail,
      'field_contact_phone' => $contact_phone,
      'field_contribution'  => $contribution,
      'field_venue'         => $venue,
      'field_venue_lat'     => $venue_lat,
      'field_venue_long'    => $venue_long,
    ]);

    $event->save();
    return $event;
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

    $event->set('field_start_at', $date_start->format(DATETIME_DATETIME_STORAGE_FORMAT));
    $event->set('field_end_at', $date_end->format(DATETIME_DATETIME_STORAGE_FORMAT));

    foreach ($fields as $key => $value) {
      if ($event->hasField($key)) {
        $event->set($key, $value);
      }
    }

    $event->save();
    return $event;
  }

}
