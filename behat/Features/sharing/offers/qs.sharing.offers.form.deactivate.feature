Feature: Offer Deactivate Form

  @api
  Scenario: When reaching the offer dashboard, I should see the deactivation form
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    Then I should see 2 "form.deactivate" elements

  # @todo This test should pass once the bug with the published -> archived worflow is fixed
  # @api @preserveDatabase
  # Scenario: When deactivating an Offer, it should works and redirect me on my offer dashboard page
    # Given I am logged in as user "member+lausanne+organizer+fribourg"
    # When I am on "/sharing/1/user/8/offers"
    # Then I should see 1 "form.offer71.deactivate" element
    # Then I follow the link ".offer71.deactivate button[type='submit']" element
    # Then the url should match "/sharing/1/user/8/offers"
    # And I should see "qs_sharing.offers.form.deactivate.success Co-Voiturage Lausanne-Fribourg" in the ".alert" element
    # And I should see 0 "form.offer71.deactivate" element
    # And I should see 1 "form.offer71.reactivate" element

  # @todo This test should NOT pass once the bug with the published -> archived worflow is fixed
  @api @preserveDatabase
  Scenario: When deactivating an Offer, it should works and redirect me on my offer dashboard page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    Then I should see 1 "form.offer71.deactivate" element
    Then I follow the link ".offer71.deactivate button[type='submit']" element
    Then the url should match "/sharing/1/user/8/offers"
    And I should see "qs_sharing.offers.form.deactivate.success Co-Voiturage Lausanne-Fribourg" in the ".alert" element
    And I should see 1 "form.offer71.deactivate" element
    And I should see 0 "form.offer71.reactivate" element
