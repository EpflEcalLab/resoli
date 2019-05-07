<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines register features from the specific context.
 */
class RegisterContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Fill the Register Form.
   *
   * @param \Behat\Gherkin\Node\TableNode $fields
   *   The fields value to use.
   *
   * @Given I fill the Register form with:
   */
  public function fillTheRegisterForm(TableNode $fields) {
    $this->visitPath("/authentication/register");

    foreach ($fields->getHash() as $field) {
      $this->getSession()->getPage()->selectFieldOption("edit-community-1", $field['community']);
      $this->getSession()->getPage()->fillField('edit-firstname', $field['firstname']);
      $this->getSession()->getPage()->fillField('edit-lastname', $field['lastname']);
      $this->getSession()->getPage()->fillField('edit-mail', $field['mail']);
      $this->getSession()->getPage()->fillField('edit-phone', $field['phone']);
      $this->getSession()->getPage()->fillField('edit-password', $field['password']);
      $this->getSession()->getPage()->fillField('edit-password-verification', $field['password']);
    }
  }

}
