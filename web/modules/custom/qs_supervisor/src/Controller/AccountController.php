<?php

namespace Drupal\qs_supervisor\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_badge\Service\BadgeManager;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A user dashboard listing its communities and the user data.
 */
class AccountController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_badge\Service\BadgeManager
   */
  private $badgeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, BadgeManager $badgeManager) {
    $this->acl = $acl;
    $this->badgeManager = $badgeManager;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   Run access checks for this account.
   * @param \Drupal\user\UserInterface $user
   *   Run access checks for this user.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountProxyInterface $account, UserInterface $user) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessAccountDashboard($user, $account)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('qs_badge.badge_manager')
    );
  }

  /**
   * Account dashboard page.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user's dashboard.
   *
   * @return array
   *   Render array of account dashboard.
   */
  public function dashboard(UserInterface $user) {
    $variables['user'] = $user;
    $variables['communities'] = $this->acl->getCommunities($user);
    $variables['pending'] = $this->acl->getPendingApprovalCommunities($user);
    $variables['privileges'] = [];

    if ($variables['communities']) {
      $variables['privileges'] = $this->badgeManager->getCommunityPrivileges($variables['communities'], $user);
    }

    return [
      '#theme' => 'qs_supervisor_account_dashboard_page',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
        ],
        'tags' => [
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
