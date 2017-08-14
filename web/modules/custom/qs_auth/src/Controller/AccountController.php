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

}
