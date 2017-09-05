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
   * Agree the requested privilege.
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
   * Disagree the requested privilege.
   *
   * This AJAX call is called from the waiting-approval dashboard only.
   */
  public function decline(Privilege $privilege) {

  }

}
