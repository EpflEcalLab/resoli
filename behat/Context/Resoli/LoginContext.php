<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;

/**
 * Defines Login application features from the specific context.
 */
class LoginContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Default accounts with password.
   *
   * It's the collection of accounts activated with the default.
   *
   * @var array
   */
  protected $accounts = [
    'admin' => [
      'username' => 'admin',
      'pass'     => 'admin',
    ],
    'nobody' => [
      'username' => 'nobody@antistatique.net',
      'pass'     => 'nobody',
    ],
    'member+lausanne' => [
      'username' => 'member+lausanne@antistatique.net',
      'pass'     => 'member+lausanne',
    ],
    'manager+lausanne' => [
      'username' => 'manager+lausanne@antistatique.net',
      'pass'     => 'manager+lausanne',
    ],
    'organizer+lausanne' => [
      'username' => 'organizer+lausanne@antistatique.net',
      'pass'     => 'organizer+lausanne',
    ],
    'member+fribourg' => [
      'username' => 'member+fribourg@antistatique.net',
      'pass'     => 'member+fribourg',
    ],
    'member2+fribourg' => [
      'username' => 'member2+fribourg@antistatique.net',
      'pass'     => 'member2+fribourg',
    ],
    'member+lausanne+organizer+fribourg' => [
      'username' => 'member+lausanne+organizer+fribourg@antistatique.net',
      'pass'     => 'member+lausanne+organizer+fribourg',
    ],
    'member+fribourg+member+lausanne' => [
      'username' => 'member+fribourg+member+lausanne@antistatique.net',
      'pass'     => 'member+fribourg+member+lausanne',
    ],
    'approval+lausanne' => [
      'username' => 'approval+lausanne@antistatique.net',
      'pass'     => 'approval+lausanne',
    ],
    'approval+fribourg+approval+lausanne' => [
      'username' => 'approval+fribourg+approval+lausanne@antistatique.net',
      'pass'     => 'approval+fribourg+approval+lausanne',
    ],
    'member+fribourg+approval+lausanne' => [
      'username' => 'member+fribourg+approval+lausanne@antistatique.net',
      'pass'     => 'member+fribourg+approval+lausanne',
    ],
    'member+fribourg+approval+lausanne+member+geneve' => [
      'username' => 'member+fribourg+approval+lausanne+member+geneve@as.net',
      'pass'     => 'member+fribourg+approval+lausanne+member+geneve',
    ],
    'member+lausanne+manager+fribourg' => [
      'username' => 'member+lausanne+manager+fribourg@antistatique.net',
      'pass'     => 'member+lausanne+manager+fribourg',
    ],
    'member+fribourg+approval+organizer+fribourg' => [
      'username' => 'member+fribourg+approval+organizer+fribourg@antistatique.net',
      'pass'     => 'member+fribourg+approval+organizer+fribourg',
    ],
    'approval+member+fribourg+approval+organizer+fribourg' => [
      'username' => 'approval+member+fribourg+approval+organizer+fribourg@as.net',
      'pass'     => 'approval+member+fribourg+approval+organizer+fribourg',
    ],
    'member+fribourg+organizer+fribourg' => [
      'username' => 'member+fribourg+organizer+fribourg@antistatique.net',
      'pass'     => 'member+fribourg+organizer+fribourg',
    ],
    'declined+organizer+lausanne' => [
      'username' => 'declined+organizer+lausanne@antistatique.net',
      'pass'     => 'declined+organizer+lausanne',
    ],
    'member+lausanne+declined+organizer+lausanne' => [
      'username' => 'member+lausanne+declined+organizer+lausanne@antistatique.net',
      'pass'     => 'member+lausanne+declined+organizer+lausanne',
    ],
    'member+fribourg+declined+member+lausanne' => [
      'username' => 'member+fribourg+declined+member+lausanne@antistatique.net',
      'pass'     => 'member+fribourg+declined+member+lausanne',
    ],
    'approval+all' => [
      'username' => 'approval+all@antistatique.net',
      'pass'     => 'approval+all',
    ],
    'declined+all' => [
      'username' => 'declined+all@antistatique.net',
      'pass'     => 'declined+all',
    ],
    'approved+all' => [
      'username' => 'approved+all@antistatique.net',
      'pass'     => 'approved+all',
    ],
  ];

  /**
   * Try to login the given account.
   *
   * @Given I am logged in as user :username
   *
   * @throws Exception
   */
  public function iAmLoggedInAsUser($username) {
    if (!isset($this->accounts[$username])) {
      throw new \Exception(sprintf('user "%s" not found.', $username));
    }

    $account = $this->accounts[$username];

    $this->visitPath('user/login');
    $this->getSession()->getPage()->fillField('name', $account['username']);
    $this->getSession()->getPage()->fillField('pass', $account['pass']);
    $this->getSession()->getPage()->pressButton('edit-submit');
  }

}
