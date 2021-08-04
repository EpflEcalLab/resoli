Feature: Forget Password

  Scenario: As anonymous I should be able to acces the forget password form.
    Given I am on "/authentication/password"
    And the response status code should be 200
    And the "edit-name" field should contain ""

  Scenario: When passing the "name" GET parameter the forget password form should be prefilled .
    Given I am on "/authentication/password?name=batman"
    And the response status code should be 200
    And the "edit-name" field should contain "batman"

  Scenario: When filling the forget password with malformed email it should raise an error.
    Given I am on "/authentication/password"
    When I fill in "edit-name" with "Batman"
    And I press "edit-submit"
    And I should see "batman is not recognized as a username or an email address." in the ".alert" element

  @mail
  Scenario: When filling the forget password it should redirect me on the confirmation page & send the one-time-login mail.
    Given I am on "/authentication/password"
    When I fill in "edit-name" with "manager+lausanne@antistatique.net"
    And I press "edit-submit"
    Then I should see "qs.auth.pass.confirmation.thanks"
    Then I should see "qs.auth.pass.confirmation.description"
    And 1 mail should be sent
    Then A mail as been sent to "manager+lausanne@antistatique.net" with subject "qs.mail.pass.recovery.subject Resoli manager+lausanne@antistatique.net Juda Bricot"
