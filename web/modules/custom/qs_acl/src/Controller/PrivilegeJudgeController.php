<?php

namespace Drupal\qs_acl\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * PrivilegeJudgeController.
 *
 * This following AJAX call are made from the waiting-approval dashboard only.
 */
class PrivilegeJudgeController extends AjaxControllerBase {

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return bool
   *   Access allowed or rejected.
   */
  public function access(AccountInterface $account) {
    $access = AccessResult::forbidden();
    $request = $this->requestStack->getCurrentRequest();

    $privilege_id = $request->request->get('privilege');
    $privilege = $this->privilegeStorage->load($privilege_id);

    if (!$privilege) {
      return AccessResult::forbidden();
    }

    $community = $privilege->getEntity();
    if ($community->bundle() == 'communities' && $this->acl->hasAdminAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Confirm the requested privilege.
   *
   * This AJAX call is called from the waiting-approval dashboard only.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formated response. Contains the status & the confirmed privilege.
   */
  public function confirm(Request $request) {
    $privilege_id = $request->request->get('privilege');
    $privilege = $this->privilegeStorage->load($privilege_id);

    $confirmed = $this->privilegeManager->confirm($privilege);
    return new JsonResponse([
      'status'    => TRUE,
      'privilege' => $confirmed->toArray(),
    ]);
  }

  /**
   * Decline the requested privilege.
   *
   * This AJAX call is called from the waiting-approval dashboard only.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formated response. Contains the status & the declined privilege.
   */
  public function decline(Request $request) {
    $privilege_id = $request->request->get('privilege');
    $privilege = $this->privilegeStorage->load($privilege_id);

    $declined = $this->privilegeManager->decline($privilege);
    return new JsonResponse([
      'status'    => TRUE,
      'privilege' => $declined->toArray(),
    ]);
  }

}
