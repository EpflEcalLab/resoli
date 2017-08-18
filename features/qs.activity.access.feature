Feature: Activitiy Access
  In order to make sure ACL is working for activities
  As a bunch of users
  I want to make sure the access & access bypass are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "member+lausanne"
    When I am on "/activities/fribourg/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "manager+lausanne"
    When I am on "/activities/fribourg/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg activities
    Given I am logged in as user "organizer+lausanne"
    When I am on "/activities/fribourg/theme"
    And the response status code should be 403
