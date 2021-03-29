<?php

namespace Drupal\qs_subscription\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\qs_badge\Service\BadgeManager;
use Drupal\qs_subscription\Service\SubscriptionManager;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Collection of subscription of the current user.
 */
class UserController extends ControllerBase {

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
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  private $subscriptionManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, PrivilegeManager $privilege_manager, SubscriptionManager $subscription_manager, BadgeManager $badge_manager) {
    $this->acl = $acl;
    $this->privilegeManager = $privilege_manager;
    $this->nodeStorage = $this->entityTypeManager()->getStorage('node');
    $this->subscriptionManager = $subscription_manager;
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
   * Account subscriptions page.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community.
   * @param \Drupal\user\UserInterface $user
   *   The user.
   *
   * @return array
   *   Render array of account subscriptions.
   */
  public function subscriptions(TermInterface $community, UserInterface $user) {
    $variables['community'] = $community;

    // We are browsing as an account with AccessBypass, add user info to page.
    if ($this->currentUser()->id() !== $user->id()) {
      $variables['user'] = $user;
    }

    $variables['events'] = $this->subscriptionManager->getByUser($community, $user);

    // Get badges.
    if (!empty($variables['events'])) {
      // From list of Events where current user has pending subscriptions.
      $variables['badges']['subscriptions']['pendings'] = $this->badgeManager->getSubscription($variables['events'], NULL, $user);

      // From list of Events where current user has confirmed subscription.
      $variables['badges']['subscriptions']['confirmed'] = $this->badgeManager->getSubscription($variables['events'], TRUE, $user);

      // From list of Events get user privileges by given events.
      $variables['badges']['privileges'] = $this->badgeManager->getPrivilegesByEvents($variables['events'], $user);

      // From list of Events count pending subscriptions by given events.
      $variables['badges']['subscriptions']['pendings_guests'] = $this->badgeManager->countSubscriptions($variables['events'], NULL);

      // From list of Events count confirmed subscriptions by given events.
      $variables['badges']['subscriptions']['confirmed_guests'] = $this->badgeManager->countSubscriptions($variables['events'], TRUE);
    }

    return [
      '#theme' => 'qs_subscription_user_collection_page',
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
