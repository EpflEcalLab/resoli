Feature: Activity Information Form

  @api
  Scenario: When reaching the Information form of Activity, the fields should be prefilled with entity values
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe/edit/info"
    Then I should see 20 "#qs-activity-edit-info-form input" elements
    Then I should see 15 "#edit-theme input" elements
    And the "edit-title" field should contain "Accueil Café"
    Then the "edit-theme-5" checkbox should be checked

  @api @preserveDatabase
  Scenario: When editting the Information form of Activity, once saved I should be redirected on the Activity dashboard
    Given I am logged in as user "organizer+lausanne"
    When I fill the Activity Information form "accueil-cafe" of "lausanne" with:
      | title | theme |
      | Accueil Café (edited) | 4 |
    And I press "edit-submit"
    Then the url should match "/lausanne/activities/accueil-cafe/dashboard"

  @api @preserveDatabase
  Scenario: When editting the Information form of Activity, no mail should be sent
    Given I am logged in as user "organizer+lausanne"
    When I fill the Activity Information form "accueil-cafe" of "lausanne" with:
      | title | theme |
      | Accueil Café (edited) | 4 |
    And I press "edit-submit"
    And 0 mails should be sent

  @api @preserveDatabase
  Scenario: When editting the Information form of Activity, the values should be alterd & stored in the database
    Given I am logged in as user "organizer+lausanne"
    When I fill the Activity Information form "accueil-cafe" of "lausanne" with:
      | title | theme |
      | Accueil Café (edited) | 4 |
    And I press "edit-submit"
    When I am on "/lausanne/activities/accueil-cafe/edit/info"
    And the "edit-title" field should contain "Accueil Café (edited)"
    Then the "edit-theme-4" checkbox should be checked
    Then the "edit-theme-5" checkbox should not be checked
