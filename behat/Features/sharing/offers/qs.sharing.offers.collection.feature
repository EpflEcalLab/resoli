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

  # @Todo assert count of items with actions once templated.

## Floating Button
  @api
  Scenario Outline: Logged-in on the "Transports et déplacements" listing, I should see the floating button point to my offers
    Given I am logged in as user "<user>"
    When I am on "/node/66?theme=19"
    Then I should see 1 ".floating a" elements
    And I should see "qs_sharing.floating.my_offers" link with href "/activities/1/user/<user-id>"
    Examples:
      | user | user-id |
      | member+lausanne | 2 |
      | manager+lausanne | 5 |
      | organizer+lausanne | 6 |
      | member+lausanne+manager+fribourg | 13 |

# Back button.
  @api
  Scenario: In the Sharing offers collection page, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/sharing/1/offers"
    Then I should not see a "#block-previousnavigation a" element
