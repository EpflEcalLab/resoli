Feature: Sharing by Offer's Type Access
  Asserts the listing of Offer's Type by Theme display the correct number of items and the
  corresponding volunteers per type.

  @api
  Scenario: On the Lausanne listing, I should see the correct elements and counts of offers.
    Given I am logged in as user "member+lausanne"
    When I am on "/sharing/1/offers"
    Then I should see 5 ".card-info" elements
    And I should see "qs_sharing.offer_type.volunteers.plural 2" in the "#card-offer-type66-theme19" element
    And I should see "qs_sharing.offer_type.volunteers 1" in the "#card-offer-type64-theme19" element
    And I should see "qs_sharing.offer_type.volunteers 1" in the "#card-offer-type67-theme20" element
    And I should see "qs_sharing.offer_type.volunteers 1" in the "#card-offer-type64-theme21" element
    And I should see "qs_sharing.offer_type.volunteers 1" in the "#card-offer-type67-theme23" element

  @api
  Scenario: On the Fribourg listing, I should see the correct elements and counts of offers.
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/2/offers"
    Then I should see 1 ".card-info" element
    And I should see "qs_sharing.offer_type.volunteers 1" in the "#card-offer-type65-theme21" element
