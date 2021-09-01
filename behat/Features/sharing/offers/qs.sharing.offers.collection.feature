Feature: Collection of Offers
  Asserts the listing of Offers of one Offer's type on a specific Theme display the correct number of items.

  @api
  Scenario: On any collection of offers listing, without given theme, it should display no offers.
    Given I am logged in as user "admin"
    When I am on "/node/66"
    And I should see "Transports et déplacements"
    And I should see "qs.sharing.no_offers"

  @api
  Scenario: On any collection of offers listing, with an none existing given theme, it should display no offers.
    Given I am logged in as user "admin"
    When I am on "/node/66?theme=foo"
    And I should see "Transports et déplacements"
    And I should see "qs.sharing.no_offers"

  @api
  Scenario: On the "Transports et déplacements" listing, with theme "Mobility", it should display 2 offers.
    Given I am logged in as user "admin"
    When I am on "/node/66?theme=19"
    And I should see "Transports et déplacements"
    And I should not see "qs.sharing.no_offers"
    Then I should see 2 "#offers-accordion .card-list-item" elements

  @api
  Scenario: On the "Transports et déplacements" listing, without given theme, it should display no offers.
    Given I am logged in as user "admin"
    When I am on "/node/66?theme=19"
    And I should see "Transports et déplacements"
    And I should not see "qs.sharing.no_offers"
  # @Todo assert count of items with actions once templated.


# Back button.
  @api
  Scenario: In the Sharing offers collection page, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/sharing/1/offers"
    Then I should not see a "#block-previousnavigation a" element
