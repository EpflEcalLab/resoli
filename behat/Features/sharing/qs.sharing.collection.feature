Feature: Sharing by Offer's Type Access
  Asserts the listing of Offer's Type by Theme display the correct number of items and the
  corresponding volunteers per type.

  @api
  Scenario: On the Lausanne listing, I should see the correct elements and counts of offers.
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/offers"
    Then I should see 5 ".card-info" elements
    And I should see "qs.sharing.offer_type.volunteers.plural 2" in the "#card-offer-type71" element
    And I should see "qs.sharing.offer_type.volunteers 1" in the "#card-offer-type74" element
    And I should see "qs.sharing.offer_type.volunteers 1" in the "#card-offer-type69" element
    And I should see "qs.sharing.offer_type.volunteers 1" in the "#card-offer-type72" element
    And I should see "qs.sharing.offer_type.volunteers 1" in the "#card-offer-type70" element

  @api
  Scenario: On the Fribourg listing, I should see the correct elements and counts of offers.
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/2/offers"
    Then I should see 1 ".card-info" element
    And I should see "qs.sharing.offer_type.volunteers 1" in the "#card-offer-type73" element
