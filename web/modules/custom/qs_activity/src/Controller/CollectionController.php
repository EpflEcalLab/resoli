<?php

namespace Drupal\qs_activity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\qs_badge\Service\BadgeManager;
use Drupal\qs_activity\Service\EventManager;
use Symfony\Component\HttpFoundation\RequestStack;
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
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

  /**
   * The entity QS Event Manager.
   *
   * @var \Drupal\qs_activity\Service\EventManager
   */
  protected $eventManager;

  /**
   * The Badge Manager.
   *
   * @var \Drupal\qs_badge\Service\BadgeManager
   */
  protected $badgeManager;

  /**
   * Request stack that controls the lifecycle of requests.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, ActivityManager $activity_manager, EventManager $event_manager, BadgeManager $badge_manager, RequestStack $request_stack) {
    $this->acl             = $acl;
    $this->nodeStorage     = $entity_type_manager->getStorage('node');
    $this->termStorage     = $entity_type_manager->getStorage('taxonomy_term');
    $this->activityManager = $activity_manager;
    $this->eventManager    = $event_manager;
    $this->badgeManager    = $badge_manager;
    $this->requestStack    = $request_stack;
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
    $container->get('request_stack')
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
    // Query to retreive all activities by theme.
    $activities_nids = $this->activityManager->getThemed($community);
    $variables = ['community' => $community];

    // The request should be took at the last moment, avoid it on constructor.
    $master_request = $this->requestStack->getMasterRequest();

    // Get filters themes.
    $filtered_themes = $master_request->query->get('themes');
    if ($filtered_themes) {
      $themes = $this->termStorage->loadMultiple($filtered_themes);
      foreach ($themes as $theme) {
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

    return [
      '#theme'     => 'qs_activity_collection_by_theme_page',
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

  /**
   * Collection by dates.
   */
  public function dates(TermInterface $community) {
    $variables = ['community' => $community];

    // The request should be took at the last moment, avoid it on constructor.
    $master_request = $this->requestStack->getMasterRequest();

    // Get pagination date.
    $pagination_date = $master_request->query->get('date');
    $date = new DrupalDateTime();
    if ($pagination_date) {
      try {
        $date = DrupalDateTime::createFromFormat('Y-m-d', $pagination_date);
      }
      catch (\Exception $e) {
        $date = new DrupalDateTime();
      }
    }

    $start = clone $date;
    $start->setTime(0, 0);

    $end = clone $start;
    // We need the date in 4 weeks but not including the day in EXACTLY 4 weeks,
    // i.e. just the second before :)
    $end->modify('+3 weeks +6 days');
    $end->setTime(23, 59, 59);

    $prev = clone $start;
    $prev->modify('-4 weeks');

    $next = clone $end;
    $next->modify('next day');

    $variables['start'] = $start;
    $variables['end'] = $end;
    $variables['prev'] = $prev;
    $variables['next'] = $next;

    // Get the only next events of each ones.
    $events = $this->eventManager->getByDate($community, $start, $end);
    $variables['events'] = $events;

    // Get badges.
    if (!empty($events)) {
      // For a list of Events IDs where current user has rejected subscription.
      // Used to know when display the rejected subscription button & badge(s).
      $variables['user_events_has_rejected_subscription'] = $this->badgeManager->getSubscription($events, 0);
      // For a list of Events IDs where current user has confirmed subscription.
      // Used to know when display the confirmed subscription button & badge(s).
      $variables['user_events_has_confirmed_subscription'] = $this->badgeManager->getSubscription($events, 1);
      // For a list of Events IDs number of pending subscriptions.
      // Used to know when display for organizer or maintainer badge(s).
      $variables['events_count_pending_subscriptions'] = $this->badgeManager->countSubscriptions($events, NULL);
      // For a list of Events IDs number of subscriptions.
      // Used to know when display for organizer or maintainer badge(s).
      $variables['events_count_confirmed_subscriptions'] = $this->badgeManager->countSubscriptions($events, 1);
    }

    return [
      '#theme'     => 'qs_activity_collection_by_date_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => [
          // Invalidated whenever any Event is updated, deleted or created.
          'node_list:event',
          // Invalidated whenever any Community is updated, deleted or created.
          'taxonomy_term_list:communities',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
