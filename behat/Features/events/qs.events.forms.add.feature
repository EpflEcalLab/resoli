Feature: Add Event Form

  @api @preserveDatabase
  Scenario: When creating an Event, the values should be saved & stored in the database
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe"
    Then I should see 2 ".card-list-item" elements
    When I fill the Add Event form "accueil-cafe" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save"
    When I am on "/lausanne/activities/accueil-cafe"
    Then I should see 3 ".card-list-item" elements

  @api @preserveDatabase
  Scenario: When creating an Event, on save we should be redirected on the Activity's events page
    Given I am logged in as user "organizer+lausanne"
    When I fill the Add Event form "accueil-cafe" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save"
    Then the url should match "/fr/lausanne/activities/accueil-cafe"

  @api @preserveDatabase @mail
  Scenario: When creating an Event, as organizer only, no mail should be sent and the user should not be subscribed to the event
    Given I am logged in as user "organizer+lausanne"
    When I fill the Add Event form "sorties-theatre" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save"
    And 0 mail should be sent
    Then I should not see a "#card64[data-status='confirmed']" element
    Then I should see a "#card64[data-status='default']" element

  @api @preserveDatabase @mail
  Scenario: When creating an Event, as organizer & co-organizer, no mail should be sent and the user should not be subscribed to the event
    Given I am logged in as user "organizer+lausanne"
    When I fill the Add Event form "accueil-cafe" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save"
    And 0 mail should be sent
    Then I should not see a "#card64[data-status='confirmed']" element
    Then I should see a "#card64[data-status='default']" element

  @api @preserveDatabase @mail
  Scenario: When creating an Event, as co-organizer only, no mail should be sent but the user should be subscribed to the event
    Given I am logged in as user "manager+lausanne"
    When I fill the Add Event form "massages" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save"
    And 0 mail should be sent
    Then I should see a "#card64[data-status='confirmed']" element
    Then I should not see a "#card64[data-status='default']" element
