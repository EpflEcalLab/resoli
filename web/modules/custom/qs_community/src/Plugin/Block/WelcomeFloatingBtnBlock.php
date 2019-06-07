<?php

namespace Drupal\qs_community\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Url;

/**
 * Welcome Floating actions buttons Block.
 *
 * Expose the Floating actions buttons of the community welcome page.
 *
 * @codingStandardsIgnoreFile
 * @Block(
 *   id = "qs_community_welcome_floating_actions_buttons_block",
 *   admin_label = @Translation("Community Welcome Floating actions buttons"),
 * )
 */
class WelcomeFloatingBtnBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The current route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $route;

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
  protected function blockAccess(AccountInterface $account) {
    if (!$account->isAuthenticated()) {
      return AccessResult::forbidden();
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $community = $this->route->getParameter('community');
    $variables = [];

    // Show the "Manage Community" button only to people with the proper ACL.
    if ($community && $this->acl->hasAdminAccessCommunity($community)) {
      $variables['floating_buttons'][] = [
        'url'     => Url::fromRoute('qs_community.dashboard', ['community' => $community->id()]),
        'label'   => $this->t('qs_menu.links.account.communities'),
        'theme'   => 'invert',
        'icon'    => 'communities-sm',
      ];
    }

    return [
      '#theme'     => 'qs_community_welcome_floating_actions_buttons_block',
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
