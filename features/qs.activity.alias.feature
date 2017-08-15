Feature: Activitiy Alias
  In order to make sure Alias is working for activities
  As a bunch of users
  I want to make sure the alias are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities
    Given I am on "/user/login"
    When I fill in "edit-name" with "member+lausanne@antistatique.net"
    Then I fill in "edit-pass" with "member+lausanne"
    And I press "edit-submit"
    When I am on "/activities/fribourg/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Member of Lausanne, I can access to Lausanne activities
    Given I am on "/user/login"
    When I fill in "edit-name" with "member+lausanne@antistatique.net"
    Then I fill in "edit-pass" with "member+lausanne"
    And I press "edit-submit"
    When I am on "/activities/lausanne/theme"
    And the response status code should be 200

