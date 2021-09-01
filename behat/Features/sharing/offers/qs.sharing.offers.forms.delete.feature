Feature: Offer Delete Form

  @api
  Scenario: When reaching the Delete form of Offer, I should see the confirmation form
    Given I am logged in as user "organizer+lausanne"
    When I am on "/sharing/76/delete"
    Then I should see "qs_sharing.offers.form.delete.warning"
    And I should see "qs.form.cancel" link with href "/sharing/1/user/6/offers"
    Then I should see 1 "button[type=submit]" element

  @api @preserveDatabase @mail
  Scenario: When deleting an Offer, it should works and redirect me on my offer dashboard page
    Given I am logged in as user "admin"
    When I am on "/sharing/72/delete"
    And I press "edit-submit"
    Then the url should match "/sharing/1/user/1/offers"
    And I should see "qs_sharing.offers.form.delete.success @offer Aide pour porter les courses" in the ".alert" element
