Feature: Activitiy Forms Access
  In order to make sure ACL is working for activity forms
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

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
