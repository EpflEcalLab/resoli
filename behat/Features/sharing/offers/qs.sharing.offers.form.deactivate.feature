Feature: Offer Deactivate Form

  @api @iris
  Scenario: When reaching the offer dashboard, I should see the deactivation form
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    Then I should see 1 "#qs-sharing-offer-deactivate-form" element
    Then I should see 1 "#qs-sharing-offer-deactivate-form--2" element

  @api @preserveDatabase @iris
  Scenario: When deactivating an Offer, it should works and redirect me on my offer dashboard page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    Then I follow the link "#qs-sharing-offer-deactivate-form button[type='submit']" element
    Then the url should match "/sharing/1/user/8/offers"
    And I should see "qs_sharing.offers.form.deactivate.success Co-Voiturage Lausanne-Fribourg" in the ".alert" element
