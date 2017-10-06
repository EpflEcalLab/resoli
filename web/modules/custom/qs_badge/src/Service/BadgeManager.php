<?php

namespace Drupal\qs_badge\Service;

use Drupal\Core\Database\Connection;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * BadgeManager.
 */
class BadgeManager {

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Class constructor.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Count for every days between two dates how many events occure by day.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   *
   * @return array[]
   *   A collection of dates where it occure an event. Oterwhise an empty array.
   */
  public function countEventsByDates(TermInterface $community, DrupalDateTime $date_start, DrupalDateTime $date_end) {
    $query = $this->connection->select('node_field_data', 'event');
    $query->condition('event.type', 'event')
      ->condition('event.status', TRUE);

    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = event.nid');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_activity.field_activity_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('node__field_start_at', 'field_start_at', 'field_start_at.entity_id = event.nid');

    $query->condition('field_start_at.field_start_at_value', [$date_start->format('c'), $date_end->format('c')], 'BETWEEN');

    $query->addExpression("DATE_FORMAT(field_start_at.field_start_at_value, '%Y-%m-%d')", 'formated_day');
    $query->addExpression("COUNT(*)", 'count');

    $query->groupBy('formated_day');
    $query->orderBy('formated_day', 'ASC');

    $rows = $query->execute()->fetchAll();

    $events_by_day = [];
    foreach ($rows as $row) {
      $events_by_day[$row->formated_day] = $row->count;
    }

    return $events_by_day;
  }

  /**
   * Get for the given events IDs, if they have subscriptions.
   *
   * @param integer[] $events
   *   A collection of events IDs.
   * @param bool $status
   *   The required status for the subscriptions.
   *
   * @return array[]
   *   The collection of events IDs which have subscriptions.
   */
  public function getSubscription(array $events, $status = TRUE) {
    return [];
  }

  /**
   * Count for the given events IDs, if they have subscriptions.
   *
   * @param integer[] $events
   *   A collection of $events IDs.
   * @param bool $status
   *   The required status for the subscriptions.
   *
   * @return array[]
   *   The collection of events IDs which have subscriptions.
   */
  public function countSubscriptions(array $events, $status = TRUE) {
    return [];
  }

}
