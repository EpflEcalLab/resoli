Feature: Offer Reactivate Form

  @api
  Scenario: When reaching the offer dashboard, I should see the reactivation form
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/users/8/offers"
    Then I should see 1 "#qs-sharing-offer-reactivate-form" elements

  @api @preserveDatabase
  Scenario: When reactivating an Offer, it should works and redirect me on my offer dashboard page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/users/8/offers"
    And I press "#collapse-75 #qs-sharing-offer-reactivate-form #edit-submit"
    Then the url should match "/sharing/1/user/8/offers"
    And I should see "qs_sharing.offers.form.reactivate.success Discussion à l'achat d'un nouvelle ordinateur" in the ".alert" element
