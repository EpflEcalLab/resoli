Feature: Badges
  In order to make sure Badges are working for event pills & activities
  As a bunch of users
  I want to make sure thex are shown like a charm

## Activity Detail Page
  @api
  Scenario: Logged as Manager of Lausanne, I can see "register" button in the Events of the Activity N°2 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#event17" element
    Then I should not see a "#event17 .flag" element

  @api
  Scenario: Logged as Manager of Lausanne, I can see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1"
    And the response status code should be 200
    Then I should see 3 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    Then I should see a "#event37 .flag .flag-subscription-wait" element
    Then I should see a "#collapse-35" element
    Then I should not see a "#event35 .flag" element
    Then I should see a "#collapse-36" element
    Then I should see a "#event36 .flag .flag-subscription-confirmed" element

  @api
  Scenario: Logged as Organizer of Lausanne, I can't see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a not member of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1"
    And the response status code should be 200
    Then I should see 1 ".card-list-item" elements
    Then I should see a "#collapse-17" element
    Then I should not see a "#event17 .flag" element

  @api
  Scenario: Logged as Organizer of Lausanne, I can see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because I'm a member of this activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1"
    And the response status code should be 200
    Then I should see 3 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    Then I should see a "#event37 .flag .flag-subscription-confirmed" element
    Then I should see a "#collapse-35" element
    Then I should not see a "#event35 .flag" element
    Then I should see a "#collapse-36" element
    Then I should not see a "#event36 .flag" element

  @api
  Scenario: Logged as Organizer of Lausanne, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-4-lausanne-theme-ndeg1"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    Then I should not see a "#event22 .flag" element
    Then I should see a "#collapse-18" element
    Then I should not see a "#event18 .flag" element

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can see "register" button in the Events of the Activity N°4 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/activity-4-lausanne-theme-ndeg1"
    And the response status code should be 200
    Then I should see 2 ".card-list-item" elements
    Then I should see a "#collapse-22" element
    Then I should not see a "#event22 .flag" element
    Then I should see a "#collapse-18" element
    Then I should not see a "#event18 .flag" element


  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, I can't see "register" button in the Events of the Activity N°3 (Activity - Lausanne - Theme N°1), because this is a public activity
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/lausanne/activities/activity-3-lausanne-theme-ndeg1"
    And the response status code should be 200
    Then I should see 3 ".card-list-item" elements
    Then I should see a "#collapse-37" element
    Then I should not see a "#event37 .flag" element
    Then I should see a "#collapse-35" element
    Then I should not see a "#event35 .flag" element
    Then I should see a "#collapse-36" element
    Then I should not see a "#event36 .flag" element

## Events by Date

## Calendar by Month

## Calendar by Week


