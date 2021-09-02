Feature: Sharing Offers add Form

## Floating Button
  @api
  Scenario: In the Sharing request form page, I don't see any floating button
    Given I am logged in as user "admin"
    When I am on "/sharing/1/offers/add"
    Then I should see 1 ".floating a" element
    And I should see "qs_sharing.add_offer" link with href "/sharing/1/offers/add"

# Back button.
  @api
  Scenario: In the Sharing request form page, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/sharing/1/offers/add"
    Then I should not see a "#block-previousnavigation a" element

# Form pre-filled values.

# Form submits.

