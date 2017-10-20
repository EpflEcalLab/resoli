<?php

namespace Drupal\qs_auth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\TermInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * ApprovalController.
 */
class ApprovalController extends ControllerBase {

  /**
   * Checks access for Approval.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, TermInterface $community) {
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
}
