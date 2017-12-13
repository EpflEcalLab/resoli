<?php

namespace Drupal\qs_activity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * DashboardController.
 */
class DashboardController extends ControllerBase {

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
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $activity
   *   Run access checks for this node.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $activity) {
    $access = AccessResult::forbidden();
    if ($this->acl->hasAdminAccessActivity($activity)) {
      $access = AccessResult::allowed();
    }
    return $access;
  }

  /**
   * Dashboard page.
   */
  public function dashboard(NodeInterface $activity) {
    return [
      '#theme'     => 'qs_activity_dashboard_page',
      '#variables' => ['activity' => $activity],
      '#cache' => [
        'tags' => [
          // Invalidated whenever any Activity is updated, deleted or created.
          'node_list:activity',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
