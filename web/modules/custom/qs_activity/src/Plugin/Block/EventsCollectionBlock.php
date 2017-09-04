<?php

namespace Drupal\qs_activity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\PrivilegeManger;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\qs_activity\Service\EventManager;

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
   * @var \Drupal\qs_acl\Service\PrivilegeManger
   */
  private $privilegeManger;

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
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PrivilegeManger $privilege_manager, CurrentRouteMatch $route, EventManager $event_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->privilegeManger = $privilege_manager;
    $this->route           = $route;
    $this->eventManager    = $event_manager;
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
        $container->get('qs_acl.privilege_manger'),
        $container->get('current_route_match'),
        $container->get('qs_activity.event_manager')
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
      $privileges            = $this->privilegeManger->fetchActive($activity);
      $variables['events']   = $this->eventManager->getAllNext($activity);

      foreach ($privileges as $privilege) {
        $variables['privileges'][] = $privilege->privilege->value;
      }
    }

    return [
      '#theme'     => 'qs_activity_events_collection_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
        ],
        'tags' => [
          // Invalidated whenever any Event is updated, deleted or created.
          'node_list:event',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege:privilege',
        ],
      ],
    ];
  }

}
