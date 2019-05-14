Feature: Community Approval

  @api @preserveDatabase @mail
  Scenario: Approving a new member should send him a mail
    Given I am logged in as user "manager+lausanne"
    When I am on "lausanne/dashboard/waiting-approval"
    And the response status code should be 200
    And I follow the link "#qs-acl-privilege-confirm-36 button" element
    And 1 mails should be sent
    Then A mail as been sent to "approval+all@antistatique.net" with subject "qs.mail.community.waiting_approval.confirm.subject Resoli approval+all@antistatique.net Nordine Ateur Lausanne"

  @api @preserveDatabase @mail
  Scenario: Declining a new member should send him a mail
    Given I am logged in as user "manager+lausanne"
    When I am on "lausanne/dashboard/waiting-approval"
    And the response status code should be 200
    And I follow the link "#qs-acl-privilege-decline-36 button" element
    And 1 mails should be sent
    Then A mail as been sent to "approval+all@antistatique.net" with subject "qs.mail.community.waiting_approval.decline.subject Resoli approval+all@antistatique.net Nordine Ateur Lausanne"
