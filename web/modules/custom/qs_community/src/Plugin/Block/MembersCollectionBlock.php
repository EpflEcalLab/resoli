<?php

namespace Drupal\qs_community\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\PrivilegeManger;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Collection of Members by Community.
 *
 * @Block(
 *   id = "qs_community_members_collection_block",
 *   admin_label = @Translation("Collection of Members by Community"),
 * )
 */
class MembersCollectionBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PrivilegeManger $privilege_manager, CurrentRouteMatch $route) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->privilegeManger = $privilege_manager;
    $this->route           = $route;
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
        $container->get('current_route_match')
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
      $variables['activity']   = $activity;
      $variables['privileges'] = $this->privilegeManger->fetchActive($activity);
      $variables['events']     = $this->eventManager->getAllNext($activity);
    }

    return [
      '#theme'     => 'qs_community_members_collection_block',
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
