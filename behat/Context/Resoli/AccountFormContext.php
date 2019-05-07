<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines account form features from the specific context.
 */
class AccountFormContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Fill the Account Form.
   *
   * @param \Behat\Gherkin\Node\TableNode $fields
   *   The fields value to use.
   *
   * @Given I fill the Account form with:
   */
  public function fillTheAccountForm(TableNode $fields) {
    foreach ($fields->getHash() as $field) {
      $this->visitPath('/account/' . $field['user'] . '/edit');
      $this->getSession()->getPage()->fillField('edit-firstname', $field['firstname']);
      $this->getSession()->getPage()->fillField('edit-lastname', $field['lastname']);
      $this->getSession()->getPage()->fillField('edit-phone', $field['phone']);
    }
  }

}
