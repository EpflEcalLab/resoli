Feature: Register Access

  Scenario: As anonymous, I should have access to the register form
    Given I am on "/authentication/register"
    And the response status code should be 200

  Scenario: As loggedin user, I should not have access to the register form
    Given I am logged in as user "admin"
    When I am on "/authentication/register"
    And the response status code should be 403
