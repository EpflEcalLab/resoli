Feature: Offer Deactivate Form

  @api
  Scenario: When reaching the offer dashboard, I should see the deactivation form
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/users/8/offers"
    Then I should see 2 "#qs-sharing-offer-deactivate-form" elements

  @api @preserveDatabase
  Scenario: When deactivating an Offer, it should works and redirect me on my offer dashboard page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/users/8/offers"
    And I press "#collapse-71 #qs-sharing-offer-deactivate-form #edit-submit"
    Then the url should match "/sharing/1/user/8/offers"
    And I should see "qs_sharing.offers.form.deactivate.success Co-Voiturage Lausanne-Fribourg" in the ".alert" element
