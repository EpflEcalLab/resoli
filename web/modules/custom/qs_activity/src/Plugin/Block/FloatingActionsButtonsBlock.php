<?php

namespace Drupal\qs_activity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Floating actions buttons Block.
 *
 * Expose the Floating actions buttons to access privileged pages.
 *
 * @Block(
 *   id = "qs_activity_floating_actions_buttons_block",
 *   admin_label = @Translation("Floating actions buttons"),
 * )
 */
class FloatingActionsButtonsBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
    $variables = [
      'display'                         => FALSE,
      'community'                       => NULL,
      'community_has_write_access'      => FALSE,
      'community_has_dashboard_access'  => FALSE,
      'activity_has_admin_access'       => FALSE,
      'activity_has_write_access_event' => FALSE,
    ];
    $route_name = $this->route->getRouteName();

    $community = $this->route->getParameter('community');
    if ($community) {
      $variables['community'] = $community;

      if (in_array($route_name, [
        'qs_activity.collection.themes',
        'qs_activity.collection.dates',
        'qs_calendar.collection.monthly',
        'qs_calendar.collection.weekly',
      ])) {
        $variables['community_has_write_access'] = $this->acl->hasWriteAccessCommunity($community);
        $variables['community_has_dashboard_access'] = $this->acl->hasAdminAccessCommunity($community);

        if ($this->acl->hasBypass()) {
          $variables['community_has_write_access']     = TRUE;
          $variables['community_has_dashboard_access'] = TRUE;
        }
      }
    }

    $node = $this->route->getParameter('node');
    if ($node && $node->bundle() == 'activity') {
      $variables['activity_has_admin_access'] = $this->acl->hasAdminAccessActivity($node);
      $variables['activity_has_write_access_event'] = $this->acl->hasWriteAccessEvent($node);
      $variables['activity'] = $node;
      if (!$community) {
        $variables['community'] = $node->field_community->entity;
      }

      if ($this->acl->hasBypass()) {
        $variables['activity_has_admin_access']       = TRUE;
        $variables['activity_has_write_access_event'] = TRUE;
      }
    }

    return [
      '#theme'     => 'qs_activity_floating_actions_buttons_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
        ],
        'tags' => [
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
