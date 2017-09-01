<?php

namespace Drupal\qs_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;

/**
 * Navigation Block.
 *
 * @Block(
 *   id = "qs_menu_navigation_block",
 *   admin_label = @Translation("Navigation Block"),
 * )
 */
class NavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
   protected $currentUser;

    /**
   * The url generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessControl $acl, CurrentRouteMatch $route, AccountProxyInterface $currentUser, UrlGeneratorInterface $url_generator) {
    $this->acl          = $acl;
    $this->route        = $route;
    $this->currentUser  = $currentUser;
    $this->urlGenerator = $url_generator;
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
        $container->get('current_route_match'),
        $container->get('current_user'),
        $container->get('url_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables['route_name'] = $this->route->getRouteName();
    $variables['current_user'] = $this->currentUser;

    $theme = [
      '#theme'     => 'qs_menu_navigation_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
        ],
      ],
    ];

    $community = $this->route->getParameter('community');
    if (!$community) {
      return;
    }

    $variables['community'] = $community;

    $variables['menu'] = [
      'activities' => [
        'label' => $this->t('qs_menu.links.activities'),
        'url' => $this->urlGenerator->generate('qs_activity.collection.themes', ['community' => $community->id()]),
        'links' => [
          'qs_activity.collection.themes' => [
            'url' => $this->urlGenerator->generate('qs_activity.collection.themes', ['community' => $community->id()]),
            'label' => $this->t('qs_menu.links.activities.themes'),
          ],
        ],
        'activated_by' => [
          'qs_activity.collection.themes',
        ],
      ],
      // 'settings'   => [
      //   'url' => $this->urlGenerator->generate('qs_supervisor.account.dashboard', ['user' => $this->currentUser->id()]),
      //   'label' => $this->t('qs_menu.links.account.dashboard'),
      //   'links' => [
      //     'qs_menu.links.account.dashboard' => [
      //       'url' => $this->urlGenerator->generate('qs_supervisor.account.dashboard', ['user' => $this->currentUser->id()]),
      //       'label' => $this->t('qs_menu.links.account.dashboard'),
      //     ],
      //   ],
      //   'activated_by' => [
      //     'qs_menu.links.account.dashboard',
      //   ],
      // ],
    ];

    return [
      '#theme'     => 'qs_menu_navigation_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
        ],
      ],
    ];
  }

}
