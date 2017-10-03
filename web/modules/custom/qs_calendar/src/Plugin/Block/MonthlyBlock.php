<?php

namespace Drupal\qs_calendar\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\qs_activity\Service\EventManager;

/**
 * Calendar Monthly.
 *
 * @Block(
 *   id = "qs_calendar_monthly_block",
 *   admin_label = @Translation("Calendar Monthly"),
 * )
 */
class MonthlyBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PrivilegeManager $privilege_manager, CurrentRouteMatch $route, EventManager $event_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->privilegeManager = $privilege_manager;
    $this->route            = $route;
    $this->eventManager     = $event_manager;
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
        $container->get('qs_activity.event_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables = [];

    return [
      '#theme'     => 'qs_calendar_monthly_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => [
          // Invalidated whenever any Event is updated, deleted or created.
          'node_list:event',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
