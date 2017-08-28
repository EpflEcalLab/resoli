Feature: Activitiy Detail Page Access
  In order to make sure ACL is working for activity detail page
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can access to the Activity N°2 (Activity 2 - Lausanne - Theme N°1)
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1"
    And the response status code should be 200

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to the Activity N°2 (Activity 2 - Lausanne - Theme N°1)
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can access to the Activity N°2 (Activity 2 - Lausanne - Theme N°1)
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/activity-2-lausanne-theme-ndeg1"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to the Activity N°11 (Activity 11 - Fribourg - Theme N°3)
    Given I am logged in as user "member+lausanne"
    When I am on "/fribourg/activities/activity-11-fribourg-theme-ndeg3"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to the Activity N°11 (Activity 11 - Fribourg - Theme N°3)
    Given I am logged in as user "organizer+lausanne"
    When I am on "/fribourg/activities/activity-11-fribourg-theme-ndeg3"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to the Activity N°11 (Activity 11 - Fribourg - Theme N°3)
    Given I am logged in as user "manager+lausanne"
    When I am on "/fribourg/activities/activity-11-fribourg-theme-ndeg3"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, when I access to an Event of Activity N°11 (Activity 11 - Fribourg - Theme N°3) I am redirected on the activity page & get an access denied
    Given I am logged in as user "member+lausanne"
    When I am on "/node/27"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, when I access to an Event of Activity N°11 (Activity 11 - Fribourg - Theme N°3) I am redirected on the activity page & get an access denied
    Given I am logged in as user "organizer+lausanne"
    When I am on "/node/27"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, when I access to an Event of Activity N°11 (Activity 11 - Fribourg - Theme N°3) I am redirected on the activity page & get an access denied
    Given I am logged in as user "manager+lausanne"
    When I am on "/node/27"
    And the response status code should be 403

