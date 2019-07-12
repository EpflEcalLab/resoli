<?php

namespace Drupal\qs_activity\Service;

use DateTimeZone;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Database\Connection;
use Drupal\taxonomy\TermInterface;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * ActivityManager.
 */
class ActivityManager {
  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Request stack that controls the lifecycle of requests.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack, Connection $connection) {
    $this->nodeStorage  = $entity_type_manager->getStorage('node');
    $this->requestStack = $request_stack;
    $this->connection   = $connection;
  }

  /**
   * Get all activities for the given community, filtred by theme if requested.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   *
   * @return integer[]
   *   A collection of Activity NID.
   */
  public function getThemed(TermInterface $community) {
    // The request should be took at the latest moment, avoid it on constructor.
    $master_request = $this->requestStack->getMasterRequest();

    $query = $this->connection->select('node_field_data', 'activity');
    $query->fields('activity', ['nid', 'title'])
      ->condition('activity.type', 'activity')
      ->condition('activity.status', TRUE);

    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = activity.nid');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.field_activity_target_id = activity.nid');
    $query->leftJoin('node__field_theme', 'field_theme', 'field_theme.entity_id = activity.nid');

    $query->groupBy('activity.nid');
    $query->groupBy('activity.title');
    $query->groupBy('field_theme.field_theme_target_id');

    // Apply filter by theme if requested.
    $themes = $master_request->query->get('themes');
    if ($themes) {
      $or_themes = $query->orConditionGroup();
      foreach ($themes as $theme) {
        $or_themes->condition('field_theme.field_theme_target_id', $theme);
      }
      $query->condition($or_themes);
    }

    $query->orderBy('field_theme.field_theme_target_id');
    $query->orderBy('activity.title', 'ASC');

    $rows = $query->execute()->fetchAll();

    $nids = [];
    foreach ($rows as $row) {
      $nids[] = $row->nid;
    }

    return $nids;
  }

  /**
   * Create an Activity.
   *
   * @param int $title
   *   The new activity title.
   * @param int[] $themes
   *   A collection of theme TID.
   * @param bool[] $authorizations
   *   The list of authorizations & the boolean value.
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\user\UserInterface $user
   *   The user entity.
   *
   * @return \Drupal\node\NodeInterface
   *   The created activity.
   */
  public function create($title, array $themes, array $authorizations, TermInterface $community, UserInterface $user = NULL) {
    $activity = $this->nodeStorage->create([
      'type'                => 'activity',
      'status'              => TRUE,
      'title'               => $title,
      'field_theme'         => $themes,
      'field_community'     => $community->id(),
      'field_contact_name'  => $user ? $user->field_firstname->value . ' ' . $user->field_lastname->value : '',
      'field_contact_mail'  => $user ? $user->mail->value : '',
      'field_contact_phone' => $user ? $user->field_phone->value : '',
    ]);

    foreach ($authorizations as $key => $value) {
      if ($activity->hasField($key)) {
        $activity->set($key, (bool) $value);
      }
    }
    $activity->save();
    return $activity;
  }

  /**
   * Update an Activity.
   *
   * Only update given fields.
   *
   * @param \Drupal\node\NodeInterface $activity
   *   The activity to update.
   * @param array $fields
   *   The fields to update with the new value.
   *
   * @return \Drupal\node\NodeInterface
   *   The updated activity.
   */
  public function update(NodeInterface $activity, array $fields) {
    foreach ($fields as $key => $value) {
      if ($activity->hasField($key)) {
        $activity->set($key, $value);
      }
    }

    $activity->save();
    return $activity;
  }

  /**
   * Get all activities for the $user in the given $community.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user entity.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Activity. Otherwise an empty array.
   */
  public function getByUser(TermInterface $community, AccountInterface $user) {
    $query = $this->connection->select('node_field_data', 'activity');
    $query->fields('activity', ['nid', 'title'])
      ->condition('activity.type', 'activity')
      ->condition('activity.status', TRUE);

    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = activity.nid');
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('privileges', 'privileges', 'privileges.entity = activity.nid');
    $query->condition('privileges.user', $user->id());
    $query->condition('privileges.bundle', 'node');
    $query->condition('privileges.status', TRUE);

    $or = $query->orConditionGroup();
    $or->condition('privileges.privilege', 'activity_members');
    $or->condition('privileges.privilege', 'activity_maintainers');
    $or->condition('privileges.privilege', 'activity_organizers');
    $query->condition($or);

    $query->groupBy('activity.nid');
    $query->groupBy('activity.title');
    $query->orderBy('activity.title', 'ASC');

    $rows = $query->execute()->fetchAll();

    $nids = [];
    foreach ($rows as $row) {
      $nids[] = $row->nid;
    }

    $activities = [];
    if ($nids) {
      $activities = $this->nodeStorage->loadMultiple($nids);
    }

    return $activities;
  }

  /**
   * Get all activities for the $user in the given $community.
   *
   * Only the ones where the user can upload photos &
   * with at least one past event.
   * An event is considered as "Past" when it finish today.
   * To summarize, the return collection contain all Activities w/ event that
   * ends today.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user entity.
   * @param bool $allow_empty_activities
   *   If the method should only return activities without any photos.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Activity. Otherwise an empty array.
   */
  public function getByUserPhoto(TermInterface $community, AccountInterface $user, $allow_empty_activities = TRUE) {
    $today = new DrupalDateTime();
    $today->setTimezone(new \DateTimeZone('UTC'));
    $today->setTime(23, 59, 59);

    $query_base = $this->connection->select('node_field_data', 'activity');
    $query_base->fields('activity', ['nid'])
      ->condition('activity.type', 'activity')
      ->condition('activity.status', TRUE);

    $query_base->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = activity.nid');
    $query_base->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    // Filter to get only activity with at least one past event or today.
    $query_base->leftJoin('node__field_activity', 'field_activity', 'field_activity.field_activity_target_id = activity.nid');
    $query_base->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = field_activity.entity_id');
    $query_base->condition('field_end_at.field_end_at_value', $today->format('c'), '<');

    if (!$allow_empty_activities) {
      // Filter to get only activities with photos.
      $query_base->leftJoin('node__field_event', 'field_event', 'field_event.field_event_target_id = field_end_at.entity_id');
      $query_base->isNotNull('field_event.field_event_target_id');
    }

    // Get activities where user has at least one privilege higher than member.
    $query_privileges = clone $query_base;

    $query_privileges->leftJoin('privileges', 'privileges', 'privileges.entity = activity.nid');
    $query_privileges->condition('privileges.user', $user->id())
      ->condition('privileges.bundle', 'node')
      ->condition('privileges.status', TRUE);

    $or = $query_privileges->orConditionGroup();
    $or->condition('privileges.privilege', 'activity_maintainers');
    $or->condition('privileges.privilege', 'activity_organizers');
    $query_privileges->condition($or);
    $query_privileges->groupBy('activity.nid');

    $rows = $query_privileges->execute()->fetchAll();

    $privileges_activity = [];
    foreach ($rows as $row) {
      $privileges_activity[$row->nid] = $row->nid;
    }

    // Get activities where user has member privilege &
    // where photos upload are allowed for member.
    $query_member = clone $query_base;

    $query_member->leftJoin('privileges', 'privileges', 'privileges.entity = activity.nid');
    $query_member->condition('privileges.user', $user->id())
      ->condition('privileges.bundle', 'node')
      ->condition('privileges.status', TRUE)
      ->condition('privileges.privilege', 'activity_members');

    $query_member->leftJoin('node__field_member_create_gallery', 'member_create_gallery', 'member_create_gallery.entity_id = activity.nid');
    $query_member->condition('member_create_gallery.field_member_create_gallery_value', TRUE);
    $query_member->groupBy('activity.nid');

    $rows = $query_member->execute()->fetchAll();

    $members_activity = [];
    foreach ($rows as $row) {
      $members_activity[$row->nid] = $row->nid;
    }

    // Merge activities to get every ones here user can
    // upload photos.
    $nids = array_merge($privileges_activity, $members_activity);

    $activities = [];
    if ($nids) {
      $activities = $this->nodeStorage->loadMultiple($nids);
    }

    return $activities;
  }

  /**
   * Get all activities in the given date range for the community.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community entity.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_start
   *   The start date.
   * @param \Drupal\Core\Datetime\DrupalDateTime $date_end
   *   The end date.
   *
   * @return \Drupal\node\NodeInterface[]
   *   A collection of node's Activity. Otherwise an empty array.
   */
  public function getByDate(TermInterface $community, DrupalDateTime $date_start, DrupalDateTime $date_end) {
    // Get all activities in the community in the date range.
    $query = $this->connection->select('node_field_data', 'activity');
    $query->fields('activity', ['nid'])
      ->condition('activity.type', 'activity')
      ->condition('activity.status', TRUE);

    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.field_activity_target_id = activity.nid');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = activity.nid');

    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = field_activity.entity_id');
    $query->condition('field_end_at.field_end_at_value', [
      $date_start->format('c'),
      $date_end->format('c'),
    ], 'BETWEEN');

    $query->groupBy('activity.nid');

    $rows = $query->execute()->fetchAll();

    $nids = [];
    foreach ($rows as $row) {
      $nids[] = $row->nid;
    }

    $activities = [];
    if ($nids) {
      $activities = $this->nodeStorage->loadMultiple($nids);
    }

    return $activities;
  }

  /**
   * Get all dates you need to create a monthly display of events.
   *
   * @param \DateTime $start_date
   *   The start date to calculate everything from.
   *
   * @return array
   *   The start date, the end date, the next date and the prev date.
   *
   * @throws \Exception
   */
  public static function getPaginationFromDate(\DateTime $start_date) {
    $start = clone $start_date;
    $now = new \DateTime();
    $now_formatted = $now->format('Ymd');

    // If the start date is in the past, force the date to today.
    if ($start_date->format('Ymd') < $now_formatted) {
      $start = clone $now;
    }

    $start
      ->modify('Monday this week')
      ->setTime(0, 0);

    $end = clone $start;
    // We need the end date to Sunday in 4 weeks.
    $end
      ->modify('next Sunday +3 weeks')
      ->setTime(23, 59, 59);

    $prev = clone $start;
    $prev->modify('-4 weeks');

    // Make sure the prev is never before today when start date is in the future.
    $now = new \DateTime();
    if (
      $prev->format('Ymd') < $now_formatted &&
      $start_date->format('Ymd') > $now_formatted
    ) {
      $prev = clone $now;
    }

    $next = clone $end;
    $next
      ->setTime(0, 0)
      ->modify('next day');

    return [
      'start' => $start,
      'end' => $end,
      'prev' => $prev,
      'next' => $next,
    ];
  }

}
