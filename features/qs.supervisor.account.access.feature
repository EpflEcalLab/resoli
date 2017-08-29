Feature: Supervisor - Redirect
  In order to make sure Supervisor - Redirection are working
  As a bunch of users
  I want to make sure the redirections bring on the correct pages

# Dashboard
  @api
  Scenario: Logged a Member of Lausanne, I can access my account dashboard
    Given I am logged in as user "member+lausanne"
    Then I am on "/account/2/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged a Member of Lausanne, I can't access the account dashboard of another user
    Given I am logged in as user "member+lausanne"
    Then I am on "/account/1/dashboard"
    And the response status code should be 403

  @api
  Scenario: Logged a Admin, I can access my account dashboard
        Given I am logged in as user "admin"
    Then I am on "/account/1/dashboard"
    And the response status code should be 200

  @api
  Scenario: Logged a Admin, I can access to any account dashboard
        Given I am logged in as user "admin"
    Then I am on "/account/2/dashboard"
    And the response status code should be 200

# User edit form
  @api
  Scenario: Logged a Member of Lausanne, I can access my account edit form
    Given I am logged in as user "member+lausanne"
    Then I am on "/account/2/edit"
    And the response status code should be 200

  @api
  Scenario: Logged a Member of Lausanne, I can't access the account edit form of another user
    Given I am logged in as user "member+lausanne"
    Then I am on "/account/1/edit"
    And the response status code should be 403

  @api
  Scenario: Logged a Admin, I can access my account edit form
        Given I am logged in as user "admin"
    Then I am on "/account/1/edit"
    And the response status code should be 200

  @api
  Scenario: Logged a Admin, I can access to any account edit form
        Given I am logged in as user "admin"
    Then I am on "/account/2/edit"
    And the response status code should be 200
