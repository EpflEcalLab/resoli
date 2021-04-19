<?php

namespace Drupal\qs_auth\Controller;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\user\UserDataInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Landing page for use to choose which communities to go on.
 *
 * This page is shown when the user has more than 1 community to go after login.
 */
class AccountController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The user data service.
   *
   * @var \Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, UserDataInterface $user_data, TimeInterface $time) {
    $this->acl = $acl;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->userData = $user_data;
    $this->time = $time;
  }

  /**
   * Communities page.
   *
   * This page is shown when the user has more than 1 community where it's a
   * certified member.
   *
   * @todo Code the page with link of community, appliance link,
   * status of pending appliance & membership.
   */
  public function communities() {
    $variables['communities'] = $this->acl->getCommunities();
    $variables['pending'] = $this->acl->getPendingApprovalCommunities();

    return [
      '#theme' => 'qs_auth_communities_page',
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
    $current = $this->time->getRequestTime();

    // Basic validation of arguments.
    $account_data = $this->userData->get('user', $user->id());

    if (isset($account_data['cancel_method']) && !empty($timestamp) && !empty($hashed_pass)) {
      // Validate expiration and hashed password/login.
      if ($timestamp <= $current && $current - $timestamp < $timeout && $user->id() && $timestamp >= $user->getLastLoginTime() && hash_equals($hashed_pass, user_pass_rehash($user, $timestamp))) {
        $edit = [
          'user_cancel_notify' => isset($account_data['cancel_notify']) ? $account_data['cancel_notify'] : $this->config('user.settings')->get('notify.status_canceled'),
        ];
        user_cancel($edit, $user->id(), $account_data['cancel_method']);

        return [
          '#theme' => 'qs_auth_cancel_confirm_page',
        ];
      }

      $this->messenger()->addMessage($this->t('qs.cancel.confirm.expired'), MessengerInterface::TYPE_ERROR);

      return $this->redirect('entity.user.cancel_form', ['user' => $user->id()], ['absolute' => TRUE]);
    }

    throw new AccessDeniedHttpException();
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
      $container->get('user.data'),
      $container->get('datetime.time')
    );
  }

  /**
   * Confirmation page when user request a recovery password.
   */
  public function passConfirm() {
    return [
      '#theme' => 'qs_auth_pass_confirm_page',
    ];
  }

}
