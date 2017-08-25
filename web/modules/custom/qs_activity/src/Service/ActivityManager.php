<?php

namespace Drupal\qs_activity\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\taxonomy\TermInterface;

/**
 * ActivityManager.
 */
class ActivityManager {
  /**
   * Relative date from now. A format accepted by strtotime().
   *
   * @var string
   */
  const MAX_DATE_LIST = '+6 months';

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
   * @param Drupal\taxonomy\TermInterface $community
   *   The community entity.
   *
   * @return integer[]
   *   A collection of Activity NID.
   */
  public function getThemed(TermInterface $community) {
    // The request should be took at the latest moment, avoid it on constructor.
    $master_request = $this->requestStack->getMasterRequest();

    $now = new DrupalDateTime();
    $max = new DrupalDateTime();
    $max->modify(self::MAX_DATE_LIST)->setTime(23, 59, 59);

    $query = $this->connection->select('node_field_data', 'activity');
    $query->fields('activity', ['nid'])
      ->condition('activity.type', 'activity')
      ->condition('activity.status', TRUE);

    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = activity.nid');

    // TOOD: Apply filter to display activities of the current community
    // which are open to anybody
    // OR
    // activities of the current community
    // where the current user is organizers|maintainers|members.
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.field_activity_target_id = activity.nid');

    // Filter activities only if at least one event finish in the futur.
    $query->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = field_activity.entity_id');
    $query->condition('field_end_at.field_end_at_value', $now, '>=');

    // Filter activities on a maximum date for performance purpose.
    $query->condition('field_end_at.field_end_at_value', $max, '<=');

    $query->leftJoin('node__field_theme', 'field_theme', 'field_theme.entity_id = activity.nid');

    $query->groupBy('activity.nid');
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
    $query->orderBy('activity.nid');

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
   * @param bool[] $autorizations
   *   The list of autorizations & the boolean value.
   * @param Drupal\taxonomy\TermInterface $community
   *   The community entity.
   *
   * @return \Drupal\node\NodeInterface
   *   The created activity.
   */
  public function create($title, array $themes, array $autorizations, TermInterface $community) {
    $activity = $this->nodeStorage->create([
      'type'            => 'activity',
      'status'          => TRUE,
      'title'           => $title,
      'field_theme'     => $themes,
      'field_community' => $community->id(),
    ]);

    foreach ($autorizations as $key => $value) {
      if ($activity->hasField($key)) {
        $activity->set($key, (bool) $value);
      }
    }

    $activity->save();
    return $activity;
  }

}
