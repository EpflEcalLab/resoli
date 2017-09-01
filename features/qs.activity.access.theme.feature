
Feature: Activities by Theme Access
  In order to make sure ACL is working for Activities by theme page
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
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg I can access to Lausanne & Fribourg activities
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/fribourg/activities/theme"
    And the response status code should be 200
    When I am on "/lausanne/activities/theme"
    And the response status code should be 200
