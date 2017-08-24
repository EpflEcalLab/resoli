Feature: Activitiy Alias
  In order to make sure Alias is working for activities
  As a bunch of users
  I want to make sure the alias are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "member+lausanne"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can access to Lausanne activities
    Given I am logged in as user "member+lausanne"
    When I am on "/lausanne/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Fribourg, I can access to Fribourg activities
    Given I am logged in as user "member+fribourg"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Logged as Manager of Genève, I can access to Genève activities
    Given I am logged in as user "member+fribourg+approval+lausanne+member+geneve"
    When I am on "/geneve/activities/theme"
    And the response status code should be 200

  @api
  Scenario: Logged as Organizer of Lausanne, I can access to Lausanne activities add form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/lausanne/activities/add"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, I can access to the Activity N°2 (Activity - Lausanne - Theme N°1)
    Given I am logged in as user "member+lausanne"
    When I am on "/node/2"
    And the url should match "lausanne/activities/activity-2-lausanne-theme-ndeg1"
    And the response status code should be 200

  @api
  Scenario: Logged as Member of Lausanne, when I access to an Event of Activity N°2 (Activity - Lausanne - Theme N°1) I am redirected on the activity page
    Given I am logged in as user "member+lausanne"
    When I am on "/node/15"
    And the url should match "lausanne/activities/activity-2-lausanne-theme-ndeg1"
    And the response status code should be 200
