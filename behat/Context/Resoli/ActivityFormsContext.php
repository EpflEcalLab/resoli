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

}
