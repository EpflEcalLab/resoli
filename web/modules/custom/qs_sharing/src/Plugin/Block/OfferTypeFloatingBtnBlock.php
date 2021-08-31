<?php

namespace Drupal\qs_sharing\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\qs_acl\Service\AccessControl;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Offer Type Floating action buttons Block.
 *
 * Expose the Floating action button of Offer Type collection page.
 *
 * @codingStandardsIgnoreFile
 * @Block(
 *     id="qs_sharing_collection_floating_action_button_block",
 *     admin_label=@Translation("Offer Type Collection Floating action button"),
 * )
 */
class OfferTypeFloatingBtnBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The node storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * The current route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $route;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $currentUser;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessControl $acl, CurrentRouteMatch $route, AccountProxyInterface $currentUser, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->acl = $acl;
    $this->route = $route;
    $this->currentUser = $currentUser;
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $community = $this->route->getParameter('community');

    $communityId = 2;

    if ($community) {
      $communityId = $community->id();
    }

    // "My Offers" floating buttons.
    $variables['floating_buttons']['action'] = [
      'url' => Url::fromRoute('qs_sharing.collection.user.offers', [
        'community' => $communityId,
        'user' => $this->currentUser->id(),
      ]),
      'label' => $this->t('qs_sharing.floating.my_offers'),
      'theme' => 'primary',
      'icon' => 'activities',
    ];

    return [
      '#theme' => 'qs_sharing_collection_floating_action_button_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url',
        ],
      ],
    ];
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
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    if (!$account->isAuthenticated()) {
      return AccessResult::forbidden();
    }

    $community = $this->route->getParameter('community');

    if (!$community) {
      return AccessResult::forbidden();
    }

    return AccessResult::allowed();
  }

}
