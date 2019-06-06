Feature: Login
  In order to make sure Authentication - Login is working
  As a bunch of users
  I want to make sure the login is working like a charm

  @api
  Scenario: Login show message(s) on error & forget password link
    Given I am on "/user/login"
    When I fill in "edit-name" with "Batman"
    Then I fill in "edit-pass" with "RobinMyLove"
    And I press "edit-submit"
    And I should see "1 error has been found: qs_auth.form.login.name" in the ".alert" element
    And I should see "Unrecognized username or password. Forgot your password?" in the ".invalid-feedback" element
    And the response status code should be 200
