<?php

namespace Drupal\qs_activity\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Dashboard to manage one event.
 *
 * The dashboard list operations the user can operate on the event with
 * its privilege in that activity (member with some privileges).
 */
class EventDashboardController extends ControllerBase {

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl) {
    $this->acl = $acl;
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
    $container->get('qs_acl.access_control')
    );
  }

  /**
   * Dashboard page.
   */
  public function dashboard(NodeInterface $event) {
    return [
      '#theme' => 'qs_activity_event_dashboard_page',
      '#variables' => ['event' => $event],
      '#cache' => [
        'tags' => [
          // Invalidated whenever any Activity is updated, deleted or created.
          'node_list:event',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
