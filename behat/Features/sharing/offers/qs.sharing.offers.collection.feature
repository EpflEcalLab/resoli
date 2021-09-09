Feature: Collection of Offers
  Asserts the listing of Offers of one Offer's type on a specific Theme display the correct number of items.

## Access
  @api
  Scenario Outline: As anonymous I should not be able to access any community offers collection.
    Given I am on "<url>"
    And the response status code should be 403
    Examples:
      | url |
      | /node/65 |
      | /node/65?theme=21 |
      | /node/66 |
      | /node/66?theme=19 |
      | /node/64?theme=19 |
      | /node/67?theme=20 |
      | /node/64?theme=21 |
      | /node/67?theme=23 |

  @api
  Scenario Outline: Logged-in, I can access my own community(ies) offers collection. Accessing community in which I don't belongs should not be unauthorized.
    Given I am logged in as user "<user>"
    When I am on "<url>"
    Then the response status code should be <code>
    Examples:
      | user | url | code |
      | admin | /node/66 | 200 |
      | admin | /node/66?theme=19 | 200 |
      | admin | /node/64?theme=19 | 200 |
      | admin | /node/67?theme=20 | 200 |
      | admin | /node/64?theme=21 | 200 |
      | admin | /node/67?theme=23 | 200 |
      | admin | /node/65 | 200 |
      | admin | /node/65?theme=21 | 200 |
      | member+lausanne | /node/66 | 200 |
      | member+lausanne | /node/66?theme=19 | 200 |
      | member+lausanne | /node/64?theme=19 | 200 |
      | member+lausanne | /node/67?theme=20 | 200 |
      | member+lausanne | /node/64?theme=21 | 200 |
      | member+lausanne | /node/67?theme=23 | 200 |
      | member+lausanne | /node/65 | 403 |
      | member+lausanne | /node/65?theme=21 | 403 |
      | approval+lausanne | /node/66 | 403 |
      | approval+lausanne | /node/66?theme=19 | 403 |
      | approval+lausanne | /node/64?theme=19 | 403 |
      | approval+lausanne | /node/67?theme=20 | 403 |
      | approval+lausanne | /node/64?theme=21 | 403 |
      | approval+lausanne | /node/67?theme=23 | 403 |
      | approval+lausanne | /node/65 | 403 |
      | approval+lausanne | /node/65?theme=21 | 403 |
      | manager+lausanne | /node/66 | 200 |
      | manager+lausanne | /node/66?theme=19 | 200 |
      | manager+lausanne | /node/64?theme=19 | 200 |
      | manager+lausanne | /node/67?theme=20 | 200 |
      | manager+lausanne | /node/64?theme=21 | 200 |
      | manager+lausanne | /node/67?theme=23 | 200 |
      | manager+lausanne | /node/65 | 403 |
      | manager+lausanne | /node/65?theme=21 | 403 |
      | member+lausanne+organizer+fribourg | /node/66 | 200 |
      | member+lausanne+organizer+fribourg | /node/66?theme=19 | 200 |
      | member+lausanne+organizer+fribourg | /node/64?theme=19 | 200 |
      | member+lausanne+organizer+fribourg | /node/67?theme=20 | 200 |
      | member+lausanne+organizer+fribourg | /node/64?theme=21 | 200 |
      | member+lausanne+organizer+fribourg | /node/67?theme=23 | 200 |
      | member+lausanne+organizer+fribourg | /node/65 | 200 |
      | member+lausanne+organizer+fribourg | /node/65?theme=21 | 200 |
      | member+fribourg+approval+organizer+fribourg | /node/66 | 403 |
      | member+fribourg+approval+organizer+fribourg | /node/66?theme=19 | 403 |
      | member+fribourg+approval+organizer+fribourg | /node/64?theme=19 | 403 |
      | member+fribourg+approval+organizer+fribourg | /node/67?theme=20 | 403 |
      | member+fribourg+approval+organizer+fribourg | /node/64?theme=21 | 403 |
      | member+fribourg+approval+organizer+fribourg | /node/67?theme=23 | 403 |
      | member+fribourg+approval+organizer+fribourg | /node/65 | 200 |
      | member+fribourg+approval+organizer+fribourg | /node/65?theme=21 | 200 |
      | declined+organizer+lausanne | /node/66 | 403 |
      | declined+organizer+lausanne | /node/66?theme=19 | 403 |
      | declined+organizer+lausanne | /node/64?theme=19 | 403 |
      | declined+organizer+lausanne | /node/67?theme=20 | 403 |
      | declined+organizer+lausanne | /node/64?theme=21 | 403 |
      | declined+organizer+lausanne | /node/67?theme=23 | 403 |
      | declined+organizer+lausanne | /node/65 | 403 |
      | declined+organizer+lausanne | /node/65?theme=21 | 403 |
      | member+lausanne+declined+organizer+lausanne | /node/66 | 200 |
      | member+lausanne+declined+organizer+lausanne | /node/66?theme=19 | 200 |
      | member+lausanne+declined+organizer+lausanne | /node/64?theme=19 | 200 |
      | member+lausanne+declined+organizer+lausanne | /node/67?theme=20 | 200 |
      | member+lausanne+declined+organizer+lausanne | /node/64?theme=21 | 200 |
      | member+lausanne+declined+organizer+lausanne | /node/67?theme=23 | 200 |
      | member+lausanne+declined+organizer+lausanne | /node/65 | 403 |
      | member+lausanne+declined+organizer+lausanne | /node/65?theme=21 | 403 |
      | member+fribourg+declined+member+lausanne | /node/66 | 403 |
      | member+fribourg+declined+member+lausanne | /node/66?theme=19 | 403 |
      | member+fribourg+declined+member+lausanne | /node/64?theme=19 | 403 |
      | member+fribourg+declined+member+lausanne | /node/67?theme=20 | 403 |
      | member+fribourg+declined+member+lausanne | /node/64?theme=21 | 403 |
      | member+fribourg+declined+member+lausanne | /node/67?theme=23 | 403 |
      | member+fribourg+declined+member+lausanne | /node/65 | 200 |
      | member+fribourg+declined+member+lausanne | /node/65?theme=21 | 200 |

## Element listed
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

## Floating Button
  @api
  Scenario Outline: Logged-in on the "Transports et déplacements" listing, I should see the floating button point to my dashboard
    Given I am logged in as user "<user>"
    When I am on "/node/66?theme=19"
    Then I should see 1 ".floating a" elements
    And I should see "qs_sharing.floating.dashboard" link with href "/sharing/1/user/<user-id>/dashboard"
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
