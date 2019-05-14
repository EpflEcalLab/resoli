Feature: Add Activity Form

  @api @preserveDatabase
  Scenario: Creating a new Activity into Lausanne and using the "Save" action should redirect me on the Activity page
    Given I am logged in as user "organizer+lausanne"
    And I fill the Add Activity form of "Lausanne" with:
      | title | theme |
      | Art Fair | 4 |
    And I press "edit-save"
    Then the url should match "/fr/lausanne/activities/art-fair"
    And I should see "qs_activity.activities.form.add.success Art Fair" in the ".alert" element

  @api @preserveDatabase
  Scenario: Creating a new Activity into Lausanne and using the "Save & Edit Defaults values" action should redirect me on the Activity Default Values form
    Given I am logged in as user "organizer+lausanne"
    And I fill the Add Activity form of "Lausanne" with:
      | title | theme |
      | Art Fair | 4 |
    And I press "edit-save-and-set-default-values"
    Then the url should match "/fr/lausanne/activities/art-fair/edit/defaults"
    And I should see "qs_activity.activities.form.add.success Art Fair" in the ".alert" element
    And the "edit-title" field should contain ""
    And the "edit-body" field should contain ""
    And the "edit-venue" field should contain ""
    And the "edit-contribution" field should contain ""
    And the "edit-contact-name" field should contain "Gerard Mensoif"
    And the "edit-contact-phone" field should contain ""
    And the "edit-contact-mail" field should contain "organizer+lausanne@antistatique.net"

  @api @preserveDatabase
  Scenario: Creating a new Activity into Lausanne and using the "Save & Add an event" action should redirect me on the Add Event form for this activity
    Given I am logged in as user "organizer+lausanne"
    And I fill the Add Activity form of "Lausanne" with:
      | title | theme |
      | Art Fair | 4 |
    And I press "edit-save-and-new-event"
    Then the url should match "/fr/lausanne/activities/art-fair/events/add"
    And I should see "qs_activity.activities.form.add.success Art Fair" in the ".alert" element
