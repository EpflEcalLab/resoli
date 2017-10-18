<?php

namespace Drupal\qs_subscription\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Access\AccessResult;
use Drupal\qs_subscription\Service\SubscriptionManager;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_badge\Service\BadgeManager;

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
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  private $subscriptionManager;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  private $privilegeManager;

  /**
   * The Badge Manager.
   *
   * @var \Drupal\qs_badge\Service\BadgeManager
   */
  protected $badgeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, PrivilegeManager $privilege_manager, SubscriptionManager $subscription_manager, BadgeManager $badge_manager) {
    $this->acl                 = $acl;
    $this->privilegeManager    = $privilege_manager;
    $this->nodeStorage         = $this->entityTypeManager()->getStorage('node');
    $this->subscriptionManager = $subscription_manager;
    $this->badgeManager        = $badge_manager;
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
      $container->get('qs_subscription.subscription_manager'),
      $container->get('qs_badge.badge_manager')
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
   * @return bool
   *   Access allowed or rejected.
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
   */
  public function subscriptions(TermInterface $community, UserInterface $user) {
    $variables['community'] = $community;

    // We are browsing as an account with AccessBypass, add user info to page.
    if ($this->currentUser()->id() != $user->id()) {
      $variables['user'] = $user;
    }

    $variables['events'] = $this->subscriptionManager->getByUser($community, $user);

    // Get badges.
    if (!empty($variables['events'])) {
      // From list of Events where current user has pending subscriptions.
      $variables['badges']['subscriptions']['pendings'] = $this->badgeManager->getSubscription($variables['events'], NULL, $user);

      // From list of Events where current user has confirmed subscription.
      $variables['badges']['subscriptions']['confirmed'] = $this->badgeManager->getSubscription($variables['events'], 1, $user);
    }

    return [
      '#theme'     => 'qs_subscription_user_collection_page',
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
