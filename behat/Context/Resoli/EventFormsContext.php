<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Drupal\Core\Datetime\DrupalDateTime;
use DateTimeZone;

/**
 * Defines event features from the specific context.
 *
 * @codingStandardsIgnoreFile
 */
class EventFormsContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Fill the Event Add Form.
   *
   * Example: I fill the Add Event form "accueil-cafe" of "lausanne" with:
   *           | title | date | start-at | end-at | body  | venue | contact-name | contact-phone | contact-mail     | contribution |
   *           | Foo   | now  | 12:00    | 15:00  | Lorem | Bar   | John Doe     | +01 234 56 78 | mail@example.org | 25 CHF       |
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

  /**
   * Fill the Event Edit Form.
   *
   * Example: I fill the Edit Event "accueil-cafe-1" form on activity "accueil-cafe" of "lausanne" with:
   *           | title | date | start-at | end-at | body  | venue | contact-name | contact-phone | contact-mail     | contribution |
   *           | Foo   | now  | 12:00    | 15:00  | Lorem | Bar   | John Doe     | +01 234 56 78 | mail@example.org | 25 CHF       |
   *
   * @param string $community
   *   The community slug of event to edit.
   * @param string $activity
   *   The activity's event slug to edit.
   * @param string $event
   *   The event slug to edit.
   * @param \Behat\Gherkin\Node\TableNode $fields
   *   The fields value to use.
   *
   * @Given I fill the Edit Event :event form on activity :activity of :community with:
   */
  public function fillEventEditForm($community, $activity, $event, TableNode $fields) {
    $this->visitPath("/$community/activities/$activity/events/$event/edit");

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

      $this->getSession()->getPage()->selectFieldOption('has_contribution', '0');
      if (isset($field['contribution'])) {
        $this->getSession()->getPage()->selectFieldOption('has_contribution', '1');
        $this->getSession()->getPage()->fillField('edit-contribution', $field['contribution']);
      }
    }
  }

}
