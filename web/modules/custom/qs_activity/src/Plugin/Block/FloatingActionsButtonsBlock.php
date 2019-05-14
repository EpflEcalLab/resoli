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
use Drupal\qs_acl\Service\PrivilegeManager;

/**
 * Floating actions buttons Block.
 *
 * Expose the Floating actions buttons to access privileged pages.
 *
 * @codingStandardsIgnoreFile
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
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  protected $privilegeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccessControl $acl, CurrentRouteMatch $route, UrlGeneratorInterface $urlGenerator, AccountProxyInterface $current_user, ActivityManager $activity_manager, PrivilegeManager $privilege_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->acl              = $acl;
    $this->route            = $route;
    $this->urlGenerator     = $urlGenerator;
    $this->currentUser      = $current_user;
    $this->activityManager  = $activity_manager;
    $this->privilegeManager = $privilege_manager;
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
      $container->get('qs_activity.activity_manager'),
      $container->get('qs_acl.privilege_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $route_name = $this->route->getRouteName();
    $community = $this->route->getParameter('community');
    $node = $this->route->getParameter('node');
    $activity = $this->route->getParameter('activity');

    if (!$community && $activity && !$activity->get('field_community')->isEmpty()) {
      $community = $activity->field_community->entity;
    }

    $variables['buttons'] = [];
    $icon = NULL;
    $url = NULL;
    $label = $this->t('qs.previous');
    $theme = 'secondary';
    $classes = [];

    // Button - "Community Dashboard".
    if ($community && in_array($route_name, [
      // 'qs_community.dashboard',
      // 'qs_community.members',
      // 'qs_community.waiting_approval',
    ])) {
      // For everybody, show a button "My Activities".
      // $icon = 'communities-sm';
      // $theme = 'danger';
      // $url =
      //   $this->urlGenerator->generateFromRoute('qs_community.dashboard', [
      //     'community' => $community->id(),
      //   ]);
      // $label = $this->t('qs_menu.links.account.communities');

      // if ($route_name == 'qs_community.members') {
      //   $label = $this->t('qs_community.dashboard.members');
      //   $icon = 'happy';
      // }
      // elseif ($route_name == 'qs_community.waiting_approval') {
      //   $label = $this->t('qs_community.dashboard.waiting_approval');
      //   $icon = 'wait';
      // }
    }

    // Button - "Add Activity" or "My Activities".
    if ($community && in_array($route_name, [
      'qs_activity.collection.themes',
      'qs_activity.collection.dates',
      'qs_activity.activities.form.add',
      'qs_activity.user.collection',
    ])) {
      // For everybody, show a button "My Activities".
//      $icon = 'activities';
//      $theme = 'primary';
//      $url = $this->urlGenerator->generateFromRoute('qs_activity.user.collection', [
//        'community' => $community->id(),
//        'user' => $this->currentUser->id(),
//      ]);
//      $label = $this->t('qs_activity.floating.my_activities');

      // When the user has write access on the community & never add activities
      // Display a shortcut link "Add Activity".
      if (count($this->activityManager->getByUser($community, $this->currentUser)) <= 0 && $this->acl->hasWriteAccessCommunity($community)) {
        $icon = 'plus';
        $theme = 'primary';
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
      $icon = 'checkflag';
      $theme = 'info';
      $url = $this->urlGenerator->generateFromRoute('qs_subscription.user.collection', [
        'community' => $community->id(),
        'user' => $this->currentUser->id(),
      ]);
      $label = $this->t('qs_activity.floating.my_subscriptions');
    }

    // Button - "Add Event" or "Activity Dashboard" or "Contact @name @email".
    if (($node && $node->bundle() == 'activity') || $activity) {
      $act = $node ? $node : $activity;
//      // Button "Add Event".
//      if ($this->acl->hasWriteAccessEvent($act)) {
//        $icon = 'plus';
//        $theme = 'secondary';
//        $url = $this->urlGenerator->generateFromRoute('qs_activity.events.form.add', [
//          'activity' => $act->id(),
//        ]);
//        $label = $this->t('qs_activity.floating.add.event');
//      }

      // Button "Activity Dashboard".
      // if ($this->acl->hasAdminAccessActivity($act)) {
      //   $icon = 'activities';
      //   $theme = 'primary';
      //   $url = $this->urlGenerator->generateFromRoute('qs_activity.activities.dashboard', [
      //     'activity' => $act->id(),
      //   ]);
      //   $label = $this->t('qs_activity.floating.dashboard.activity');
      // }

      // Button "Contact Organizer(s) & Maintainer(s)".
      if (!$this->acl->hasWriteAccessEvent($act) && !$this->acl->hasAdminAccessActivity($act)) {
        $officials_mails = [];

        // Get all organizers's mails of this activity.
        $query_organizers = $this->privilegeManager->queryPrivilege($act, 'activity_organizers');
        $query_organizers->leftJoin('users_field_data', 'users', 'users.uid = privileges.user');
        $query_organizers->fields('users', ['mail']);
        $rows = $query_organizers->execute()->fetchAll();

        foreach ($rows as $row) {
          $officials_mails[$row->user] = $row->mail;
        }

        // Get all maintainers's mails of this activity.
        $query_maintainers = $this->privilegeManager->queryPrivilege($act, 'activity_maintainers');
        $query_maintainers->leftJoin('users_field_data', 'users', 'users.uid = privileges.user');
        $query_maintainers->fields('users', ['mail']);
        $rows = $query_maintainers->execute()->fetchAll();

        foreach ($rows as $row) {
          $officials_mails[$row->user] = $row->mail;
        }

        // Don't show any button if no officials exists.
        if (!empty($officials_mails)) {
          $icon  = 'mail';
          $theme = 'primary';
          $url   = 'mailto:' . implode(',', $officials_mails);
          $label = $this->t('qs_activity.floating.contact.organizers_and_maintainers');
        }
      }

      // if ($route_name == 'qs_activity.activities.form.edit.info') {
      //   $label = $this->t('qs.activity.edit_info');
      // }
      // elseif ($route_name == 'qs_activity.activities.form.edit.visibility') {
      //   $label = $this->t('qs.activity.edit_visibility');
      // }
      // elseif ($route_name == 'qs_activity.activities.form.edit.defaults') {
      //   $label = $this->t('qs.activity.edit_default_values');
      // }
      // elseif ($route_name == 'qs_activity.activities.members') {
      //   $label = $this->t('qs.activity.members');
      // }
//      elseif ($route_name == 'qs_activity.events.form.add') {
//        $label = $this->t('qs.activity.add_event');
//        $theme = 'secondary';
//      }
      // elseif ($route_name == 'qs_activity.activities.form.delete') {
      //   $icon = 'trash';
      //   $label = $this->t('qs.activity.delete');
      //   $theme = 'danger';
      // }
    }

    // Button - "My Photos".
    if ($community && in_array($route_name, [
      'qs_photo.collection.theme',
      'qs_photo.collection.month',
      // 'qs_photo.user.activities.collection',
      'qs_photo.activity',
      'qs_photo.user.form.manage',
//      'qs_photo.form.add',
    ])) {

      // For everybody, show a button "My Photos".
      // $icon = 'picture';
      // $theme = 'primary';
      // $url = $this->urlGenerator->generateFromRoute('qs_photo.user.activities.collection', [
      //   'community' => $community->id(),
      //   'user' => $this->currentUser->id(),
      // ]);
      // $label = $this->t('qs_photo.floating.my_photos');

      // if ($route_name == 'qs_photo.user.form.manage') {
      //   $label = $this->t('qs_photo.floating.manage_photos');
      // }
    }

    // Add Photo.
//    if ($community && in_array($route_name, [
//      'qs_photo.form.add',
//    ])) {
//      // For everybody, show a button "My Photos".
//      $icon = 'plus';
//      $theme = 'secondary';
//      $url = $this->urlGenerator->generateFromRoute('qs_photo.form.add', [
//        'community' => $community->id(),
//      ]);
//      $label = $this->t('qs_photo.form.add.title');
//    }

    // Delete Photos.
    if ($activity && in_array($route_name, [
      'qs_photo.form.delete',
    ])) {
      // For everybody, show a button "My Photos".
      $icon = 'trash';
      $theme = 'danger';
      $url = '#';
      $label = $this->t('qs.photo.delete');
    }

    // // Comment Photos.
    // if ($activity && in_array($route_name, [
    //   'qs_photo.form.comments',
    // ])) {
    //   // For everybody, show a button "My Photos".
    //   $icon = 'pencil';
    //   $theme = 'secondary';
    //   $url = '#';
    //   $label = $this->t('qs_photo.form.comment.title');
    // }

    // Welcome.
    if ($community && in_array($route_name, [
      'qs_community.welcome',
    ])) {
      $icon = 'user';
      $theme = 'invert';
      $url = $this->urlGenerator->generateFromRoute('qs_supervisor.account.dashboard', [
        'user' => $this->currentUser->id(),
      ]);
      $label = $this->t('qs_supervisor.floating.my_account');
    }

    // Display as active on these routes.
    if (in_array($route_name, [
      'qs_activity.activities.form.add',
      // 'qs_activity.user.collection',
      // 'qs_activity.activities.dashboard',
      // 'qs_activity.activities.form.edit.info',
      // 'qs_activity.activities.form.edit.visibility',
      // 'qs_activity.activities.form.edit.defaults',
      // 'qs_activity.activities.members',
//      'qs_activity.events.form.add',
      // 'qs_activity.activities.form.delete',
      // 'qs_community.dashboard',
      // 'qs_community.members',
      // 'qs_community.waiting_approval',
      'qs_photo.user.activities.collection',
      'qs_photo.user.form.manage',
//      'qs_photo.form.add',
      'qs_photo.form.comments',
      'qs_photo.form.delete',
    ])) {
      $classes[] = 'active';
    }

    $variables['buttons'][] = [
      'url' => $url,
      'label' => $label,
      'theme' => $theme,
      'icon' => $icon,
      'classes' => $classes,
    ];

    //
    // Add a second button if needed.
    //
    // Welcome screen second button.
    if ($community && $this->acl->hasAdminAccessCommunity($community) && in_array($route_name, [
      'qs_community.welcome',
    ])) {
      $variables['buttons'][] = [
        'url' => $this->urlGenerator->generate('qs_community.dashboard', ['community' => $community->id()]),
        'label' => $this->t('qs_menu.links.account.communities'),
        'theme' => 'invert',
        'icon' => 'communities-sm',
        'classes' => $classes,
      ];
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
