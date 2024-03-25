<?php

namespace Drupal\qs_activity\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\qs_activity\Service\EventManager;
use Drupal\qs_badge\Service\BadgeManager;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Expose the route to list activities > events by Theme or Date.
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
   * Request stack that controls the lifecycle of requests.
   *
   * @var \Drupal\Core\Http\RequestStack
   */
  protected $requestStack;

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
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, ActivityManager $activity_manager, EventManager $event_manager, BadgeManager $badge_manager, RequestStack $request_stack, LanguageManagerInterface $language_manager) {
    $this->acl = $acl;
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->activityManager = $activity_manager;
    $this->eventManager = $event_manager;
    $this->badgeManager = $badge_manager;
    $this->requestStack = $request_stack;
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
      $container->get('entity_type.manager'),
      $container->get('qs_activity.activity_manager'),
      $container->get('qs_activity.event_manager'),
      $container->get('qs_badge.badge_manager'),
      $container->get('request_stack'),
      $container->get('language_manager')
    );
  }

  /**
   * Collection by dates.
   */
  public function dates(TermInterface $community) {
    $variables = ['community' => $community];

    // The request should be took at the last moment, avoid it on constructor.
    $master_request = $this->requestStack->getMainRequest();

    // Get pagination date.
    $pagination_date = $master_request->query->get('date', 'now');

    try {
      $start_date = DrupalDateTime::createFromFormat('Y-m-d', $pagination_date);
    }
    catch (\Exception $e) {
      $start_date = new DrupalDateTime();
    }

    // Get pagination dates.
    $dates = $this->activityManager->getPaginationFromDate($start_date->getPhpDateTime());
    // Transform dates to DrupalDateTime objects.
    $dates = array_map(static function ($date) {
      /** @var \DateTime $date */
      return DrupalDateTime::createFromTimestamp($date->getTimestamp());
    }, $dates);
    $variables = array_merge($variables, $dates);

    // Get the only next events of each ones.
    $events = $this->eventManager->getByDate($community, $dates['start'], $dates['end']);
    $variables['events'] = $events;

    // Get the only next activities of each ones.
    $activities = $this->activityManager->getByDate($community, $dates['start'], $dates['end']);

    // Get badges.
    if (!empty($events) && !empty($activities)) {
      // From a list of Events where current user has pending subscriptions.
      $variables['badges']['subscriptions']['pendings'] = $this->badgeManager->getSubscription($events, NULL);

      // From a list of Events where current user has confirmed subscription.
      $variables['badges']['subscriptions']['confirmed'] = $this->badgeManager->getSubscription($events, TRUE);

      // From list of Activities get user privileges.
      $variables['badges']['privileges'] = $this->badgeManager->getPrivileges($activities);

      // From list of Events count pending subscriptions by given events.
      $variables['badges']['subscriptions']['pendings_guests'] = $this->badgeManager->countSubscriptions($events, NULL);

      // From list of Events count confirmed subscriptions by given events.
      $variables['badges']['subscriptions']['confirmed_guests'] = $this->badgeManager->countSubscriptions($events, TRUE);
    }

    return [
      '#theme' => 'qs_activity_collection_by_date_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => $this->getCacheTags($variables['events']),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
    $tags = [
      // Invalidated whenever any Event is updated, deleted or created.
      'node_list:event',
      // Invalidated whenever any Community is updated, deleted or created.
      'taxonomy_term_list:communities',
    ];

    if ($nodes) {
      foreach ($nodes as $node) {
        $tags[] = 'node:' . $node->id();
      }
    }

    return $tags;
  }

  /**
   * Collection by themes.
   */
  public function themes(TermInterface $community) {
    // Get the current language.
    $currentLang = $this->languageManager->getCurrentLanguage();

    // Query to retrieve all activities by theme.
    $activities_nids = $this->activityManager->getThemed($community);
    $variables = ['community' => $community];

    // The request should be took at the last moment, avoid it on constructor.
    $master_request = $this->requestStack->getMainRequest();

    // Get filters themes.
    $filtered_themes = $master_request->query->get('themes');

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

    // From the activity before, get the only next events of each ones.
    $events = $this->eventManager->getNext($activities_nids);

    if (!empty($activities_nids)) {
      $variables['activities'] = $this->nodeStorage->loadMultiple($activities_nids);
    }

    if (!empty($events)) {
      foreach ($events as $event) {
        $variables['events'][$event->field_activity->target_id] = $event;
      }
    }

    // Get badges.
    if (!empty($variables['activities'])) {
      // From list of Activities get user privileges.
      $variables['badges']['privileges'] = $this->badgeManager->getPrivileges($variables['activities']);

      // From list of Activities count pending subscriptions by activity.
      $variables['badges']['activities_subscriptions']['pendings_guests'] = $this->badgeManager->countSubscriptionsByActivities($variables['activities'], NULL);
    }

    return [
      '#theme' => 'qs_activity_collection_by_theme_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => [
          // Invalidated whenever any Event is updated, deleted or created.
          'node_list:event',
          // Invalidated whenever any Activity is updated, deleted or created.
          'node_list:activity',
          // Invalidated whenever any Community is updated, deleted or created.
          'taxonomy_term_list:communities',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
