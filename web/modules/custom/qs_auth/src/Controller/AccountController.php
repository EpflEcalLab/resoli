<?php

namespace Drupal\qs_auth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\TermInterface;

/**
 * AccountController.
 */
class AccountController extends ControllerBase {

  /**
   * Approval page.
   *
   * This page is shown when the user access to a community which he previously
   * applied but which he's not a certified member.
   * He must be reviewed by a Manager of this community.
   *
   * @TODO: Code the page with link of community, appliance link,
   * status of pending appliance & membership.
   */
  public function approval(TermInterface $community) {
    dump($community);
    return ['#markup' => 'approval'];
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
