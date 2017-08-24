Feature: Activitiy Access
  In order to make sure ACL is working for activities
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can access to Lausanne activities
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Login as user waiting approval of Lausanne, I can't access to Lausanne activities
    Given I am logged in as user "approval+lausanne"
    When I am on "/lausanne/activities/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "member+lausanne"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "manager+lausanne"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "organizer+lausanne"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg I can't access to Lausanne & Fribourg activities
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 200
    When I am on "/lausanne/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to Lausanne activities add form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Lausanne, I can access to Lausanne activities add form
    Given I am logged in as user "manager+lausanne"
    When I am on "/lausanne/activities/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities add form
    Given I am logged in as user "member+lausanne"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg activities add form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg activities add form
    Given I am logged in as user "manager+lausanne"
    When I am on "/fribourg/activities/add"
    And the response status code should be 403


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

