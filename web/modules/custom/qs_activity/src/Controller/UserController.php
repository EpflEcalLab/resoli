<?php

namespace Drupal\qs_activity\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\qs_badge\Service\BadgeManager;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Dashboard that list activities of one user.
 */
class UserController extends ControllerBase {

  /**
   * The entity QS Activity Manager.
   *
   * @var \Drupal\qs_activity\Service\ActivityManager
   */
  protected $activityManager;

  /**
   * The Badge Manager.
   *
   * @var \Drupal\qs_badge\Service\BadgeManager
   */
  protected $badgeManager;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  private $privilegeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, PrivilegeManager $privilege_manager, ActivityManager $activity_manager, BadgeManager $badge_manager) {
    $this->acl = $acl;
    $this->privilegeManager = $privilege_manager;
    $this->nodeStorage = $this->entityTypeManager()->getStorage('node');
    $this->activityManager = $activity_manager;
    $this->badgeManager = $badge_manager;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   * @param \Drupal\user\UserInterface $user
   *   Run access checks for this user.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountProxyInterface $account, TermInterface $community, UserInterface $user) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessAccountDashboard($user, $account) && $this->acl->hasAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Account activities page.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community.
   * @param \Drupal\user\UserInterface $user
   *   The user.
   *
   * @return array
   *   Render array of account activities.
   */
  public function activities(TermInterface $community, UserInterface $user) {
    $variables['community'] = $community;

    // We are browsing as an account with AccessBypass, add user info to page.
    if ($this->currentUser()->id() !== $user->id()) {
      $variables['user'] = $user;
    }

    $variables['activities'] = $this->activityManager->getByUser($community, $user);

    // Get badges.
    if (!empty($variables['activities'])) {
      // From list of Activities get user privileges.
      $variables['badges']['privileges'] = $this->badgeManager->getPrivileges($variables['activities'], $user);

      // From list of Activities count pending subscriptions by activity.
      $variables['badges']['activities_subscriptions']['pendings_guests'] = $this->badgeManager->countSubscriptionsByActivities($variables['activities'], NULL);
    }

    $variables['community_has_write_access'] = $this->acl->hasWriteAccessCommunity($community);

    return [
      '#theme' => 'qs_activity_user_collection_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
        ],
        'tags' => [
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
          'node_list:activities',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('qs_acl.privilege_manager'),
      $container->get('qs_activity.activity_manager'),
      $container->get('qs_badge.badge_manager')
    );
  }

}
