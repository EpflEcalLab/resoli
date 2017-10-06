<?php

namespace Drupal\qs_subscription\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_subscription\Service\SubscriptionManager;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;

/**
 * SubscribersController.
 */
class SubscribersController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  private $configuration = ['limit' => 50];

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The Subscription Manager.
   *
   * @var \Drupal\qs_subscription\Service\SubscriptionManager
   */
  private $subscriptionManager;

  /**
   * The user Storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, SubscriptionManager $subscription_manager) {
    $this->acl                 = $acl;
    $this->subscriptionManager = $subscription_manager;
    $this->userStorage         = $this->entityTypeManager()->getStorage('user');
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
   *   Run access checks for this node.
   *
   * @return bool
   *   Access allowed or rejected.
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
   * Subscribers page.
   */
  public function subscribers(NodeInterface $event) {
    $variables['event'] = $event;
    $variables['activity'] = $event->field_activity->entity;

    $query = $this->subscriptionManager->querySubscribers($event);
    $ids = $query->execute()->fetchAll();
    pager_default_initialize(count($ids), $this->configuration['limit']);
    $variables['pager'] = [
      '#type'     => 'pager',
      '#quantity' => '3',
    ];
    $page = pager_find_page();
    $query->range($page, $this->configuration['limit']);
    $rows = $query->execute()->fetchAll();

    $uids = [];
    foreach ($rows as $row) {
      $uids[$row->user] = $row->user;
    }

    // Load user entities.
    $subscribers = [];
    if ($uids) {
      $subscribers = $this->userStorage->loadMultiple($uids);
    }
    $variables['subscribers'] = $subscribers;

    return [
      '#theme'     => 'qs_subscription_subscribers_page',
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
