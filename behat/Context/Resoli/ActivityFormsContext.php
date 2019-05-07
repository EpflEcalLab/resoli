<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines activity features from the specific context.
 */
class ActivityFormsContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Fill the Activity Add Form.
   *
   * @param string $community
   *   The community name to add an activity.
   * @param \Behat\Gherkin\Node\TableNode $fields
   *   The fields value to use.
   *
   * @Given I fill the Add Activity form of :community with:
   */
  public function fillActivityAddForm($community, TableNode $fields) {
    $this->visitPath("/$community/activities/add");

    foreach ($fields->getHash() as $field) {
      $this->getSession()->getPage()->fillField('edit-title', $field['title']);
      $this->getSession()->getPage()->selectFieldOption('edit-theme-' . $field['theme'], $field['theme']);
    }
  }

}
