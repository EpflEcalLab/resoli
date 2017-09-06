<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Drupal\Component\Render\FormattableMarkup;
use Behat\Mink\Exception\ElementNotFoundException;

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

  /**
   * @Given I should see :label link with href :href
  */
  public function iShouldSeeLinkWithHref($label, $href) {
    $link = $this->getSession()->getPage()->findLink($label);

    if (null === $link) {
      throw new ElementNotFoundException($this->getSession(), 'link', 'id|title|alt|text', $label);
    }

    if (strpos($link->getAttribute('href'), $href) === false) {
      throw new ElementNotFoundException($this->getSession(), 'link', 'href', $href);
    }
  }
}
