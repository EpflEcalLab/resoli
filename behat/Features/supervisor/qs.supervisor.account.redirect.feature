Feature: Supervisor - Redirect
  In order to make sure Supervisor - Redirection are working
  As a bunch of users
  I want to make sure the redirection bring on the correct pages

  @api
  Scenario: Logged a Member of Lausanne, accessing my root Drupal system user page brings me on my account dashboard
    Given I am logged in as user "member+lausanne"
    Then I am on "/user"
    And the url should match "/account/2/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged a Member of Lausanne, accessing my Drupal system user page brings me on my account dashboard
    Given I am logged in as user "member+lausanne"
    Then I am on "/user/2"
    And the url should match "/account/2/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged a Member of Lausanne, accessing another Drupal system user page brings me on page 403
    Given I am logged in as user "member+lausanne"
    Then I am on "/user/1"
    And the response status code should be 403

  @api
  Scenario: Logged a Admin, accessing my Drupal system user page brings me on my account dashboard
    Given I am logged in as user "admin"
    Then I am on "/user/1"
    And the url should match "/account/1/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged a Admin, accessing another Drupal system user page brings me on the account dashboard
    Given I am logged in as user "admin"
    Then I am on "/user/2"
    And the url should match "/account/2/dashboard"
    And the response status code should be 200
