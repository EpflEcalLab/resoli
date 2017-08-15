<?php

namespace Drupal\qs_auth\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * AccountController.
 */
class AccountController extends ControllerBase {

  /**
   * Account page.
   */
  public function approval() {
    $variables = [];

    return [
      '#theme'     => 'egj_auth_approval_page',
      '#variables' => $variables,
    ];
  }

  /**
   * Communities page.
   *
   * This page is shown when the user has more than 1 community where he's a
   * certified member.
   *
   * @TODO: Code the page with link of community, appliance link,
   * status of pending appliance & membership.
   */
  public function communities() {
    return ['#markup' => 'communities'];
  }

}
