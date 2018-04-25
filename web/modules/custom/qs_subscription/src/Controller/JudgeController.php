<?php

namespace Drupal\qs_subscription\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_subscription\Service\SubscriptionManager;
use Drupal\qs_acl\Service\PrivilegeManager;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\qs_subscription\Entity\Subscription;

/**
 * JudgeController.
 */
class JudgeController extends ControllerBase {
  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * The Privilege Manager.
   *
   * @var \Drupal\qs_acl\Service\PrivilegeManager
   */
  protected $privilegeManager;

  /**
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  protected $subscriptionManager;

  /**
   * Composes and optionally sends an email message.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mail;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccessControl $acl, PrivilegeManager $privilege_manager, SubscriptionManager $subscription_manager, MailManagerInterface $mail) {
    $this->acl                 = $acl;
    $this->privilegeManager    = $privilege_manager;
    $this->subscriptionManager = $subscription_manager;
    $this->mail                = $mail;
    $this->userStorage         = $entity_type_manager->getStorage('user');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('entity_type.manager'),
    $container->get('qs_acl.access_control'),
    $container->get('qs_acl.privilege_manager'),
    $container->get('qs_subscription.subscription_manager'),
    $container->get('plugin.manager.mail')
    );
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\qs_subscription\Entity\Subscription $subscription
   *   Run access checks for this $subscription.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, Subscription $subscription) {
    $access = AccessResult::forbidden();

    $event = $subscription->getEntity();
    if ($event->bundle() != 'event') {
      return $access;
    }

    // Get the related activity.
    $activity = $event->field_activity->entity;
    if ($activity && $this->acl->hasWriteAccessEvent($activity)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Confirm the requested subscription.
   *
   * This AJAX call is called from the waiting-approval dashboard only.
   *
   * @param \Drupal\qs_subscription\Entity\Subscription $subscription
   *   The subscription.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formatted response. Contains status & confirmed subscription.
   */
  public function confirm(Subscription $subscription) {
    $entity = $subscription->getEntity();
    $user = $subscription->getOwner();

    // Send email to user when event subscription is approved.
    if ($entity && $entity->bundle() == 'event' && $user && $user->entity) {
      $this->mail->mail('qs_subscription', 'subscription_event_waiting_approval_confirm', $user->entity->getEmail(), $user->entity->getPreferredLangcode(), [
        'account' => $user->entity,
        'event'   => $entity,
      ]);

      $activity = $entity->field_activity->entity;

      // Get all organizers of this activities's event.
      $query_organizers = $this->privilegeManager->queryPrivilege($activity, 'activity_organizers');
      $rows = $query_organizers->execute()->fetchAll();

      foreach ($rows as $row) {
        $ids[$row->user] = $row->user;
      }

      // Get all maintainer of this activities's event.
      $query_maintainers = $this->privilegeManager->queryPrivilege($activity, 'activity_maintainers');
      $rows = $query_maintainers->execute()->fetchAll();

      foreach ($rows as $row) {
        $ids[$row->user] = $row->user;
      }

      // Load user with activity_organizers or activity_maintainers privilege(s)
      // & send them mail.
      $accounts = NULL;
      if ($ids) {
        $accounts = $this->userStorage->loadMultiple($ids);

        foreach ($accounts as $account) {
          $this->mail->mail('qs_subscription', 'subscription_event_waiting_approval_confirm_organizers', $account->getEmail(), $account->getPreferredLangcode(), [
            'user'  => $user->entity,
            'event' => $entity,
          ]);
        }
      }
    }

    $confirmed = $this->subscriptionManager->confirm($subscription);
    return new JsonResponse([
      'status'       => TRUE,
      'subscription' => $confirmed->toArray(),
    ]);
  }

  /**
   * Decline the requested subscription.
   *
   * This AJAX call is called from the waiting-approval dashboard only.
   *
   * @param \Drupal\qs_subscription\Entity\Subscription $subscription
   *   The subscription.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formatted response. Contains the status & the declined subscription.
   */
  public function decline(Subscription $subscription) {
    $entity = $subscription->getEntity();
    $user = $subscription->getOwner();

    // Send email to user when event subscription is approved.
    if ($entity && $entity->bundle() == 'event' && $user && $user->entity) {
      $this->mail->mail('qs_subscription', 'subscription_event_waiting_approval_decline', $user->entity->getEmail(), $user->entity->getPreferredLangcode(), [
        'account' => $user->entity,
        'event'   => $entity,
      ]);
    }

    $declined = $this->subscriptionManager->decline($subscription);
    return new JsonResponse([
      'status'       => TRUE,
      'subscription' => $declined->toArray(),
    ]);
  }

}
