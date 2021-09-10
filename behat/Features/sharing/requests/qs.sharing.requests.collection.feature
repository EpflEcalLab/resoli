@debug
Feature: Collection of Requests
  Asserts the listing of Requests of one community display the correct number of items.

## Redirect
  @api
  Scenario: Accessing to a request canonical view should redirect to the requests collection page.
    Given I am logged in as user "member+lausanne"
    When I am on "/node/77"
    And the url should match "/sharing/1/requests"
    And the response status code should be 200
