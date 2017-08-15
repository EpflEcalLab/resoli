Feature: Activitiy Access
  In order to make sure ACL is working for activities
  As a bunch of users
  I want to make sure the access & accessbypass are working like a charm

  @api
  Scenario: Logged as Member of Lausanne, I can't access to Fribourg activities
    Given I am on "/user/login"
    When I fill in "edit-name" with "member+lausanne@antistatique.net"
    Then I fill in "edit-pass" with "member+lausanne"
    And I press "edit-submit"
    When I am on "/activities/2/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Manager of Lausanne, I can't access to Fribourg activities
    Given I am on "/user/login"
    When I fill in "edit-name" with "manager+lausanne@antistatique.net"
    Then I fill in "edit-pass" with "manager+lausanne"
    And I press "edit-submit"
    When I am on "/activities/2/theme"
    And the response status code should be 403

  @api
  Scenario: Logged as Organizer of Lausanne, I can't access to Fribourg activities
    Given I am on "/user/login"
    When I fill in "edit-name" with "organizer+lausanne@antistatique.net"
    Then I fill in "edit-pass" with "organizer+lausanne"
    And I press "edit-submit"
    When I am on "/activities/2/theme"
    And the response status code should be 403
