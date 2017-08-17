<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Default accounts with password.
   *
   * It's the collection of accounts activated with the default
   *
   * @var array
   */
  protected $accounts = [
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
  ];

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * @Given I am logged in as user :username
  */
  public function iAmLoggedInAsUser($username) {
    if(!isset($this->accounts[$username])) {
      throw new \Exception(new FormattableMarkup('user "@username" not found.', ['@username' => $username]));
    }

    $account = $this->accounts[$username];

    $this->visitPath('user/login');
    $this->getSession()->getPage()->fillField('name', $account['username']);
    $this->getSession()->getPage()->fillField('pass', $account['pass']);
    $this->getSession()->getPage()->pressButton('edit-submit');
  }

}
