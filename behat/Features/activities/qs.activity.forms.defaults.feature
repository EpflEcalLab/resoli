Feature: Activity Defaults Form

  @api
  Scenario: When reaching the Defaults Values form of Activity, the fields should be prefilled with entity values
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/edit/defaults"
    Then I should see 12 "#qs-activity-edit-defaults-form input" elements
    And the "edit-title" field should contain ""
    And the "edit-body" field should contain ""
    And the "edit-venue" field should contain ""
    And the "edit-contribution" field should contain ""
    And the "edit-contact-name" field should contain ""
    And the "edit-contact-phone" field should contain ""
    And the "edit-contact-mail" field should contain ""

  @api @preserveDatabase
  Scenario: When editting the Defaults Values form of Activity, once saved I should be redirected on the Activity dashboard
    Given I am logged in as user "organizer+lausanne"
    When I fill the Activity Defaults Values form "accueil-cafe" of "lausanne" with:
      | title | body | venue | contribution | contact-name | contact-phone | contact-mail |
      | Accueil Café (defaults) | Body (defaults) | Venue (defaults) | 20 CHF | John | +01 234 56 78 | john.doe@example.org |
    And I press "edit-submit"
    Then the url should match "/lausanne/activities/accueil-cafe/dashboard"
    And I should see "qs_activity.activities.form.edit.defaults.success Accueil Café" in the ".alert" element

  @api @preserveDatabase
  Scenario: When editting the Defaults Values form of Activity, no mail should be sent
    Given I am logged in as user "organizer+lausanne"
    When I fill the Activity Defaults Values form "accueil-cafe" of "lausanne" with:
      | title | body | venue | contribution | contact-name | contact-phone | contact-mail |
      | Accueil Café (defaults) | Body (defaults) | Venue (defaults) | 20 CHF | John | +01 234 56 78 | john.doe@example.org |
    And I press "edit-submit"
    And 0 mail should be sent

  @api @preserveDatabase
  Scenario: When editting the Defaults Values form of Activity, the values should be alterd & stored in the database
    Given I am logged in as user "organizer+lausanne"
    When I fill the Activity Defaults Values form "accueil-cafe" of "lausanne" with:
      | title | body | venue | contribution | contact-name | contact-phone | contact-mail |
      | Accueil Café (defaults) | Body (defaults) | Venue (defaults) | 20 CHF | John | +01 234 56 78 | john.doe@example.org |
    And I press "edit-submit"
    When I am on "/lausanne/activities/accueil-cafe/edit/defaults"
    And the "edit-title" field should contain "Accueil Café (defaults)"
    And the "edit-body" field should contain "Body (defaults)"
    And the "edit-venue" field should contain "Venue (defaults)"
    And the "edit-contribution" field should contain "20 CHF"
    And the "edit-contact-name" field should contain "John"
    And the "edit-contact-phone" field should contain "+01 234 56 78"
    And the "edit-contact-mail" field should contain "john.doe@example.org"
