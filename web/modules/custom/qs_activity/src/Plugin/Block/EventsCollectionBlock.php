<?php

namespace Drupal\qs_activity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_activity\Service\EventManager;
use Drupal\qs_badge\Service\BadgeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Collection of Events by Activity.
 *
 * This block must be shown into an Activity Node page only.
 *
 * @Block(
 *     id="qs_activity_events_collection_block",
 *     admin_label=@Translation("Collection of Events by Activity"),
 * )
 */
class EventsCollectionBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  private $privilegeManager;

  /**
   * Current Route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  private $route;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PrivilegeManager $privilege_manager, RequestStack $request_stack, CurrentRouteMatch $route, EventManager $event_manager, BadgeManager $badge_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->privilegeManager = $privilege_manager;
    $this->requestStack = $request_stack;
    $this->route = $route;
    $this->eventManager = $event_manager;
    $this->badgeManager = $badge_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $renderer = [
      '#theme' => 'qs_activity_events_collection_block',
      '#variables' => [
        // List of user privilege for this activity (member|organizer|manager)
        'privileges' => [],
        'activity' => NULL,
        'events' => [],
        'view' => 'future',
      ],
      '#cache' => [
        'contexts' => [
          'user',
        ],
      ],
      '#attached' => [
        'library' => 'quartiers_solidaires/google-map',
      ],
    ];

    $request = $this->requestStack->getCurrentRequest();

    $activity = $this->route->getParameter('node');

    if (!$activity) {
      return $renderer;
    }
    $renderer['#variables']['activity'] = $activity;

    // Build the privileges array for renderer.
    $privileges = $this->privilegeManager->fetchActive($activity);

    foreach ($privileges as $privilege) {
      $renderer['#variables']['privileges'][] = $privilege->privilege->value;
    }

    // Get the view mode (future or past events to list)
    $view = $request->get('view');

    if ($view === 'past') {
      // Get paginated 25 elements of past events.
      $renderer['#variables']['view'] = 'past';
      $events = $this->eventManager->getAllPrev($activity, 25);
    }
    else {
      $events = $this->eventManager->getAllNext($activity);
    }

    $renderer['#variables']['pager'] = [
      '#type' => 'pager',
      '#quantity' => '3',
    ];

    $renderer['#variables']['events'] = $events;
    $renderer['#cache']['tags'] = $this->getCacheTags($events);

    // Calculate badge only when events are not empty for performance.
    if (empty($events)) {
      return $renderer;
    }

    // Get badges.
    // From list of Events where current user has pending subscriptions.
    $renderer['#variables']['badges']['subscriptions']['pendings'] = $this->badgeManager->getSubscription($renderer['#variables']['events'], NULL);
    // From list of Events where current user has confirmed subscription.
    $renderer['#variables']['badges']['subscriptions']['confirmed'] = $this->badgeManager->getSubscription($renderer['#variables']['events'], TRUE);
    // From list of Activities get user privileges.
    $renderer['#variables']['badges']['privileges'] = $this->badgeManager->getPrivileges([$renderer['#variables']['activity']]);
    // From list of Events count pending subscriptions by given events.
    $renderer['#variables']['badges']['subscriptions']['pendings_guests'] = $this->badgeManager->countSubscriptions($renderer['#variables']['events'], NULL);
    // From list of Events count confirmed subscriptions by given events.
    $renderer['#variables']['badges']['subscriptions']['confirmed_guests'] = $this->badgeManager->countSubscriptions($renderer['#variables']['events'], TRUE);

    return $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // Instantiates this form class.
    return new static(
        // Load the service required to construct this class.
        $configuration,
        $plugin_id,
        $plugin_definition,
        // Load customs services used in this class.
        $container->get('qs_acl.privilege_manager'),
        $container->get('request_stack'),
        $container->get('current_route_match'),
        $container->get('qs_activity.event_manager'),
        $container->get('qs_badge.badge_manager')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(?array $nodes = NULL) {
    $tags = [
      // Invalidated whenever any Event is updated, deleted or created.
      'node_list:event',
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
