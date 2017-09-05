<?php

namespace Drupal\qs_acl\Controller;

use Drupal\qs_acl\Entity\Privilege;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * PrivilegeJudgeController.
 *
 * This following AJAX call are made from the waiting-approval dashboard only.
 */
class PrivilegeJudgeController extends AjaxControllerBase {

  /**
   * Confirm the requested privilege.
   *
   * This AJAX call is called from the waiting-approval dashboard only.
   *
   * @param \Drupal\qs_acl\Entity\Privilege $privilege
   *   The Privilege to confirm.
   *
   * @return Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formated response. Contains the status & the confirmed privilege.
   */
  public function confirm(Privilege $privilege) {
    $confirmed = $this->privilegeManger->confirm($privilege);
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
   * @param \Drupal\qs_acl\Entity\Privilege $privilege
   *   The Privilege to decline.
   *
   * @return Symfony\Component\HttpFoundation\JsonResponse
   *   JSON formated response. Contains the status & the declined privilege.
   */
  public function decline(Privilege $privilege) {
    $declined = $this->privilegeManger->decline($privilege);
    return new JsonResponse([
      'status'    => TRUE,
      'privilege' => $declined->toArray(),
    ]);
  }

}
