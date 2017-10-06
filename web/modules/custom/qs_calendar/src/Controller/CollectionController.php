<?php

namespace Drupal\qs_calendar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\qs_acl\Service\SubscriptionManager;
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
   * The Subscription Manager.
   *
   * @var \Drupal\qs_acl\Service\SubscriptionManager
   */
  protected $subscriptionManager;

  /**
   * Request stack that controls the lifecycle of requests.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, ActivityManager $activity_manager, EventManager $event_manager, SubscriptionManager $subscription_manager, RequestStack $request_stack) {
    $this->acl                 = $acl;
    $this->nodeStorage         = $entity_type_manager->getStorage('node');
    $this->termStorage         = $entity_type_manager->getStorage('taxonomy_term');
    $this->activityManager     = $activity_manager;
    $this->eventManager        = $event_manager;
    $this->subscriptionManager = $subscription_manager;
    $this->requestStack        = $request_stack;
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
    $container->get('qs_acl.subscription_manager'),
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
   * Collection by week.
   */
  public function weekly(TermInterface $community) {
    $variables = ['community' => $community];
    $variables['events'] = $this->getEventsByDay($community);

    // Get day from parameter.
    $master_request = $this->requestStack->getMasterRequest();
    $current_day = $master_request->query->get('day');
    $variables['current_day'] = $current_day ?: new DrupalDateTime();

    // Get badges.
    if (!empty($variables['events'])) {
    }

    return [
      '#theme'     => 'qs_calendar_collection_weekly_page',
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

  /**
   * Collection by month.
   */
  public function monthly(TermInterface $community) {
    $variables = ['community' => $community];
    $variables['events'] = $this->getEventsByDay($community);

    // Get day from parameter.
    $master_request = $this->requestStack->getMasterRequest();
    $current_day = $master_request->query->get('day');
    $variables['current_day'] = $current_day ?: new DrupalDateTime();

    // Get badges.
    if (!empty($variables['events'])) {
    }

    return [
      '#theme'     => 'qs_calendar_collection_monthly_page',
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

  /**
   * Get every events for the given community & day in GET parameter.
   *
   * @param Drupal\taxonomy\TermInterface $community
   *   The community entity.
   *
   * @return Drupal\node\NodeInterface[]
   *   A collection of node's Event. Oterwhise an empty array.
   */
  protected function getEventsByDay(TermInterface $community) {
    // The request should be took at the last moment, avoid it on constructor.
    $master_request = $this->requestStack->getMasterRequest();

    // Get pagination day.
    $pagination_day = $master_request->query->get('day');
    $day = new DrupalDateTime();
    if ($pagination_day) {
      try {
        $day = DrupalDateTime::createFromFormat('Y-m-d', $pagination_day);
      }
      catch (\Exception $e) {
        $day = new DrupalDateTime();
      }
    }

    $day_start = clone $day;
    $day_start->setTime(0, 0);

    $day_end = clone $day;
    $day_end->setTime(23, 59, 59);

    // Get the only next events of each ones.
    return $this->eventManager->getByDate($community, $day_start, $day_end);
  }

}
