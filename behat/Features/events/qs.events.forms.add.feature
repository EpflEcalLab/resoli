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
  Scenario Outline: When creating an Event, as "organizer" or as "organizer & co-organizer", no mail should be sent and the user should not be subscribed to the event
    Given I am logged in as user "organizer+lausanne"
    When I fill the Add Event form "<event>" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save"
    And 0 mail should be sent
    Then I should not see a "#card64[data-status='confirmed']" element
    Then I should see a "#card64[data-status='default']" element
    Examples:
      | event |
      | sorties-theatre |
      | accueil-cafe |

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

  @api @preserveDatabase
  Scenario: When creating an weekly repeated Event, 12 events must be saved & stored in the database
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/accueil-cafe"
    Then I should see 2 ".card-list-item" elements
    When I fill the Add Event form "accueil-cafe" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save-and-repeat-weekly"
    When I am on "/lausanne/activities/accueil-cafe"
    Then I should see 14 ".card-list-item" elements

  @api @preserveDatabase
  Scenario: When creating an weekly repeated Event, on save we should be redirected on the Activity's events page
    Given I am logged in as user "organizer+lausanne"
    When I fill the Add Event form "accueil-cafe" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save-and-repeat-weekly"
    Then the url should match "/fr/lausanne/activities/accueil-cafe"

  @api @preserveDatabase @mail
  Scenario Outline: When creating an weekly repeated Event, as "organizer" or as "organizer & co-organizer", no mail should be sent and the user should not be subscribed to the event
    Given I am logged in as user "organizer+lausanne"
    When I fill the Add Event form "<event>" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save-and-repeat-weekly"
    And 0 mail should be sent
    Then I should not see a "#card64[data-status='confirmed']" element
    Then I should see a "#card64[data-status='default']" element
    Then I should not see a "#card65[data-status='confirmed']" element
    Then I should see a "#card65[data-status='default']" element
    Then I should not see a "#card66[data-status='confirmed']" element
    Then I should see a "#card66[data-status='default']" element
    Then I should not see a "#card67[data-status='confirmed']" element
    Then I should see a "#card67[data-status='default']" element
    Then I should not see a "#card68[data-status='confirmed']" element
    Then I should see a "#card68[data-status='default']" element
    Then I should not see a "#card69[data-status='confirmed']" element
    Then I should see a "#card69[data-status='default']" element
    Then I should not see a "#card70[data-status='confirmed']" element
    Then I should see a "#card70[data-status='default']" element
    Then I should not see a "#card71[data-status='confirmed']" element
    Then I should see a "#card71[data-status='default']" element
    Then I should not see a "#card72[data-status='confirmed']" element
    Then I should see a "#card72[data-status='default']" element
    Then I should not see a "#card73[data-status='confirmed']" element
    Then I should see a "#card73[data-status='default']" element
    Then I should not see a "#card74[data-status='confirmed']" element
    Then I should see a "#card74[data-status='default']" element
    Then I should not see a "#card75[data-status='confirmed']" element
    Then I should see a "#card75[data-status='default']" element
    Examples:
      | event |
      | sorties-theatre |
      | accueil-cafe |

  @api @preserveDatabase @mail
  Scenario: When creating an weekly repeated Event, as co-organizer only, no mail should be sent but the user should be subscribed to the event
    Given I am logged in as user "manager+lausanne"
    When I fill the Add Event form "massages" of "lausanne" with:
      | title | date | start-at | end-at | body | venue | contact-name | contact-phone | contact-mail | contribution |
      | Accueil Café | +2 days | 12:00 | 15:00 | Partagez un simple café entre habitants! | Antistatique | John Doe | +01 234 56 78 | john.doe@example.org | 25 CHF |
    And I press "edit-save-and-repeat-weekly"
    And 0 mail should be sent
    Then I should see a "#card64[data-status='confirmed']" element
    Then I should not see a "#card64[data-status='default']" element
    Then I should see a "#card65[data-status='confirmed']" element
    Then I should not see a "#card65[data-status='default']" element
    Then I should see a "#card66[data-status='confirmed']" element
    Then I should not see a "#card66[data-status='default']" element
    Then I should see a "#card67[data-status='confirmed']" element
    Then I should not see a "#card67[data-status='default']" element
    Then I should see a "#card68[data-status='confirmed']" element
    Then I should not see a "#card68[data-status='default']" element
    Then I should see a "#card69[data-status='confirmed']" element
    Then I should not see a "#card69[data-status='default']" element
    Then I should see a "#card70[data-status='confirmed']" element
    Then I should not see a "#card70[data-status='default']" element
    Then I should see a "#card71[data-status='confirmed']" element
    Then I should not see a "#card71[data-status='default']" element
    Then I should see a "#card72[data-status='confirmed']" element
    Then I should not see a "#card72[data-status='default']" element
    Then I should see a "#card73[data-status='confirmed']" element
    Then I should not see a "#card73[data-status='default']" element
    Then I should see a "#card74[data-status='confirmed']" element
    Then I should not see a "#card74[data-status='default']" element
    Then I should see a "#card75[data-status='confirmed']" element
    Then I should not see a "#card75[data-status='default']" element
