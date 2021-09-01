Feature: Sharing Request add Form

# Back button.
  @api
  Scenario: In the Sharing request form page, I don't see any back button
    Given I am logged in as user "admin"
    When I am on "/sharing/1/request/add"
    Then I should not see a "#block-previousnavigation a" element
