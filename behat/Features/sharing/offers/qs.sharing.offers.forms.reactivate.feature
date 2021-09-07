Feature: Offer Reactivate Form

  @api
  Scenario: When reaching the offer dashboard, I should see the reactivation form
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "sharing/1/user/8/offers"
    Then I should see 1 "#qs-sharing-offer-reactivate-form" element

  @api @preserveDatabase
  Scenario: When reactivating an Offer, it should works and redirect me on my offer dashboard page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    Then I follow the link "#qs-sharing-offer-reactivate-form button[type='submit']" element
    Then the url should match "/sharing/1/user/8/offers"
    And I should see "qs_sharing.offers.form.reactivate.success Discussion à l'achat d'un nouvelle ordinateur" in the ".alert" element
