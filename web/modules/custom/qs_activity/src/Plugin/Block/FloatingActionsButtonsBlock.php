<?php

namespace Drupal\qs_activity\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_activity\Service\ActivityManager;

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
   * The current route.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $route;

  /**
   * The url generator service.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $currentUser;

  /**
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessControl $acl, CurrentRouteMatch $route, UrlGeneratorInterface $urlGenerator, AccountProxyInterface $current_user, ActivityManager $activity_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->acl             = $acl;
    $this->route           = $route;
    $this->urlGenerator    = $urlGenerator;
    $this->currentUser     = $current_user;
    $this->activityManager = $activity_manager;
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
        $container->get('url_generator'),
        $container->get('current_user'),
        $container->get('qs_activity.activity_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $route_name = $this->route->getRouteName();
    $community = $this->route->getParameter('community');
    $node = $this->route->getParameter('node');

    $icon = NULL;
    $url = NULL;
    $label = $this->t('qs.previous');
    $theme = 'secondary';

    // Button - "Add Activity" or "My Activities".
    if ($community && in_array($route_name, [
      'qs_activity.collection.themes',
      'qs_activity.collection.dates',
    ])) {
      // For everybody, show a button "My Activities".
      $icon = 'activities';
      $theme = 'danger';
      $url = $this->urlGenerator->generateFromRoute('qs_activity.user.collection', [
        'community' => $community->id(),
        'user' => $this->currentUser->id(),
      ]);
      $label = $this->t('qs_activity.floating.my_activities');

      // When the user has write access on the community & never add activities
      // Display a shortcut link "Add Activity".
      if (count($this->activityManager->getByUser($community, $this->currentUser)) <= 0 && $this->acl->hasWriteAccessCommunity($community)) {
        $icon = 'plus';
        $url = $this->urlGenerator->generateFromRoute('qs_activity.activities.form.add', [
          'community' => $community->id(),
        ]);
        $label = $this->t('qs_activity.floating.add.activity');
      }
    }

    // Button - "My Subscriptions".
    if ($community && in_array($route_name, [
      'qs_calendar.collection.monthly',
      'qs_calendar.collection.weekly',
    ])) {
      $icon = 'calendar';
      $theme = 'info';
      $url = $this->urlGenerator->generateFromRoute('qs_subscription.user.collection', [
        'community' => $community->id(),
        'user' => $this->currentUser->id(),
      ]);
      $label = $this->t('qs_activity.floating.my_subscriptions');
    }

    // Button - "Add Event" or "Activity Dashboard".
    if ($node && $node->bundle() == 'activity') {
      // Button "Add Event".
      if ($this->acl->hasWriteAccessEvent($node)) {
        $icon = 'plus';
        $theme = 'primary';
        $url = $this->urlGenerator->generateFromRoute('qs_activity.events.form.add', [
          'activity' => $node->id(),
        ]);
        $label = $this->t('qs_activity.floating.add.event');
      }

      // Button "Activity Dashboard".
      if ($this->acl->hasAdminAccessActivity($node)) {
        $icon = 'activities';
        $theme = 'primary';
        $url = $this->urlGenerator->generateFromRoute('qs_activity.activities.dashboard', [
          'activity' => $node->id(),
        ]);
        $label = $this->t('qs_activity.floating.dashboard.activity');
      }
    }

    if ($community && in_array($route_name, [
      'qs_community.welcome',
    ])) {
      $icon = 'activities';
      $theme = 'info';
      $url = $this->urlGenerator->generateFromRoute('qs_supervisor.account.dashboard', [
        'user' => $this->currentUser->id(),
      ]);
      $label = $this->t('qs_supervisor.floating.my_account');

      // Button "Community Dashboard".
      if ($this->acl->hasAdminAccessCommunity($community)) {
        $icon = 'communities';
        $theme = 'danger';
        $url = $this->urlGenerator->generateFromRoute('qs_community.dashboard', [
          'community' => $community->id(),
        ]);
        $label = $this->t('qs_activity.floating.dashboard.community');
      }
    }

    $variables['url'] = $url;
    $variables['label'] = $label;
    $variables['theme'] = $theme;
    $variables['icon'] = $icon;

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
