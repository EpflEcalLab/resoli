<?php

namespace Drupal\qs_badge\Service;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
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
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Class constructor.
   */
  public function __construct(AccountProxyInterface $currentUser, Connection $connection, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $currentUser;
    $this->connection = $connection;
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * Count for every days between two dates how many events occur by day.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   *
   * @return array[]
   *   A collection of dates where it occur an event. Otherwise an empty array.
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
   * Get events for every days between two dates where user has subscriptions.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   * @param bool $status
   *   The required status for the subscriptions.
   * @param \Drupal\user\UserInterface $account
   *   The user entity.
   *
   * @return array[]
   *   A collection of dates where it occur an event with subscriptions.
   *   Otherwise an empty array.
   */
  public function getSubscriptionByDates(TermInterface $community, DrupalDateTime $date_start, DrupalDateTime $date_end, $status = TRUE, UserInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $query = $this->connection->select('node_field_data', 'event');
    $query->fields('event', ['nid'])
      ->condition('event.type', 'event')
      ->condition('event.status', TRUE);

    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = event.nid');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_activity.field_activity_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('node__field_start_at', 'field_start_at', 'field_start_at.entity_id = event.nid');
    $query->condition('field_start_at.field_start_at_value', [$date_start->format('c'), $date_end->format('c')], 'BETWEEN');

    $query->leftJoin('subscriptions', 'subscriptions', 'subscriptions.entity = event.nid');
    $query->condition('subscriptions.user', $user->id());

    if ($status) {
      $query->condition('subscriptions.status', $status);
    }
    else {
      $query->condition('subscriptions.status', NULL, 'is');
    }

    $query->addExpression("DATE_FORMAT(field_start_at.field_start_at_value, '%Y-%m-%d')", 'formated_day');

    $query->orderBy('formated_day', 'ASC');

    $rows = $query->execute()->fetchAll();

    $subscriptions_events_by_day = [];
    foreach ($rows as $row) {
      $subscriptions_events_by_day[$row->formated_day][$row->nid] = $this->nodeStorage->load($row->nid);
    }

    return $subscriptions_events_by_day;
  }

  /**
   * From a given events node IDs, return the list w/ subscription for the user.
   *
   * @param \Drupal\node\NodeInterface[] $events
   *   A collection of events.
   * @param bool $status
   *   The required status for the subscriptions.
   * @param \Drupal\user\UserInterface $account
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
   * The count remove the current used to avoid false positif when only author
   * is subscribed.
   *
   * @param integer[] $events
   *   A collection of $events IDs.
   * @param bool $status
   *   The required status for the subscriptions.
   * @param \Drupal\user\UserInterface $account
   *   The user entity.
   *
   * @return array[]
   *   The collection of events IDs which have number of subscriptions.
   */
  public function countSubscriptions(array $events, $status = TRUE, UserInterface $account = NULL) {
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
      ->condition('subscriptions.user', $user->id(), '!=')
      ->groupBy('subscriptions.entity');

    $query->addExpression("COUNT(*)", 'count');

    if ($status) {
      $query->condition('subscriptions.status', $status);
    }
    else {
      $query->condition('subscriptions.status', NULL, 'is');
    }

    $rows = $query->execute()->fetchAll();

    $events = [];
    foreach ($rows as $row) {
      if ($row->count > 0) {
        $events[$row->entity] = $row->count;
      }
    }

    return $events;
  }

  /**
   * Count for the given activities IDs, if they have subscriptions.
   *
   * The count remove the current used to avoid false positif when only author
   * is subscribed.
   *
   * @param integer[] $activities
   *   A collection of $activities IDs.
   * @param bool $status
   *   The required status for the subscriptions.
   * @param \Drupal\user\UserInterface $account
   *   The user entity.
   *
   * @return array[]
   *   The collection of activities IDs which have number of subscriptions.
   */
  public function countSubscriptionsByActivities(array $activities, $status = TRUE, UserInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $nids = [];
    foreach ($activities as $activity) {
      $nids[$activity->id()] = $activity->id();
    }

    $query = $this->connection->select('subscriptions', 'subscriptions');
    $query->condition('subscriptions.user', $user->id(), '!=');

    $query->leftJoin('node__field_activity', 'field_activity', 'subscriptions.entity = field_activity.entity_id');
    $query->condition('field_activity.field_activity_target_id', $nids, 'IN');

    $query->fields('field_activity', ['field_activity_target_id'])
      ->groupBy('field_activity.field_activity_target_id');

    $query->addExpression("COUNT(*)", 'count');

    if ($status) {
      $query->condition('subscriptions.status', $status);
    }
    else {
      $query->condition('subscriptions.status', NULL, 'is');
    }

    $rows = $query->execute()->fetchAll();

    $activities = [];
    foreach ($rows as $row) {
      if ($row->count > 0) {
        $activities[$row->field_activity_target_id] = $row->count;
      }
    }

    return $activities;
  }

  /**
   * From given activities node IDs, return the list privilege for the user.
   *
   * The user's privileges are ordered from lowest to highest.
   *  - activity_members,
   *  - activity_maintainers,
   *  - activity_organizers.
   * It means you have the highest privilege in the last index of the array.
   *
   * @param \Drupal\node\NodeInterface[] $activities
   *   A collection of activities.
   * @param \Drupal\user\UserInterface $account
   *   The user entity.
   *
   * @return integer[]
   *   The collection of activities IDs which have privileges.
   */
  public function getPrivileges(array $activities, UserInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $nids = [];
    foreach ($activities as $activity) {
      $nids[$activity->id()] = $activity->id();
    }

    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['privilege', 'entity'])
      ->condition('privileges.entity', $nids, 'in')
      ->condition('privileges.user', $user->id())
      ->condition('privileges.status', 1);

    // Order privilege from low to high permissions.
    // MySQL only.
    $query->addExpression("find_in_set(privilege, 'activity_members,activity_maintainers,activity_organizers')", 'order_privileges');
    $query->orderBy('order_privileges');

    $rows = $query->execute()->fetchAll();

    $privileges = [];
    foreach ($rows as $row) {
      $privileges[$row->entity][$row->privilege] = $row->privilege;
    }

    return $privileges;
  }

  /**
   * Get the list privilege for the user for every days between two dates.
   *
   * The user's privileges are ordered from lowest to highest.
   *  - activity_members,
   *  - activity_maintainers,
   *  - activity_organizers.
   * It means you have the highest privilege in the last index of the array.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   * @param \Drupal\user\UserInterface $account
   *   The user entity.
   *
   * @return array[]
   *   A collection of dates where it occur an event with subscriptions.
   *   Otherwise an empty array.
   */
  public function getPrivilegesByDates(TermInterface $community, DrupalDateTime $date_start, DrupalDateTime $date_end, UserInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $query = $this->connection->select('node_field_data', 'event');
    $query->fields('event', ['nid'])
      ->condition('event.type', 'event')
      ->condition('event.status', TRUE);

    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = event.nid');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_activity.field_activity_target_id');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('node__field_start_at', 'field_start_at', 'field_start_at.entity_id = event.nid');
    $query->condition('field_start_at.field_start_at_value', [$date_start->format('c'), $date_end->format('c')], 'BETWEEN');

    $query->leftJoin('privileges', 'privileges', 'privileges.entity = field_activity.field_activity_target_id');
    $query->fields('privileges', ['privilege'])
      ->condition('privileges.user', $user->id())
      ->condition('privileges.status', 1);

    $query->addExpression("DATE_FORMAT(field_start_at.field_start_at_value, '%Y-%m-%d')", 'formated_day');

    // Order privilege from low to high permissions.
    // MySQL only.
    $query->addExpression("find_in_set(privilege, 'activity_members,activity_maintainers,activity_organizers')", 'order_privileges');
    $query->orderBy('formated_day', 'ASC');
    $query->orderBy('order_privileges');

    $rows = $query->execute()->fetchAll();

    $privileges = [];
    foreach ($rows as $row) {
      $privileges[$row->formated_day][$row->privilege] = $row->privilege;
    }

    return $privileges;
  }

  /**
   * From given events node IDs, return the list privilege for the user.
   *
   * The user's privileges are ordered from lowest to highest.
   *  - activity_members,
   *  - activity_maintainers,
   *  - activity_organizers.
   * It means you have the highest privilege in the last index of the array.
   *
   * @param \Drupal\node\NodeInterface[] $events
   *   A collection of events.
   * @param \Drupal\user\UserInterface $account
   *   The user entity.
   *
   * @return integer[]
   *   The collection of activities IDs which have privileges.
   */
  public function getPrivilegesByEvents(array $events, UserInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $nids = [];
    foreach ($events as $event) {
      $nids[$event->id()] = $event->id();
    }

    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['privilege', 'entity'])
      ->condition('privileges.user', $user->id())
      ->condition('privileges.status', 1);
    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.field_activity_target_id = privileges.entity');
    $query->fields('field_activity', ['field_activity_target_id'])
      ->condition('field_activity.entity_id', $nids, 'in');

    // Order privilege from low to high permissions.
    // MySQL only.
    $query->addExpression("find_in_set(privilege, 'activity_members,activity_maintainers,activity_organizers')", 'order_privileges');
    $query->orderBy('order_privileges');

    $rows = $query->execute()->fetchAll();

    $privileges = [];
    foreach ($rows as $row) {
      $privileges[$row->field_activity_target_id][$row->privilege] = $row->privilege;
    }

    return $privileges;
  }

  /**
   * From given communities node IDs, return list of privileges for given user.
   *
   * @param \Drupal\node\NodeInterface[] $communities
   *   A collection of activities.
   * @param \Drupal\user\UserInterface $account
   *   The user entity.
   *
   * @return integer[]
   *   The collection of activities IDs which have privileges.
   */
  public function getCommunityPrivileges(array $communities, UserInterface $account = NULL) {
    $user = $this->currentUser;
    if (!is_null($account)) {
      $user = $account;
    }

    $nids = [];
    foreach ($communities as $community) {
      $nids[$community->id()] = $community->id();
    }

    $query = $this->connection->select('privileges', 'privileges');
    $query->fields('privileges', ['privilege', 'entity'])
      ->condition('privileges.entity', $nids, 'in')
      ->condition('privileges.user', $user->id())
      ->condition('privileges.status', 1);

    // Order privilege from low to high permissions.
    // MySQL only.
    $query->addExpression("find_in_set(privilege, 'community_members,community_organizers,community_managers')", 'order_privileges');
    $query->orderBy('order_privileges');

    $rows = $query->execute()->fetchAll();

    $privileges = [];
    foreach ($rows as $row) {
      $privileges[$row->entity][$row->privilege] = substr($row->privilege, 0, -1);
    }

    return $privileges;
  }

}
