
Feature: Sharing by Offer Access
  Asserts the listing of Offer by User display the correct number of items.

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, When I access "My offers" in Lausanne
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/offers/8"
    And the response status code should be 200
    Then I should see 3 ".card-list .card-list-item" elements
    Then I should see a "#offer71" element
    Then I should see a "#offer72" element
    Then I should see a "#offer75" element
    Then I should not see a "#offer73" element
    Then I should not see a "#offer74" element
    And I should see "qs.sharing.add_offer"
    And I should not see "qs.sharing.user.collection.empty"

  @api
  Scenario: Logged as Member of Lausanne & Organizer of Fribourg, When I access "My offers" in Fribourg
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/2/offers/2"
    And the response status code should be 200
    Then I should see 1 ".card-list .card-list-item" elements
    Then I should see a "#offer73" element
    Then I should not see a "#offer71" element
    Then I should not see a "#offer72" element
    Then I should not see a "#offer74" element
    Then I should not see a "#offer75" element
    And I should see "qs.sharing.add_offer"
    And I should not see "qs.sharing.user.collection.empty"

  @api
  Scenario: Logged as Member of Lausanne, When I access "My offers" in Lausanne
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/offers/2"
    And the response status code should be 200
    Then I should see 1 ".card-list .card-list-item" elements
    Then I should see a "#offer69" element
    Then I should not see a "#offer71" element
    Then I should not see a "#offer72" element
    Then I should not see a "#offer73" element
    Then I should not see a "#offer74" element
    Then I should not see a "#offer75" element
    And I should see "qs.sharing.add_offer"
    And I should not see "qs.sharing.user.collection.empty"

  @api
  Scenario: Logged as Member of Lausanne, When I access "My offers" in Fribourg
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/2/offers/2"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    Then I should not see a "#offer69" element
    Then I should not see a "#offer71" element
    Then I should not see a "#offer72" element
    Then I should not see a "#offer73" element
    Then I should not see a "#offer74" element
    Then I should not see a "#offer75" element
    And I should see "qs.sharing.add_offer"
    And I should see "qs.sharing.user.collection.empty"

  @api
  Scenario: Logged as Admin, When I access "My offers" in Lausanne
    Given I am logged in as user "admin"
    When I am on "/sharing/1/user/1"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    And I should see "qs.sharing.add_offer"
    And I should see "qs.sharing.user.collection.empty"

  @api
  Scenario: Logged as Admin, When I access "My offers" in Fribourg
    Given I am logged in as user "admin"
    When I am on "/sharing/2/user/1"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    And I should see "qs.sharing.add_offer"
    And I should see "qs.sharing.user.collection.empty"

  @api
  Scenario: Logged as Admin, When I access "My offers" in Genève
    Given I am logged in as user "admin"
    When I am on "/sharing/3/user/1"
    And the response status code should be 200
    Then I should see 0 ".card-list .card-list-item" elements
    And I should see "qs.sharing.add_offer"
    And I should see "qs.sharing.user.collection.empty"

