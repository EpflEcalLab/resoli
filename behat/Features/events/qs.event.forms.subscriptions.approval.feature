Feature: Event Subscriptions Approval

  @api @preserveDatabase @mail
  Scenario: Approving a new subscription should send some mails:
    - One to the requesting user
    - One for each Organizer & Manager of the activity's event
    Given I am logged in as user "admin"
    When I am on "lausanne/activities/sorties-theatre/events/macbeth/dashboard/waiting-approval"
    And the response status code should be 200
    And I follow the link "#qs-subscription-confirm-4 button" element
    And 3 mails should be sent
    Then A mail as been sent to "manager+lausanne@antistatique.net" with subject "qs.mail.subscription.waiting_approval.confirm_organizers.subject Resoli manager+lausanne@antistatique.net Juda Bricot Lausanne Macbeth Sorties Théâtre"
    Then A mail as been sent to "organizer+lausanne@antistatique.net" with subject "qs.mail.subscription.waiting_approval.confirm_organizers.subject Resoli manager+lausanne@antistatique.net Juda Bricot Lausanne Macbeth Sorties Théâtre"
    Then A mail as been sent to "manager+lausanne@antistatique.net" with subject "qs.mail.subscription.waiting_approval.confirm.subject Resoli manager+lausanne@antistatique.net Juda Bricot Lausanne Macbeth Sorties Théâtre"

  @api @preserveDatabase @mail
  Scenario: Declining a new subscription should send a mail to the requesting user only
    Given I am logged in as user "admin"
    When I am on "lausanne/activities/sorties-theatre/events/macbeth/dashboard/waiting-approval"
    And the response status code should be 200
    And I follow the link "#qs-subscription-decline-4 button" element
    And 1 mails should be sent
    Then A mail as been sent to "manager+lausanne@antistatique.net" with subject "qs.mail.subscription.waiting_approval.decline.subject Resoli manager+lausanne@antistatique.net Juda Bricot Lausanne Macbeth Sorties Théâtre"

  @api @preserveDatabase @mail
  Scenario: Rejecting an already approved subscription should send a mail to the user
    Given I am logged in as user "admin"
    When I am on "lausanne/activities/sorties-theatre/events/macbeth/dashboard/subscribers"
    And the response status code should be 200
    And I follow the link "#qs-subscription-decline-5 button" element
    And 1 mails should be sent
    Then A mail as been sent to "organizer+lausanne@antistatique.net" with subject "qs.mail.subscription.waiting_approval.decline.subject Resoli organizer+lausanne@antistatique.net Gerard Mensoif Lausanne Macbeth Sorties Théâtre"
