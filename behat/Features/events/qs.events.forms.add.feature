Feature: Add Event Form

  @api @preserveDatabase
  Scenario: When creating an Event, on save we should be redirected on the Activity's events page
    Given I am logged in as user "organizer+lausanne"
    When I fill the Add Event form "accueil-cafe" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-submit"
    Then the url should match "/fr/lausanne/activities/accueil-cafe"

  @api @preserveDatabase
  Scenario: When creating an Event, no mail should be sent
    Given I am logged in as user "organizer+lausanne"
    When I fill the Add Event form "accueil-cafe" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-submit"
    And 0 mails should be sent

  @api @preserveDatabase
  Scenario: When creating an Event, the values should be alterd & stored in the database
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe"
    Then I should see 2 ".card-list-item" elements
    When I fill the Add Event form "accueil-cafe" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-submit"
    When I am on "/lausanne/activities/accueil-cafe"
    Then I should see 3 ".card-list-item" elements
