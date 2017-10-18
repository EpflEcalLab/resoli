<?php

namespace Drupal\qs_subscription\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_subscription\Service\SubscriptionManager;
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
  protected $subscriptionManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, SubscriptionManager $subscription_manager) {
    $this->acl                 = $acl;
    $this->subscriptionManager = $subscription_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
    $container->get('qs_acl.access_control'),
    $container->get('qs_subscription.subscription_manager')
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
   * @return bool
   *   Access allowed or rejected.
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
   * @return Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formated response. Contains the status & the confirmed subscription.
   */
  public function confirm(Subscription $subscription) {
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
   * @return Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formated response. Contains the status & the declined subscription.
   */
  public function decline(Subscription $subscription) {
    $declined = $this->subscriptionManager->decline($subscription);
    return new JsonResponse([
      'status'       => TRUE,
      'subscription' => $declined->toArray(),
    ]);
  }

}
