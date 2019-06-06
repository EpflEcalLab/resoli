Feature: Account Edit

  @preserveDatabase
  Scenario: When I edit my account, the value should remaing in the database
    Given I am logged in as user "admin"
    And I fill the Account form with:
      | user | mail | firstname | lastname | phone |
      | 1 | behat+edited@antistatique.net | Firstname (edited) | Lastname (edited) | +01 234 56 78 (edited) |
    And I press "edit-submit"
    Then the url should match "/account/1/dashboard"
    And I should see "qs_supervisor.account.form.edit.success Firstname (edited), Lastname (edited), behat+edited@antistatique.net" in the ".alert" element
    Then I am on "/account/1/edit"
    And the "edit-mail" field should contain "behat+edited@antistatique.net"
    And the "edit-firstname" field should contain "Firstname (edited)"
    And the "edit-lastname" field should contain "Lastname (edited)"
    And the "edit-phone" field should contain "+01 234 56 78 (edited)"
