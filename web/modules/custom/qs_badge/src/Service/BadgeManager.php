<?php

namespace Drupal\qs_badge\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Database\Connection;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * BadgeManager.
 */
class BadgeManager {

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $currentUser;

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Class constructor.
   */
  public function __construct(AccountProxyInterface $currentUser, Connection $connection) {
    $this->currentUser = $currentUser;
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
   * From a given events node IDs, return the list w/ subscription for the user.
   *
   * @param Drupal\node\NodeInterface[] $events
   *   A collection of events.
   * @param bool $status
   *   The required status for the subscriptions.
   * @param Drupal\user\UserInterface $account
   *   The user entity.
   *
   * @return integer[]
   *   The collection of events IDs which have subscriptions.
   */
  public function getSubscription(array $events, $status = TRUE, UserInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $nids = [];
    foreach ($events as $event) {
      $nids[$event->id()] = $event->id();
    }

    $query = $this->connection->select('subscriptions', 'subscriptions');
    $query->fields('subscriptions', ['entity'])
      ->condition('subscriptions.entity', $nids, 'in')
      ->condition('subscriptions.user', $user->id());

    if ($status) {
      $query->condition('subscriptions.status', $status);
    }
    else {
      $query->condition('subscriptions.status', NULL, 'is');
    }

    $rows = $query->execute()->fetchAll();

    $subscriptions = [];
    foreach ($rows as $row) {
      $subscriptions[$row->entity] = $row->entity;
    }

    return $subscriptions;
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
