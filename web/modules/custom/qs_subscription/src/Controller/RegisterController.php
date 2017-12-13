<?php

namespace Drupal\qs_subscription\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_subscription\Service\SubscriptionManager;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\NodeInterface;

/**
 * RegisterController.
 */
class RegisterController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  protected $acl;

  /**
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  private $subscriptionManager;

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
   * @param \Drupal\node\NodeInterface $event
   *   Run access checks for this event.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $event) {
    $access = AccessResult::forbidden();

    // Get the related activity.
    $activity = $event->field_activity->entity;

    if ($activity && $this->acl->hasSubscribeAccessEvent($activity)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * Confirm the requested subscription.
   *
   * This AJAX call is called from the waiting-approval dashboard only.
   *
   * @param \Drupal\node\NodeInterface $event
   *   The event.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formatted response. Contains status & confirmed subscription.
   */
  public function request(NodeInterface $event) {
    $requested = $this->subscriptionManager->request($event);
    return new JsonResponse([
      'status'       => TRUE,
      'subscription' => $requested->toArray(),
    ]);
  }

}
