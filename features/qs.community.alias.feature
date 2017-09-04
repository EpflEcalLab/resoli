Feature: Community Alias
  In order to make sure Alias is working for communities
  As a bunch of users
  I want to make sure the alias are working like a charm

## Community - Dashboard
  @api
  Scenario: Logged as Manager of Lausanne, I can access to Lausanne dashboard
    Given I am logged in as user "manager+lausanne"
    When I am on "lausanne/dashboard"
    And the url should match "lausanne/dashboard"
    And the response status code should be 200

## Community - Members page
  @api
  Scenario: Logged as Manager of Lausanne, I can access to Lausanne list of members
    Given I am logged in as user "manager+lausanne"
    When I am on "lausanne/members"
    And the url should match "lausanne/members"
    And the response status code should be 200

## Community - Collection  of Accounts waiting for Approval
  @api
  Scenario: Logged as Manager of Lausanne, I can access to Lausanne list of accounts waiting for approval
    Given I am logged in as user "manager+lausanne"
    When I am on "lausanne/waiting-approval"
    And the url should match "lausanne/waiting-approval"
    And the response status code should be 200

