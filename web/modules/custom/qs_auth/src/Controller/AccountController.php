<?php

namespace Drupal\qs_auth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\user\UserDataInterface;
use Drupal\Component\Utility\Crypt;

/**
 * AccountController.
 */
class AccountController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * The entity query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * The user data service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, QueryFactory $query_factory, UserDataInterface $user_data) {
    $this->acl          = $acl;
    $this->termStorage  = $entity_type_manager->getStorage('taxonomy_term');
    $this->queryFactory = $query_factory;
    $this->userData     = $user_data;
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
    $container->get('entity.query'),
    $container->get('user.data')
    );
  }

  /**
   * Checks access for Approval.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function accessApproval(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();
    if ($community->bundle() == 'communities') {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * Approval page.
   *
   * This page is shown when the user access to a community which he previously
   * applied but which he's not a certified member.
   * He must be reviewed by a Manager of this community.
   */
  public function approval(TermInterface $community) {
    $variables['community'] = $community;

    return [
      '#theme' => 'qs_auth_approval_page',
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

  /**
   * Communities page.
   *
   * This page is shown when the user has more than 1 community where he's a
   * certified member.
   *
   * @TODO: Code the page with link of community, appliance link,
   * status of pending appliance & membership.
   */
  public function communities() {
    $variables['communities'] = $this->acl->getCommunities();
    $variables['pending'] = $this->acl->getPendingApprovalCommunities();

    return [
      '#theme'     => 'qs_auth_communities_page',
      '#variables' => $variables,
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

  /**
   * Confimration page when user request a recovery password.
   */
  public function passConfirm() {
    return [
      '#theme' => 'qs_auth_pass_confirm_page',
    ];
  }

  /**
   * Confirms cancelling a user account via an email link.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user account.
   * @param int $timestamp
   *   The timestamp.
   * @param string $hashed_pass
   *   The hashed password.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response.
   */
  public function confirmCancel(UserInterface $user, $timestamp = 0, $hashed_pass = '') {
    // Time out in seconds until cancel URL expires; 24 hours = 86400 seconds.
    $timeout = 86400;
    $current = REQUEST_TIME;

    // Basic validation of arguments.
    $account_data = $this->userData->get('user', $user->id());
    if (isset($account_data['cancel_method']) && !empty($timestamp) && !empty($hashed_pass)) {
      // Validate expiration and hashed password/login.
      if ($timestamp <= $current && $current - $timestamp < $timeout && $user->id() && $timestamp >= $user->getLastLoginTime() && Crypt::hashEquals($hashed_pass, user_pass_rehash($user, $timestamp))) {
        $edit = [
          'user_cancel_notify' => isset($account_data['cancel_notify']) ? $account_data['cancel_notify'] : $this->config('user.settings')->get('notify.status_canceled'),
        ];
        user_cancel($edit, $user->id(), $account_data['cancel_method']);

        return [
          '#theme' => 'qs_auth_cancel_confirm_page',
        ];
      }
      else {
        drupal_set_message($this->t('qs.cancel.confirm.expired'), 'error');
        return $this->redirect('entity.user.cancel_form', ['user' => $user->id()], ['absolute' => TRUE]);
      }
    }
    throw new AccessDeniedHttpException();
  }

}
