<?php

namespace Drupal\Behat\Context\Resoli;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines activity features from the specific context.
 *
 * @codingStandardsIgnoreFile
 */
class ActivityFormsContext extends RawDrupalContext {

  /**
   * Fill the Activity Add Form.
   *
   * Example: I fill the Add Activity form of "Lausanne" with:
   *           | title    | theme |
   *           | Art Fair | 4     |
   *
   * @param string $community
   *   The community slug to add an activity.
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

  /**
   * Fill the Activity General Information Form.
   *
   * Example: I fill the Activity Information form "accueil-cafe" of "lausanne" with:
   *           | title       | theme |
   *           | Lorem Ipsum | 4     |
   *
   * @param string $community
   *   The community slug of activity to edit.
   * @param string $activity
   *   The activity slug to edit.
   * @param \Behat\Gherkin\Node\TableNode $fields
   *   The fields value to use.
   *
   * @Given I fill the Activity Information form :activity of :community with:
   */
  public function fillActivityInfoForm($community, $activity, TableNode $fields) {
    $this->visitPath("/$community/activities/$activity/edit/info");

    foreach ($fields->getHash() as $field) {
      $this->getSession()->getPage()->fillField('edit-title', $field['title']);
      $this->getSession()->getPage()->selectFieldOption('edit-theme-' . $field['theme'], $field['theme']);
    }
  }

  /**
   * Fill the Activity Visibility Form.
   *
   * Example: I fill the Activity Visibility form "accueil-cafe" of "lausanne" with:
   *           | community-can-subscribe | community-access-contact | community-access-detail | community-access-story | member-create-story | community-access-gallery | member-create-gallery |
   *           | 1 | 1 | 1 | 0 | 1 | 0 | 1 |
   *
   * @param string $community
   *   The community slug of activity to edit.
   * @param string $activity
   *   The activity slug to edit.
   * @param \Behat\Gherkin\Node\TableNode $fields
   *   The fields value to use.
   *
   * @Given I fill the Activity Visibility form :activity of :community with:
   */
  public function fillActivityVisibilityForm($community, $activity, TableNode $fields) {
    $this->visitPath("/$community/activities/$activity/edit/visibility");

    foreach ($fields->getHash() as $field) {
      if ($field['community-can-subscribe']) {
        $this->getSession()->getPage()->checkField('edit-community-can-subscribe');
      }
      else {
        $this->getSession()->getPage()->uncheckField('edit-community-can-subscribe');
      }

      if ($field['community-access-contact']) {
        $this->getSession()->getPage()->checkField('edit-community-access-contact');
      }
      else {
        $this->getSession()->getPage()->uncheckField('edit-community-access-contact');
      }

      if ($field['community-access-detail']) {
        $this->getSession()->getPage()->checkField('edit-community-access-detail');
      }
      else {
        $this->getSession()->getPage()->uncheckField('edit-community-access-detail');
      }

      if ($field['community-access-story']) {
        $this->getSession()->getPage()->checkField('edit-community-access-story');
      }
      else {
        $this->getSession()->getPage()->uncheckField('edit-community-access-story');
      }

      if ($field['member-create-story']) {
        $this->getSession()->getPage()->checkField('edit-member-create-story');
      }
      else {
        $this->getSession()->getPage()->uncheckField('edit-member-create-story');
      }

      if ($field['community-access-gallery']) {
        $this->getSession()->getPage()->checkField('edit-community-access-gallery');
      }
      else {
        $this->getSession()->getPage()->uncheckField('edit-community-access-gallery');
      }

      if ($field['member-create-gallery']) {
        $this->getSession()->getPage()->checkField('edit-member-create-gallery');
      }
      else {
        $this->getSession()->getPage()->uncheckField('edit-member-create-gallery');
      }
    }
  }

  /**
   * Fill the Activity Defaults Values Form.
   *
   * Example: I fill the Activity Defaults Values form "accueil-cafe" of "lausanne" with:
   *           | title | body  | venue | contribution | contact-name | contact-phone | contact-mail     |
   *           | Foo   | Lorem | Bar   | 20 CHF       | John Doe     | +01 234 56 78 | mail@example.org |
   *
   * @param string $community
   *   The community slug of activity to edit.
   * @param string $activity
   *   The activity slug to edit.
   * @param \Behat\Gherkin\Node\TableNode $fields
   *   The fields value to use.
   *
   * @Given I fill the Activity Defaults Values form :activity of :community with:
   */
  public function fillActivityDefaultsForm($community, $activity, TableNode $fields) {
    $this->visitPath("/$community/activities/$activity/edit/defaults");

    foreach ($fields->getHash() as $field) {
      $this->getSession()->getPage()->fillField('edit-title', $field['title']);
      $this->getSession()->getPage()->fillField('edit-body', $field['body']);
      $this->getSession()->getPage()->fillField('edit-venue', $field['venue']);
      $this->getSession()->getPage()->fillField('edit-contribution', $field['contribution']);
      $this->getSession()->getPage()->fillField('edit-contact-name', $field['contact-name']);
      $this->getSession()->getPage()->fillField('edit-contact-phone', $field['contact-phone']);
      $this->getSession()->getPage()->fillField('edit-contact-mail', $field['contact-mail']);
    }
  }

}
