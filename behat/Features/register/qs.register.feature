Feature: Register

  @preserveDatabase @mail
  Scenario: Registring should send a "Waiting approval" to the end-user
    Given I am on "/authentication/register"
    Then I select "1" from "edit-community-1"
    Then I fill in "edit-firstname" with "John"
    Then I fill in "edit-lastname" with "Doe"
    Then I fill in "edit-mail" with "john.doe@example.org"
    Then I fill in "edit-phone" with "+01 234 56 78"
    Then I fill in "edit-password" with "qwertz"
    Then I fill in "edit-password-verification" with "qwertz"
    And I press "edit-submit"
    Then the url should match "authentication/approval/1"
    And I should see "qs_auth.form.register.success John, Doe" in the ".alert" element
    Then I should see "qs.auth.approval.thanks"
    Then I should see "qs.auth.approval.description Lausanne"
    Then I should see "qs.auth.approval.short"
    Then A mail as been sent to "john.doe@example.org" with subject "qs.mail.welcome.no_approval.subject Resoli john.doe@example.org John Doe"
    Then A mail as been sent to "manager+lausanne@antistatique.net" with subject "qs.mail.community.apply.subject Resoli john.doe@example.org John Doe Lausanne"
