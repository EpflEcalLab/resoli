<?php

namespace Drupal\qs_photo\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\qs_activity\Service\EventManager;
use Drupal\qs_badge\Service\BadgeManager;
use Drupal\qs_calendar\Service\CalendarBuilder;
use Drupal\qs_photo\Service\PhotoManager;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Collection of photos by months or by themes.
 */
class CollectionController extends ControllerBase {

  /**
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

  /**
   * The Badge Manager.
   *
   * @var \Drupal\qs_badge\Service\BadgeManager
   */
  protected $badgeManager;

  /**
   * The Calendar builder.
   *
   * @var \Drupal\qs_calendar\Service\CalendarBuilder
   */
  protected $calendarBuilder;

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The entity QS Photo Manager.
   *
   * @var \Drupal\qs_photo\Service\PhotoManager
   */
  protected $photoManager;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  protected $termStorage;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, PhotoManager $photo_manager, CalendarBuilder $calendar_builder, ActivityManager $activity_manager, EventManager $event_manager, BadgeManager $badge_manager, LanguageManagerInterface $language_manager) {
    $this->acl = $acl;
    $this->photoManager = $photo_manager;
    $this->calendarBuilder = $calendar_builder;
    $this->activityManager = $activity_manager;
    $this->eventManager = $event_manager;
    $this->nodeStorage = $this->entityTypeManager()->getStorage('node');
    $this->termStorage = $this->entityTypeManager()->getStorage('taxonomy_term');
    $this->badgeManager = $badge_manager;
    $this->languageManager = $language_manager;
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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('qs_photo.photo_manager'),
      $container->get('qs_calendar.calendar_builder'),
      $container->get('qs_activity.activity_manager'),
      $container->get('qs_activity.event_manager'),
      $container->get('qs_badge.badge_manager'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
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

  /**
   * Collection by months.
   */
  public function months(Request $request, TermInterface $community) {
    $variables = [
      'community' => $community,
      'photos' => [],
    ];

    // Get pagination month.
    $pagination_month = $request->query->get('month');

    $month = new DrupalDateTime();

    if ($pagination_month) {
      try {
        $month = DrupalDateTime::createFromFormat('Y-m-d', $pagination_month);
      }
      catch (\Exception $e) {
        $month = new DrupalDateTime();
      }
    }

    $date_start = $this->calendarBuilder->getFirstMondayMonth($month);
    $date_end = $this->calendarBuilder->getLastSundayMonth($month);
    $date_start->setTime(00, 00, 00);
    $date_end->setTime(23, 59, 59);

    $variables['dates'] = $this->calendarBuilder->build($date_start, $date_end);

    $variables['start'] = $date_start;
    $variables['end'] = $date_end;

    // Get all events in the date range.
    $events = $this->eventManager->getByDate($community, $date_start, $date_end);

    if ($events) {
      // Get all photos for the given events.
      $variables['photos'] = $this->photoManager->getByEvents($events);
    }

    return [
      '#theme' => 'qs_photo_collection_by_month_page',
      '#variables' => $variables,
      '#attached' => [
        'library' => [
          'quartiers_solidaires/photoswipe',
        ],
      ],
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
    // Get the current language.
    $currentLang = $this->languageManager->getCurrentLanguage();

    $variables = ['community' => $community];
    // Get all activities by theme.
    $activities_nids = $this->activityManager->getThemed($community);

    // Get filters themes.
    $filtered_themes = $request->query->get('themes');

    if ($filtered_themes) {
      $themes = $this->termStorage->loadMultiple($filtered_themes);

      foreach ($themes as $theme) {
        // Check if has translation.
        if ($theme->hasTranslation($currentLang->getId())) {
          $theme = $theme->getTranslation($currentLang->getId());
        }
        $variables['themes'][] = $theme->getName();
      }
    }

    // Load 4 photos by activity.
    $activites = [];
    $variables['photos'] = [];

    if (!empty($activities_nids)) {
      $activites = $this->nodeStorage->loadMultiple($activities_nids);
      $variables['activities'] = $activites;

      foreach ($activites as $activity) {
        // Get photos by activity.
        if ($photos = $this->photoManager->getByActivity($activity, 4)) {
          $variables['photos'][$activity->id()] = $photos;
        }
      }
    }

    // Get badges.
    if (!empty($variables['activities'])) {
      // From list of Activities get user privileges.
      $variables['badges']['privileges'] = $this->badgeManager->getPrivileges($variables['activities']);
    }

    return [
      '#theme' => 'qs_photo_collection_by_theme_page',
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

}
