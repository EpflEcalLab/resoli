<?php

namespace Drupal\qs_activity\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_activity\Service\ActivityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Activities Floating actions buttons Block.
 *
 * Expose the Floating actions buttons of Activities collection pages.
 *
 * @codingStandardsIgnoreFile
 *
 * @Block(
 *     id="qs_activity_collection_floating_actions_buttons_block",
 *     admin_label=@Translation("Activities Collection Floating actions buttons"),
 * )
 */
class ActivitiesFloatingBtnBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessControl $acl, CurrentRouteMatch $route, AccountProxyInterface $current_user, ActivityManager $activity_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->acl = $acl;
    $this->route = $route;
    $this->currentUser = $current_user;
    $this->activityManager = $activity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $community = $this->route->getParameter('community');
    $activity = $this->route->getParameter('activity');

    if (!$community && $activity && !$activity->get('field_community')->isEmpty()) {
      $community = $activity->field_community->entity;
    }

    // "My Activities" floating buttons.
    $variables['floating_buttons']['action'] = [
      'url' => Url::fromRoute('qs_activity.user.collection', [
        'community' => $community->id(),
        'user' => $this->currentUser->id(),
      ]),
      'label' => $this->t('qs_activity.floating.my_activities'),
      'theme' => 'primary',
      'icon' => 'activities',
    ];

    // "Add Activity" floating buttons.
    // When the user has write access on the community & never add activities
    // Replace the "My Activities" action by the "Add Activity" action.
    if (\count($this->activityManager->getByUser($community, $this->currentUser)) <= 0 && $this->acl->hasWriteAccessCommunity($community)) {
      $variables['floating_buttons']['action'] = [
        'url' => Url::fromRoute('qs_activity.activities.form.add', [
          'community' => $community->id(),
        ]),
        'label' => $this->t('qs_activity.floating.add.activity'),
        'theme' => 'primary',
        'icon' => 'plus',
      ];
    }

    return [
      '#theme' => 'qs_activity_collection_floating_actions_buttons_block',
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
      $container->get('qs_activity.activity_manager')
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
    $activity = $this->route->getParameter('activity');

    if (!$community && $activity && !$activity->get('field_community')->isEmpty()) {
      $community = $activity->field_community->entity;
    }

    if (!$community) {
      return AccessResult::forbidden();
    }

    return AccessResult::allowed();
  }

}
