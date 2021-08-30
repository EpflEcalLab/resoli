Feature: Sharing by Offer Redirect
  Asserts the canonical access of any Offer redirect to the offer's type.

  @api
  Scenario: Accessing to an offer canonical view should redirect to it's offer's type.
    Given I am logged in as user "member+lausanne"
    When I am on "/node/70"
    And the url should match "node/67"
    And the response status code should be 200
