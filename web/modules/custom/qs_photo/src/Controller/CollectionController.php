<?php

namespace Drupal\qs_photo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_photo\Service\PhotoManager;
use Drupal\qs_calendar\Service\CalendarBuilder;
use Drupal\qs_activity\Service\ActivityManager;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * CollectionController.
 */
class CollectionController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The entity QS Photo Manager.
   *
   * @var \Drupal\qs_photo\Service\PhotoManager
   */
  protected $photoManager;

  /**
   * The Calendar builder.
   *
   * @var \Drupal\qs_calendar\Service\CalendarBuilder
   */
  protected $calendarBuilder;

  /**
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, PhotoManager $photo_manager, CalendarBuilder $calendar_builder, ActivityManager $activity_manager) {
    $this->acl             = $acl;
    $this->photoManager    = $photo_manager;
    $this->calendarBuilder = $calendar_builder;
    $this->activityManager = $activity_manager;
    $this->nodeStorage     = $this->entityTypeManager()->getStorage('node');
    $this->termStorage     = $this->entityTypeManager()->getStorage('taxonomy_term');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('qs_photo.photo_manager'),
      $container->get('qs_calendar.calendar_builder'),
      $container->get('qs_activity.activity_manager')
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
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Collection by months.
   */
  public function months(Request $request, TermInterface $community) {
    $variables = ['community' => $community];

    // Get pagination month.
    $pagination_month = $request->query->get('month');

    $month = new DrupalDateTime();
    if ($pagination_month) {
      try {
        $month = DrupalDateTime::createFromFormat('Y-m-01', $pagination_month);
      }
      catch (\Exception $e) {
        $month = new DrupalDateTime();
      }
    }

    $date_start         = $this->calendarBuilder->getFirstMondayMonthFullWeek($month);
    $date_end           = $this->calendarBuilder->getLastSundayMonthFullWeek($month);
    $variables['dates'] = $this->calendarBuilder->build($date_start, $date_end);

    $variables['start'] = $date_start;
    $variables['end'] = $date_end;

    $date_end->setTime(23, 59, 59);

    // Get all activities in the date range.
    $activities = $this->activityManager->getByDate($community, $date_start, $date_end);

    // Get all photos for the given activities.
    $variables['photos'] = $this->photoManager->getByActivities($activities);

    return [
      '#theme'     => 'qs_photo_collection_by_month_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => $this->getCacheTags($variables['photos']),
      ],
    ];
  }

  /**
   * Collection by themes.
   */
  public function themes(Request $request, TermInterface $community) {
    $variables = ['community' => $community];
    // Get all activities by theme.
    $activities_nids = $this->activityManager->getThemed($community);

    // Get filters themes.
    $filtered_themes = $request->query->get('themes');
    if ($filtered_themes) {
      $themes = $this->termStorage->loadMultiple($filtered_themes);
      foreach ($themes as $theme) {
        $variables['themes'][] = $theme->getName();
      }
    }

    // Load 4 photos by activity.
    $activites = [];
    if (!empty($activities_nids)) {
      $activites = $this->nodeStorage->loadMultiple($activities_nids);
      $variables['activities'] = $activites;
      foreach ($activites as $activity) {
        // Get photos by activity.
        $variables['photos'][$activity->id()] = $this->photoManager->getByActivity($activity, 4);
      }
    }

    return [
      '#theme'     => 'qs_photo_collection_by_theme_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => $this->getCacheTags($activites),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(array $nodes = NULL) {
    $tags = [
      // Invalidated whenever any Event is updated, deleted or created.
      'node_list:photo',
      // Invalidated whenever any Community is updated, deleted or created.
      'taxonomy_term_list:communities',
      // Invalidated whenever any Privilege is updated, deleted or created.
      'privilege_list:privilege',
    ];
    if ($nodes) {
      foreach ($nodes as $node) {
        $tags[] = 'node:' . $node->id();
      }
    }
    return $tags;
  }

}
