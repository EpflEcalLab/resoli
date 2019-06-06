<?php

namespace Drupal\qs_test;

use Drupal\Core\Session\AnonymousUserSession;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

/**
 * Provides methods to create and setup users.
 *
 * This trait is meant to be used only by test classes.
 */
trait UserTestTrait {

  /**
   * Setup the default anonymous user.
   */
  public function setupAnonymous() {
    // Create anonymous user role.
    $role = Role::create([
      'id'    => 'anonymous',
      'label' => 'anonymous',
    ]);
    $role->save();

    // Insert the anonymous user into the database.
    User::create([
      'uid'  => 0,
      'name' => '',
    ])->save();

    $this->container->get('current_user')->setAccount(new AnonymousUserSession());
  }

}
