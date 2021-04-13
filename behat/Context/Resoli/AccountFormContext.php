<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines account form features from the specific context.
 *
 * @codingStandardsIgnoreFile
 */
class AccountFormContext extends RawDrupalContext {

  /**
   * Fill the Account Form.
   *
   * Example: I fill the Account form with
   *           | user | mail             | firstname | lastname | phone         |
   *           | 1    | mail@example.org | John      | Doe      | +01 234 56 78 |
   *
   * @param \Behat\Gherkin\Node\TableNode $fields
   *   The fields value to use.
   *
   * @Given I fill the Account form with:
   */
  public function fillTheAccountForm(TableNode $fields) {
    foreach ($fields->getHash() as $field) {
      $this->visitPath('/account/' . $field['user'] . '/edit');
      $this->getSession()->getPage()->fillField('edit-mail', $field['mail']);
      $this->getSession()->getPage()->fillField('edit-firstname', $field['firstname']);
      $this->getSession()->getPage()->fillField('edit-lastname', $field['lastname']);
      $this->getSession()->getPage()->fillField('edit-phone', $field['phone']);
    }
  }

}
