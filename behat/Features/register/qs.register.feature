Feature: Register

  @preserveDatabase @mail
  Scenario: When I create a new account, at the end I should be redirected to approval page on the applied community
    Given I fill the Register form with:
      | community | firstname | lastname | mail | phone | password |
      | 1 | John | Doe | john.doe@example.org | +01 234 56 78 | qwertz |
    And I press "edit-submit"
    Then the url should match "authentication/approval/1"
    And I should see "qs_auth.form.register.success John Doe john.doe@example.org" in the ".alert" element
    Then I should see "qs.auth.approval.thanks"
    Then I should see "qs.auth.approval.description Lausanne"
    Then I should see "qs.auth.approval.short"

  @preserveDatabase @mail
  Scenario: Registring a new user on Lausanne should send 2 mails:
    - One mail should welcome the new user
    - One mail should warn every manager(s) of Lausanne of this new appliance
    Given I fill the Register form with:
      | community | firstname | lastname | mail | phone | password |
      | 1 | John | Doe | john.doe@example.org | +01 234 56 78 | qwertz |
    And I press "edit-submit"
    And 2 mails should be sent
    Then A mail as been sent to "john.doe@example.org" with subject "qs.mail.welcome.no_approval.subject Resoli john.doe@example.org John Doe"
    Then A mail as been sent to "manager+lausanne@antistatique.net" with subject "qs.mail.community.apply.subject Resoli john.doe@example.org John Doe Lausanne"
