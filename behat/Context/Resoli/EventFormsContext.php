<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Drupal\Core\Datetime\DrupalDateTime;
use DateTimeZone;

/**
 * Defines event features from the specific context.
 */
class EventFormsContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Fill the Event Add Form.
   *
   * @param string $community
   *   The community slug of activity to edit.
   * @param string $activity
   *   The activity slug to edit.
   * @param \Behat\Gherkin\Node\TableNode $fields
   *   The fields value to use.
   *
   * @Given I fill the Add Event form :activity of :community with:
   */
  public function fillEventAddForm($community, $activity, TableNode $fields) {
    $this->visitPath("/$community/activities/$activity/events/add");

    foreach ($fields->getHash() as $field) {
      $dt = new DrupalDateTime();
      $dt->setTimezone(new DateTimeZone('UTC'));
      $dt->modify($field['date']);

      $this->getSession()->getPage()->fillField('edit-title', $field['title']);
      $this->getSession()->getPage()->fillField('edit-date', $dt->format('d.m.Y'));
      $this->getSession()->getPage()->fillField('edit-start-at', $field['start-at']);
      $this->getSession()->getPage()->fillField('edit-end-at', $field['end-at']);
      $this->getSession()->getPage()->fillField('edit-body', $field['body']);
      $this->getSession()->getPage()->fillField('edit-venue', $field['venue']);
      $this->getSession()->getPage()->fillField('edit-contact-name', $field['contact-name']);
      $this->getSession()->getPage()->fillField('edit-contact-phone', $field['contact-phone']);
      $this->getSession()->getPage()->fillField('edit-contact-mail', $field['contact-mail']);
      $this->getSession()->getPage()->fillField('edit-contribution', $field['contribution']);
    }
  }

}
