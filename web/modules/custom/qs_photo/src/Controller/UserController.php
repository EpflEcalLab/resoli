<?php

namespace Drupal\qs_photo\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Access\AccessResult;
use Drupal\qs_activity\Service\ActivityManager;
use Drupal\qs_acl\Service\PrivilegeManager;

/**
 * UserController.
 */
class UserController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

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
  private $privilegeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, PrivilegeManager $privilege_manager, ActivityManager $activity_manager) {
    $this->acl              = $acl;
    $this->privilegeManager = $privilege_manager;
    $this->nodeStorage      = $this->entityTypeManager()->getStorage('node');
    $this->activityManager  = $activity_manager;
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
      $container->get('qs_activity.activity_manager')
    );
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
   * Account activities page, which ones the user can upload photo(s).
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
    if ($this->currentUser()->id() != $user->id()) {
      $variables['user'] = $user;
    }

    if ($this->acl->hasBypass()) {
      // Show every activities for bypass user.
      $nids = $this->activityManager->getThemed($community);
      $variables['activities'] = $this->nodeStorage->loadMultiple($nids);
    }
    else {
      // Show only activity where user has upload photo access.
      $variables['activities'] = $this->activityManager->getByUserPhoto($community, $user);
    }

    return [
      '#theme'     => 'qs_photo_user_activities_collection_page',
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

}
