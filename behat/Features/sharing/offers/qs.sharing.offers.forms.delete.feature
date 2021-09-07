Feature: Offer Delete Form

  @api
  Scenario: When reaching the offer dashboard, I should see the delete form
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    Then I should see 1 "form.delete" element

  @api @preserveDatabase
  Scenario: When deleting an Offer, it should works and redirect me on my offer dashboard page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/user/8/offers"
    Then I should see 1 "form.offer75.delete" element
    Then I follow the link ".offer75.delete button[type='submit']" element
    Then the url should match "/sharing/1/user/8/offers"
    And I should see "qs_sharing.offers.form.delete.success Discussion à l'achat d'un nouvelle ordinateur" in the ".alert" element
    And I should see 0 "form.offer75.delete" element
