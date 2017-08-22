<?php

namespace Drupal\qs_activity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * CollectionController.
 */
class CollectionController extends ControllerBase {

  /**
   * Relative date from now. A format accepted by strtotime().
   *
   * @var string
   */
  const MAX_DATE_LIST = '+6 months';

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * EntityTypeManagerInterface to load Node(s)
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
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
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, AccountProxyInterface $currentUser, EntityTypeManagerInterface $entity_type_manager, QueryFactory $query_factory, Connection $connection) {
    $this->acl          = $acl;
    $this->currentUser  = $currentUser;
    $this->nodeStorage  = $entity_type_manager->getStorage('node');
    $this->queryFactory = $query_factory;
    $this->connection   = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
    $container->get('current_user'),
    $container->get('entity_type.manager'),
    $container->get('entity.query'),
    $container->get('database')
    );
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Collection by themes.
   */
  public function themes(TermInterface $community) {
    $activities_nids = $this->getActivitiesByTheme($community);
    $events_nids = $this->getNextEventByActivities($activities_nids);

    if (!empty($activities_nids)) {
      $variables['activities'] = $this->nodeStorage->loadMultiple($activities_nids);
    }

    if (!empty($events_nids)) {
      $events = $this->nodeStorage->loadMultiple($events_nids);
      foreach ($events as $event) {
        $variables['events'][$event->field_activity->target_id] = $event;
      }
    }

    return [
      '#theme'     => 'qs_activity_collection_by_theme_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
        ],
        'tags' => [
          // Invalidated whenever any Community is updated, deleted or created.
          'taxonomy_term_list:communities',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

  /**
   * TODO: comment
   */
  private function getActivitiesByTheme(TermInterface $community) {
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
    // OR activities where the current user is organizers|maintainers|members.
    $query->condition('field_community.field_community_target_id', [$community->id()], 'IN');

    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.field_activity_target_id = activity.nid');

    // Filter activities only if at least one event finish in the futur.
    $query->leftJoin('node__field_end_at', 'field_end_at', 'field_end_at.entity_id = field_activity.entity_id');
    $query->condition('field_end_at.field_end_at_value', $now, '>=');

    // Filter activities on a maximum date for performance purpose.
    $query->condition('field_end_at.field_end_at_value', $max, '<=');

    $query->leftJoin('node__field_theme', 'field_theme', 'field_theme.entity_id = activity.nid');

    // TOOD: Apply filter by theme if requested..
    $query->groupBy('activity.nid');
    $query->groupBy('field_theme.field_theme_target_id');

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
   * TODO: comment
   */
  private function getNextEventByActivities(array $activities_nids) {
    $now = new DrupalDateTime();

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

    return $nids;
  }

}
