<?php

namespace Drupal\qs_subscription\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_subscription\Service\SubscriptionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Collection of pending subscriptions for an event.
 */
class WaitingApprovalController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  private $configuration = ['limit' => 50];

  /**
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  private $subscriptionManager;

  /**
   * The Subscription Storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  private $subscriptionStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, SubscriptionManager $subscription_manager) {
    $this->acl = $acl;
    $this->subscriptionManager = $subscription_manager;
    $this->subscriptionStorage = $this->entityTypeManager()->getStorage('subscription');
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $event
   *   Run access checks for this node.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $event) {
    $access = AccessResult::forbidden();

    // Get the related activity.
    $activity = $event->field_activity->entity;

    if ($activity && $this->acl->hasWriteAccessEvent($activity)) {
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
    $container->get('qs_subscription.subscription_manager')
    );
  }

  /**
   * Collection page of Accounts waiting for Approval.
   */
  public function waitingApproval(NodeInterface $event) {
    $variables['event'] = $event;
    $variables['activity'] = $event->field_activity->entity;

    $query = $this->subscriptionManager->queryWaitingApproval($event);
    $rows = $query->execute()->fetchAll();

    // Get all members to mailto before pagination.
    $mailto = [];

    foreach ($rows as $row) {
      $mailto[$row->user] = $row->mail;
    }
    $variables['mailto'] = $mailto;

    pager_default_initialize(\count($rows), $this->configuration['limit']);
    $variables['pager'] = [
      '#type' => 'pager',
      '#quantity' => '3',
    ];
    $page = pager_find_page();
    $query->range($page * $this->configuration['limit'], $this->configuration['limit']);

    $rows = $query->execute()->fetchAll();

    $ids = [];

    foreach ($rows as $row) {
      $ids[] = $row->id;
    }

    // Load subscriptions entities.
    $subscriptions = $this->subscriptionStorage->loadMultiple($ids);
    $variables['subscriptions'] = $subscriptions;

    return [
      '#theme' => 'qs_subscription_waiting_approval_page',
      '#variables' => $variables,
      '#cache' => [
        'tags' => [
          // Invalidated whenever any community is updated, deleted or created.
          'user_list:user',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'subscription_list:subscription',
        ],
      ],
    ];
  }

}
