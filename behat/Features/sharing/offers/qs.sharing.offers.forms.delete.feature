Feature: Offer Delete Form

  @api
  Scenario: When reaching the offer dashboard, I should see the delete form
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/users/8/offers"
    Then I should see 1 "#qs-sharing-offer-delete-form" elements

  @api @preserveDatabase
  Scenario: When deleting an Offer, it should works and redirect me on my offer dashboard page
    Given I am logged in as user "member+lausanne+organizer+fribourg"
    When I am on "/sharing/1/users/8/offers"
    And I press "#collapse-75 #qs-sharing-offer-delete-form #edit-submit"
    Then the url should match "/sharing/1/user/8/offers"
    And I should see "qs_sharing.offers.form.delete.success Discussion à l'achat d'un nouvelle ordinateur" in the ".alert" element
    And I should not see "#collapse-75" in the ".card-list-offer" element
