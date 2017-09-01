<?php

namespace Drupal\qs_activity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Button Activity Add Block.
 *
 * Expose a button to access the Activity Add Form.
 *
 * @Block(
 *   id = "qs_activity_btn_activity_add_block",
 *   admin_label = @Translation("Button activity add block"),
 * )
 */
class BtnActivityAddBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * Current Route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  private $route;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessControl $acl, CurrentRouteMatch $route) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->acl   = $acl;
    $this->route = $route;
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
        $container->get('qs_acl.access_control'),
        $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables = ['has_write_access' => FALSE, 'community' => NULL];

    $community = $this->route->getParameter('community');
    if ($community) {
      $variables['has_write_access'] = $this->acl->hasWriteAccessCommunity($community);
      $variables['community'] = $community;
    }

    return [
      '#theme'     => 'qs_activity_btn_activity_add_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
        ],
        'tags' => [
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege:privilege',
        ],
      ],
    ];
  }

}
