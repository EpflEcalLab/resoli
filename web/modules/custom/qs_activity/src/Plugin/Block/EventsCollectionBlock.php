<?php

namespace Drupal\qs_activity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\qs_activity\Service\EventManager;
use Drupal\qs_badge\Service\BadgeManager;

/**
 * Collection of Events by Activity.
 *
 * This block must be shown into an Activity Node page only.
 *
 * @Block(
 *   id = "qs_activity_events_collection_block",
 *   admin_label = @Translation("Collection of Events by Activity"),
 * )
 */
class EventsCollectionBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PrivilegeManager $privilege_manager, CurrentRouteMatch $route, EventManager $event_manager, BadgeManager $badge_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->privilegeManager = $privilege_manager;
    $this->route            = $route;
    $this->eventManager     = $event_manager;
    $this->badgeManager     = $badge_manager;
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
        $container->get('current_route_match'),
        $container->get('qs_activity.event_manager'),
        $container->get('qs_badge.badge_manager')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables = [
      // List of user privilege for this activity (member|organizer|manager)
      'privileges' => [],
      'activity'   => NULL,
      'events'     => [],
    ];

    $activity = $this->route->getParameter('node');
    if ($activity) {
      $variables['activity'] = $activity;
      $privileges            = $this->privilegeManager->fetchActive($activity);
      $variables['events']   = $this->eventManager->getAllNext($activity);

      foreach ($privileges as $privilege) {
        $variables['privileges'][] = $privilege->privilege->value;
      }

      // Get badges.
      if (!empty($variables['events'])) {
        // From list of Events where current user has pending subscriptions.
        $variables['badges']['subscriptions']['pendings'] = $this->badgeManager->getSubscription($variables['events'], NULL);

        // From list of Events where current user has confirmed subscription.
        $variables['badges']['subscriptions']['confirmed'] = $this->badgeManager->getSubscription($variables['events'], TRUE);

        // From list of Activities get user privileges.
        $variables['badges']['privileges'] = $this->badgeManager->getPrivileges([$variables['activity']]);

        // From list of Events count pending subscriptions by given events.
        $variables['badges']['subscriptions']['pendings_guests'] = $this->badgeManager->countSubscriptions($variables['events'], NULL);

        // From list of Events count confirmed subscriptions by given events.
        $variables['badges']['subscriptions']['confirmed_guests'] = $this->badgeManager->countSubscriptions($variables['events'], TRUE);
      }
    }

    return [
      '#theme'     => 'qs_activity_events_collection_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
        ],
        'tags' => $this->getCacheTags($variables['events']),
      ],
      '#attached' => [
        'library' => 'quartiers_solidaires/google-map',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(array $nodes = NULL) {
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
