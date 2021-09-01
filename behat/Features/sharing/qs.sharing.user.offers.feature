
Feature: Offer User Collection
  Asserts the listing of Offer by User display the correct number of items.

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, When I access "My offers" in Lausanne
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    And the response status code should be 200
    Then I should see 3 ".card-list .card-list-item" elements
    Then I should see a "#offer71" element
    Then I should see a "#offer72" element
    Then I should see a "#offer75" element
    Then I should not see a "#offer73" element
    Then I should not see a "#offer74" element
    And I should see "qs_sharing.add_offer"
    And I should not see "qs_sharing.user.offers.collection.empty"

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, When I access "My offers" in Fribourg
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/2/user/2/offers"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    Then I should not see a "#offer73" element
    Then I should not see a "#offer71" element
    Then I should not see a "#offer72" element
    Then I should not see a "#offer74" element
    Then I should not see a "#offer75" element
    And I should see "qs_sharing.add_offer"
    And I should see "qs_sharing.user.offers.collection.empty"

  @api
  Scenario: Logged as Member of Lausanne, When I access "My offers" in Lausanne
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/user/2/offers"
    And the response status code should be 200
    Then I should see 1 ".card-list .card-list-item" elements
    Then I should see a "#offer69" element
    Then I should not see a "#offer71" element
    Then I should not see a "#offer72" element
    Then I should not see a "#offer73" element
    Then I should not see a "#offer74" element
    Then I should not see a "#offer75" element
    And I should see "qs_sharing.add_offer"
    And I should not see "qs_sharing.user.offers.collection.empty"

  @api
  Scenario: Logged as Member of Lausanne, When I access "My offers" in Fribourg
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/2/user/2/offers"
    And the response status code should be 403

  @api
  Scenario: Logged as Admin, When I access "My offers" in Lausanne
    Given I am logged in as user "admin"
    When I am on "/sharing/1/user/1/offers"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    And I should see "qs_sharing.add_offer"
    And I should see "qs_sharing.user.offers.collection.empty"

  @api
  Scenario: Logged as Admin, When I access "My offers" in Fribourg
    Given I am logged in as user "admin"
    When I am on "/sharing/2/user/1/offers"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    And I should see "qs_sharing.add_offer"
    And I should see "qs_sharing.user.offers.collection.empty"

  @api
  Scenario: Logged as Admin, When I access "My offers" in Genève
    Given I am logged in as user "admin"
    When I am on "/sharing/3/user/1/offers"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    And I should see "qs_sharing.add_offer"
    And I should see "qs_sharing.user.offers.collection.empty"

  @api
  Scenario Outline: As anonymous I should not be able to access any offers dashboard page.
    Given I am on "<url>"
    And the response status code should be 403

    Examples:
      | url |
      | /sharing/1/user/1/offers |
      | /sharing/2/user/1/offers |
      | /sharing/1/user/8/offers |
      | /sharing/2/user/8/offers |

  @api
  Scenario Outline: Logged-in, I can access my own offers dashboard page. Accessing other community or people page should be unauthorized.
    Given I am logged in as user "<user>"
    When I am on "/sharing/<community>/user/<user-id>/offers"
    Then the response status code should be <code>

    Examples:
      | user | user-id | community | code |
      | member+lausanne | 2 | 1  | 200 |
      | member+lausanne | 2 | 2  | 403 |
      | approval+lausanne | 3 | 1 | 403 |
      | approval+lausanne | 3 | 2 | 403 |
      | organizer+lausanne | 6 | 1 | 200 |
      | organizer+lausanne | 6 | 2 | 403 |
      | manager+lausanne | 5 | 1  | 200 |
      | manager+lausanne | 5 | 2  | 403 |
      | member+lausanne+manager+fribourg | 13 | 1  | 200 |
      | member+lausanne+manager+fribourg | 13 | 2  | 200 |
      | member+lausanne+manager+fribourg | 13 | 3  | 403 |
      | declined+organizer+lausanne | 17 | 1  | 403 |
      | declined+organizer+lausanne | 17 | 2  | 403 |
      | member+lausanne+declined+organizer+lausanne | 18 | 1  | 200 |
      | member+lausanne+declined+organizer+lausanne | 18 | 2  | 403 |
      | member+fribourg+declined+member+lausanne | 19 | 1  | 403 |
      | member+fribourg+declined+member+lausanne | 19 | 2  | 200 |
