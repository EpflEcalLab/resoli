<?php

namespace Drupal\qs_supervisor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\qs_auth\Service\Account;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\user\UserInterface;

/**
 * AccountController.
 */
class AccountController extends ControllerBase {
  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * The Quartiers-Solidaires account service.
   *
   * @var \Drupal\qs_auth\Service\Account
   */
  private $account;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, Account $account) {
    $this->acl         = $acl;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->account     = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('entity_type.manager'),
      $container->get('qs_auth.account')
    );
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   Run access checks for this account.
   * @param \Drupal\user\UserInterface $user
   *   Run access checks for this user.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountProxyInterface $account, UserInterface $user) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessAccountDashboard($user, $account)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Account dashboard page.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user's dashboard.
   */
  public function dashboard(UserInterface $user) {
    return [
      '#theme'     => 'qs_supervisor_account_dashboard_page',
      '#variables' => ['user' => $user],
      '#cache' => [
        'contexts' => [
          'user',
        ],
        'tags' => [
          // Invalidated whenever any Community is updated, deleted or created.
          'taxonomy_term_list:communities',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
